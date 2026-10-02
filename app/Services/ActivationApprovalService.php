<?php

namespace App\Services;

use App\Mail\ActivationApprovedMail;
use App\Mail\ActivationRejectedMail;
use App\Mail\InviteeApprovedMail;
use App\Models\Activation;
use App\Models\ActivationHistory;
use App\Models\Beneficiary;
use App\Models\Ledger;
use App\Models\MembershipFee;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ActivationApprovalService
{
    public const TYPE_REGULAR = 'regular';
    public const TYPE_SATELLITE = 'satellite';

    private const OV_RATES = [
        2 => 50.00,
        3 => 40.00,
        4 => 30.00,
        5 => 20.00,
        6 => 10.00,
    ];

    public function approve(
        Activation $activation,
        int $selectedUserID,
        string $type = self::TYPE_REGULAR,
        array $activationAttributes = []
    ): bool {
        $referralUser = $this->findReferralUser($activation);

        $gcashCF = $this->settingEnabled('gcash_convenience_fee_enabled');
        $paymayaCF = $this->settingEnabled('paymaya_convenience_fee_enabled');

        $totalMembershipFees = (float) MembershipFee::sum('Cost');
        $activatedUser = null;

        try {
            DB::transaction(function () use (
                $activation,
                $selectedUserID,
                $type,
                $referralUser,
                $gcashCF,
                $paymayaCF,
                $activationAttributes,
                &$activatedUser
            ) {
                Log::info('========== ACTIVATION APPROVAL START ==========', [
                    'ActivationID' => $activation->ActivationID,
                    'UserID' => $selectedUserID,
                    'type' => $type,
                    'upline_code' => $activation->upline_code,
                    'amount' => $activation->amount,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Lock Activation
                |--------------------------------------------------------------------------
                */
                $activation = Activation::where(
                    'ActivationID',
                    $activation->ActivationID
                )->lockForUpdate()->first();

                if (! $activation || $activation->status !== 'Pending') {
                    throw new \RuntimeException(
                        'Activation is no longer pending.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Approve Activation
                |--------------------------------------------------------------------------
                */
                $activation->update(array_merge(
                    ['status' => 'Approved'],
                    $activationAttributes
                ));

                /*
                |--------------------------------------------------------------------------
                | Approval History
                |--------------------------------------------------------------------------
                */
                $admin = Auth::user();

                $adminName = trim(implode(' ', array_filter([
                    $admin?->firstname,
                    $admin?->middlename,
                    $admin?->lastname,
                ])));

                ActivationHistory::create([
                    'ActivationID' => $activation->ActivationID,
                    'AdminID' => Auth::id(),
                    'status' => 'Approved',
                    'remarks' => "Activation approved by {$adminName}.",
                ]);

                /*
                |--------------------------------------------------------------------------
                | Activate User
                |--------------------------------------------------------------------------
                */
                User::where('UserID', $selectedUserID)->update([
                    'status' => 'Active',
                ]);

                $activatedUser = User::find($selectedUserID);

                /*
                |--------------------------------------------------------------------------
                | SATELLITE
                |--------------------------------------------------------------------------
                */
                if ($type === self::TYPE_SATELLITE) {
                    $this->processSatelliteAccounting(
                        $activation,
                        $selectedUserID,
                        $referralUser
                    );

                    Log::info('========== SATELLITE APPROVAL FINISHED ==========', [
                        'ActivationID' => $activation->ActivationID,
                    ]);

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | REGULAR - MEMBERSHIP FEES
                |--------------------------------------------------------------------------
                */
                foreach (MembershipFee::all() as $fee) {
                    $ledger = Ledger::create([
                        'RefID' => $fee->FeeID,
                        'amount' => $fee->Cost,
                        'Source' => 'MF',
                        'ActivationID' => $activation->ActivationID,
                        'status' => 'Completed',
                    ]);

                    Log::info('MF Ledger created.', [
                        'LedgerID' => $ledger->getKey(),
                        'FeeID' => $fee->FeeID,
                        'amount' => $fee->Cost,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | CONVENIENCE FEE
                |--------------------------------------------------------------------------
                */
                $paymentMethod = strtolower(
                    trim((string) $activation->payment_method)
                );

                if (
                    ($paymentMethod === 'gcash' && $gcashCF) ||
                    ($paymentMethod === 'paymaya' && $paymayaCF)
                ) {
                    $cfAmount = round(
                        MembershipFee::sum('Cost') * 0.01,
                        2
                    );

                    Ledger::create([
                        'RefID' => $selectedUserID,
                        'amount' => $cfAmount,
                        'Source' => 'CF',
                        'ActivationID' => $activation->ActivationID,
                        'status' => 'Completed',
                    ]);

                    Log::info('CF Ledger created.', [
                        'ActivationID' => $activation->ActivationID,
                        'amount' => $cfAmount,
                        'payment_method' => $paymentMethod,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | PF
                |--------------------------------------------------------------------------
                */
                if ($referralUser) {
                    $this->createWalletLedger(
                        $referralUser->UserID,
                        $activation->ActivationID,
                        50.00,
                        'PF',
                        -50.00
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | DR + OV
                |--------------------------------------------------------------------------
                */
                $this->processUplineCommissions(
                    $activation,
                    $referralUser
                );

                Log::info('========== REGULAR APPROVAL FINISHED ==========', [
                    'ActivationID' => $activation->ActivationID,
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('========== ACTIVATION APPROVAL FAILED ==========', [
                'ActivationID' => $activation->ActivationID ?? null,
                'UserID' => $selectedUserID,
                'type' => $type,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | EMAILS
        |--------------------------------------------------------------------------
        */
        if ($activatedUser?->email) {
            try {
                Mail::to($activatedUser->email)->send(
                    new ActivationApprovedMail(
                        $activatedUser,
                        $activation,
                        $totalMembershipFees
                    )
                );
            } catch (\Throwable $e) {
                Log::error('Failed to send member approval email.', [
                    'email' => $activatedUser->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($referralUser?->email && $activatedUser) {
            try {
                Mail::to($referralUser->email)->send(
                    new InviteeApprovedMail(
                        $referralUser,
                        $activatedUser,
                        $activation
                    )
                );
            } catch (\Throwable $e) {
                Log::error('Failed to send upline approval email.', [
                    'email' => $referralUser->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | FIND DIRECT REFERRAL
    |--------------------------------------------------------------------------
    */
    private function findReferralUser(Activation $activation): ?User
    {
        $code = trim((string) $activation->upline_code);

        if ($code === '') {
            Log::warning('No activation upline_code.', [
                'ActivationID' => $activation->ActivationID,
            ]);

            return null;
        }

        $user = User::where('referralCode', $code)->first();

        Log::info('Direct referral lookup.', [
            'ActivationID' => $activation->ActivationID,
            'upline_code' => $code,
            'found' => (bool) $user,
            'UserID' => $user?->UserID,
            'referralCode' => $user?->referralCode,
            'uplineCode' => $user?->uplineCode,
        ]);

        return $user;
    }

    /*
    |--------------------------------------------------------------------------
    | REGULAR + SATELLITE UPLINE COMMISSIONS
    |--------------------------------------------------------------------------
    */
    private function processUplineCommissions(
        Activation $activation,
        ?User $referralUser
    ): void {
        $drFee = (float) (
            MembershipFee::where(
                'FeeName',
                'Direct Referral'
            )->value('Cost') ?? 400.00
        );

        $drAmount = round($drFee * 0.90, 2);
        $txAmount = round($drFee * 0.10, 2);

        $code = trim((string) $activation->upline_code);
        $level = 1;

        Log::info('========== UPLINE TRAVERSAL START ==========', [
            'ActivationID' => $activation->ActivationID,
            'starting_upline_code' => $code,
            'DR' => $drAmount,
            'TX' => $txAmount,
        ]);

        while ($code !== '' && $level <= 6) {

            Log::info('Upline level lookup.', [
                'ActivationID' => $activation->ActivationID,
                'level' => $level,
                'search_referralCode' => $code,
            ]);

            $user = User::where('referralCode', $code)->first();

            if (! $user) {
                Log::warning('Upline NOT FOUND - traversal stopped.', [
                    'ActivationID' => $activation->ActivationID,
                    'level' => $level,
                    'search_referralCode' => $code,
                ]);

                break;
            }

            Log::info('Upline FOUND.', [
                'ActivationID' => $activation->ActivationID,
                'level' => $level,
                'UserID' => $user->UserID,
                'referralCode' => $user->referralCode,
                'uplineCode' => $user->uplineCode,
            ]);

            /*
            |--------------------------------------------------------------------------
            | LEVEL 1 = DR + TX
            |--------------------------------------------------------------------------
            */
            if ($level === 1) {

                Log::info('Processing Level 1 DR/TX.', [
                    'ActivationID' => $activation->ActivationID,
                    'UserID' => $user->UserID,
                    'DR' => $drAmount,
                    'TX' => $txAmount,
                ]);

                if ($drAmount > 0) {
                    $this->createWalletLedger(
                        $user->UserID,
                        $activation->ActivationID,
                        $drAmount,
                        'DR',
                        -$drAmount
                    );
                }

                if ($txAmount > 0) {
                    $ledger = Ledger::create([
                        'RefID' => $user->UserID,
                        'amount' => $txAmount,
                        'Source' => 'TX',
                        'ActivationID' => $activation->ActivationID,
                        'status' => 'Completed',
                    ]);

                    Log::info('TX Ledger created.', [
                        'LedgerID' => $ledger->getKey(),
                        'UserID' => $user->UserID,
                        'amount' => $txAmount,
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | LEVEL 2-6 = OV
            |--------------------------------------------------------------------------
            */
            if ($level >= 2) {

                $ovAmount = self::OV_RATES[$level] ?? 0;

                Log::info('OV LEVEL DETECTED.', [
                    'ActivationID' => $activation->ActivationID,
                    'level' => $level,
                    'UserID' => $user->UserID,
                    'referralCode' => $user->referralCode,
                    'uplineCode' => $user->uplineCode,
                    'OV' => $ovAmount,
                ]);

                if ($ovAmount <= 0) {
                    Log::warning('OV amount is zero.', [
                        'ActivationID' => $activation->ActivationID,
                        'level' => $level,
                    ]);
                } else {
                    $this->createOV(
                        $user->UserID,
                        $activation->ActivationID,
                        $level,
                        $ovAmount
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | NEXT UPLINE
            |--------------------------------------------------------------------------
            */
            $nextCode = trim((string) $user->uplineCode);

            Log::info('Moving to next upline.', [
                'ActivationID' => $activation->ActivationID,
                'current_level' => $level,
                'current_UserID' => $user->UserID,
                'current_referralCode' => $user->referralCode,
                'next_upline_code' => $nextCode,
            ]);

            $code = $nextCode;
            $level++;
        }

        if ($level > 6) {
            Log::info('OV traversal reached maximum Level 6.', [
                'ActivationID' => $activation->ActivationID,
            ]);
        }

        Log::info('========== UPLINE TRAVERSAL END ==========', [
            'ActivationID' => $activation->ActivationID,
            'last_level' => $level - 1,
            'remaining_code' => $code,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE OV WALLET + LEDGER
    |--------------------------------------------------------------------------
    */
    private function createOV(
        int $userID,
        int $activationID,
        int $level,
        float $amount
    ): void {
        Log::info('>>> OV WALLET INSERT START', [
            'UserID' => $userID,
            'ActivationID' => $activationID,
            'level' => $level,
            'amount' => $amount,
        ]);

        try {
            $wallet = Wallet::create([
                'UserID' => $userID,
                'ActivationID' => $activationID,
                'amount' => $amount,
                'source' => 'OV',
                'status' => 'Active',
            ]);

            Log::info('>>> OV WALLET INSERT SUCCESS', [
                'WalletID' => $wallet->getKey(),
                'UserID' => $userID,
                'ActivationID' => $activationID,
                'level' => $level,
                'amount' => $amount,
            ]);

            $ledger = Ledger::create([
                'RefID' => $userID,
                'amount' => -$amount,
                'Source' => 'OV',
                'ActivationID' => $activationID,
                'status' => 'Completed',
            ]);

            Log::info('>>> OV LEDGER INSERT SUCCESS', [
                'LedgerID' => $ledger->getKey(),
                'RefID' => $userID,
                'ActivationID' => $activationID,
                'level' => $level,
                'amount' => -$amount,
            ]);

        } catch (\Throwable $e) {

            Log::error('>>> OV INSERT FAILED', [
                'UserID' => $userID,
                'ActivationID' => $activationID,
                'level' => $level,
                'amount' => $amount,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | WALLET + LEDGER HELPER
    |--------------------------------------------------------------------------
    */
    private function createWalletLedger(
        int $userID,
        int $activationID,
        float $walletAmount,
        string $source,
        float $ledgerAmount
    ): void {
        $wallet = Wallet::create([
            'UserID' => $userID,
            'ActivationID' => $activationID,
            'amount' => $walletAmount,
            'source' => $source,
            'status' => 'Active',
        ]);

        Log::info('Wallet created.', [
            'WalletID' => $wallet->getKey(),
            'UserID' => $userID,
            'ActivationID' => $activationID,
            'source' => $source,
            'amount' => $walletAmount,
        ]);

        $ledger = Ledger::create([
            'RefID' => $userID,
            'amount' => $ledgerAmount,
            'Source' => $source,
            'ActivationID' => $activationID,
            'status' => 'Completed',
        ]);

        Log::info('Ledger created.', [
            'LedgerID' => $ledger->getKey(),
            'RefID' => $userID,
            'ActivationID' => $activationID,
            'Source' => $source,
            'amount' => $ledgerAmount,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SATELLITE ACCOUNTING
    |--------------------------------------------------------------------------
    */
    private function processSatelliteAccounting(
        Activation $activation,
        int $selectedUserID,
        ?User $referralUser
    ): void {
        $activationAmount = (float) $activation->amount;

        $sfAmount = round($activationAmount * 0.90, 2);
        $csAmount = round($activationAmount * 0.10, 2);

        Log::info('Satellite accounting.', [
            'ActivationID' => $activation->ActivationID,
            'activationAmount' => $activationAmount,
            'SF' => $sfAmount,
            'CS' => $csAmount,
        ]);

        /*
        |--------------------------------------------------------------------------
        | SF
        |--------------------------------------------------------------------------
        */
        if ($sfAmount > 0) {
            Ledger::create([
                'RefID' => $selectedUserID,
                'amount' => $sfAmount,
                'Source' => 'SF',
                'ActivationID' => $activation->ActivationID,
                'status' => 'Completed',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CS
        |--------------------------------------------------------------------------
        */
        if ($csAmount > 0) {
            Ledger::create([
                'RefID' => $selectedUserID,
                'amount' => -$csAmount,
                'Source' => 'CS',
                'ActivationID' => $activation->ActivationID,
                'status' => 'Completed',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | UPLINE DR / TX / OV / PF
        |--------------------------------------------------------------------------
        */
        if ($referralUser) {

            /*
            |--------------------------------------------------------------------------
            | CS WALLET
            |--------------------------------------------------------------------------
            */
            if ($csAmount > 0) {
                Wallet::create([
                    'UserID' => $referralUser->UserID,
                    'ActivationID' => $activation->ActivationID,
                    'amount' => $csAmount,
                    'source' => 'CS',
                    'status' => 'Active',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | DR + TX + OV
            |--------------------------------------------------------------------------
            */
            $this->processUplineCommissions(
                $activation,
                $referralUser
            );

            /*
            |--------------------------------------------------------------------------
            | PF
            |--------------------------------------------------------------------------
            */
            $this->createWalletLedger(
                $referralUser->UserID,
                $activation->ActivationID,
                50.00,
                'PF',
                -50.00
            );
        }
            }


    /*
    |--------------------------------------------------------------------------
    | SETTING
    |--------------------------------------------------------------------------
    */
    private function settingEnabled(string $name): bool
    {
        return filter_var(
            Setting::where('name', $name)->value('value'),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    */
    public function reject(
        Activation $activation,
        int $selectedUserID,
        string $remarks
    ): void {
        DB::transaction(function () use (
            $activation,
            $selectedUserID,
            $remarks
        ) {
            $activation->update([
                'status' => 'Rejected',
            ]);

            ActivationHistory::create([
                'ActivationID' => $activation->ActivationID,
                'AdminID' => Auth::id(),
                'status' => 'Rejected',
                'remarks' => $remarks,
            ]);

            User::where('UserID', $selectedUserID)->update([
                'status' => 'Inactive',
            ]);
        });

        $user = User::find($selectedUserID);

        if ($user?->email) {
            $beneficiaries = Beneficiary::where('UserID', $selectedUserID)
                ->where('status', true)
                ->orderBy('BeneficiaryID')
                ->get()
                ->toArray();

            try {
                Mail::to($user->email)->send(
                    new ActivationRejectedMail(
                        $user,
                        $activation,
                        $beneficiaries,
                        $remarks
                    )
                );
            } catch (\Throwable $e) {
                Log::error('Failed to send rejection email.', [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
