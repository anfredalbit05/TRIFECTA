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

/*
|--------------------------------------------------------------------------
| Activation Approval Service
|--------------------------------------------------------------------------
| Single source of truth for approving / rejecting a membership activation.
|
| Both existing review modals (regular ActivationReviewModal and the
| satellite SatelliteMembersActivationReview) historically held private
| copies of this logic, differing only in the commission shape:
|
|   • Regular: DR = 90% of the Direct Referral fee + a 10% TX ledger row
|     (TX is ledger-only, no wallet entry), plus policy/receipt numbers and
|     cashier snapshot recorded on approval.
|
|   • Satellite: DR = 100% of the Direct Referral fee, no TX split.
|
| Everything else (activation status, history row, user activation, MF
| breakdown, CF convenience fee, PF to the recruiter, levels 2-6 overrides,
| email notifications) is identical. The Transaction List tabs reuse this
| service so approvals initiated from either screen write the same records.
|--------------------------------------------------------------------------
*/

class ActivationApprovalService
{
    /** Commission flavors — change only the Level-1 DR treatment. */
    public const TYPE_REGULAR = 'regular';

    public const TYPE_SATELLITE = 'satellite';

    /**
     * Approve a pending activation.
     *
     * The caller resolves $activation / $selectedUserID (each screen has its
     * own loading rules) and handles any typed-confirmation UI; this method
     * performs the atomic writes + notifications and returns true on success,
     * false when the activation was no longer pending (or the member vanished).
     */
    public function approve(
        Activation $activation,
        int $selectedUserID,
        string $type = self::TYPE_REGULAR,
        array $activationAttributes = []
    ): bool {
        // =====================================================
        // LOAD SHARED RELATION DATA
        // =====================================================

        $referralUser = null;

        if (filled($activation->upline_code)) {
            $referralUser = User::where(
                'referralCode',
                trim((string) $activation->upline_code)
            )->first();
        }

        $beneficiaries = Beneficiary::where('UserID', $selectedUserID)
            ->where('status', true)
            ->orderBy('BeneficiaryID')
            ->get()
            ->toArray();

        $gcashConvenienceFeeEnabled = filter_var(
            Setting::where('name', 'gcash_convenience_fee_enabled')->value('value'),
            FILTER_VALIDATE_BOOLEAN
        );

        $paymayaConvenienceFeeEnabled = filter_var(
            Setting::where('name', 'paymaya_convenience_fee_enabled')->value('value'),
            FILTER_VALIDATE_BOOLEAN
        );

        $totalMembershipFeesCost = (float) MembershipFee::sum('Cost');

        /** @var User|null $activatedUser */
        $activatedUser = null;

        // =====================================================
        // ATOMIC DATABASE OPERATIONS
        // =====================================================

        try {
            DB::transaction(function () use (
                $activation,
                $selectedUserID,
                $type,
                $referralUser,
                $gcashConvenienceFeeEnabled,
                $paymayaConvenienceFeeEnabled,
                $activationAttributes,
                &$activatedUser
            ) {
                Log::info('Starting approval transaction for ActivationID: '.$activation->ActivationID);

                /*
                | Re-check pending status inside the row lock so concurrent
                | approvals (modal vs transaction list) can never double-pay
                | commissions.
                */
                $fresh = Activation::where('ActivationID', $activation->ActivationID)
                    ->lockForUpdate()
                    ->first();

                if (! $fresh || $fresh->status !== 'Pending') {
                    throw new \RuntimeException('Activation is no longer pending.');
                }

                $activation = $fresh;

                // Update Activation Status (+ any extra attributes the
                // caller stamps at approval time — policy/receipt/cashier)
                $activation->update(array_merge([
                    'status' => 'Approved',
                ], $activationAttributes));

                // Build Admin Name for Log History
                $admin = Auth::user();
                $adminName = trim(implode(' ', array_filter([
                    $admin->firstname ?? '',
                    $admin->middlename ?? '',
                    $admin->lastname ?? '',
                ])));

                // Log Approval History
                ActivationHistory::create([
                    'ActivationID' => $activation->ActivationID,
                    'AdminID' => Auth::id(),
                    'status' => 'Approved',
                    'remarks' => "Activation approved by {$adminName}.",
                ]);

                // Activate User Account
                User::where('UserID', $selectedUserID)->update([
                    'status' => 'Active',
                ]);

                $activatedUser = User::find($selectedUserID);

                /*
                |--------------------------------------------------------------------------
                | Membership Fees (MF) Ledger Breakdown
                |--------------------------------------------------------------------------
                */
                $membershipFees = MembershipFee::all();

                foreach ($membershipFees as $fee) {
                    Ledger::create([
                        'RefID' => $fee->FeeID,
                        'amount' => $fee->Cost,
                        'Source' => 'MF',
                        'ActivationID' => $activation->ActivationID,
                        'status' => 'Completed',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | GCash / Paymaya Convenience Fee (CF)
                |--------------------------------------------------------------------------
                | Only applies when payment_method is Gcash or Paymaya and the
                | corresponding setting is enabled. CF = 1% of Total Membership Cost.
                |--------------------------------------------------------------------------
                */

                $paymentMethod = strtolower(trim((string) $activation->payment_method));

                if (
                    ($paymentMethod === 'gcash' && $gcashConvenienceFeeEnabled)
                    || ($paymentMethod === 'paymaya' && $paymayaConvenienceFeeEnabled)
                ) {
                    $totalAmount = (float) MembershipFee::sum('Cost');
                    $cfAmount = round($totalAmount * 0.01, 2);

                    Ledger::create([
                        'RefID' => $selectedUserID,
                        'amount' => $cfAmount,
                        'Source' => 'CF',
                        'ActivationID' => $activation->ActivationID,
                        'status' => 'Completed',
                    ]);

                    Log::info('GCash | Paymaya CF ledger entry created.', [
                        'UserID' => $selectedUserID,
                        'ActivationID' => $activation->ActivationID,
                        'payment_method' => $activation->payment_method,
                        'amount' => $cfAmount,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Provident Fund (PF: 50.00) to Direct Recruiter / Upline
                |--------------------------------------------------------------------------
                */
                if ($referralUser) {
                    Wallet::create([
                        'UserID' => $referralUser->UserID,
                        'ActivationID' => $activation->ActivationID,
                        'amount' => 50.00,
                        'source' => 'PF',
                        'status' => 'Active',
                    ]);

                    Ledger::create([
                        'RefID' => $referralUser->UserID,
                        'amount' => -50.00,
                        'Source' => 'PF',
                        'ActivationID' => $activation->ActivationID,
                        'status' => 'Completed',
                    ]);

                    Log::info("PF entry (50.00) written for referral UserID: {$referralUser->UserID}");
                }

                /*
                |--------------------------------------------------------------------------
                | Multi-Tier Commission Hierarchy
                |--------------------------------------------------------------------------
                | Regular flavor: Level 1 DR receives 90% of the Direct Referral
                | fee, the remaining 10% is a TX ledger-only row.
                | Satellite flavor: Level 1 DR receives 100% of the fee.
                | Levels 2-6: fixed overrides (50/40/30/20/10) — OV wallet + ledger.
                |--------------------------------------------------------------------------
                */

                $drFee = (float) (
                    MembershipFee::where('FeeName', 'Direct Referral')->value('Cost')
                    ?? 400.00
                );

                $levelCommissionRates = [
                    2 => 50.00, // 1st Level Override
                    3 => 40.00, // 2nd Level Override
                    4 => 30.00, // 3rd Level Override
                    5 => 20.00, // 4th Level Override
                    6 => 10.00, // 5th Level Override
                ];

                $drCommissionAmount = $type === self::TYPE_SATELLITE
                    ? $drFee
                    : round($drFee * 0.90, 2); // 90% = 360.00

                $txAmount = $type === self::TYPE_SATELLITE
                    ? 0.00
                    : round($drFee * 0.10, 2); // 10% = 40.00

                $currentUplineCode = $activation->upline_code;
                $level = 1;
                $maxLevels = 6;

                while ($currentUplineCode && $level <= $maxLevels) {
                    $uplineUser = User::where('referralCode', $currentUplineCode)->first();

                    if (! $uplineUser) {
                        Log::warning(
                            "Upline user not found for code: {$currentUplineCode} at Level {$level}"
                        );

                        break;
                    }

                    if ($level === 1) {
                        /*
                        | Level 1 - Direct Referral (DR wallet + ledger)
                        */
                        if ($drCommissionAmount > 0) {
                            Wallet::create([
                                'UserID' => $uplineUser->UserID,
                                'ActivationID' => $activation->ActivationID,
                                'amount' => $drCommissionAmount,
                                'source' => 'DR',
                                'status' => 'Active',
                            ]);

                            Ledger::create([
                                'RefID' => $uplineUser->UserID,
                                'amount' => -$drCommissionAmount,
                                'Source' => 'DR',
                                'ActivationID' => $activation->ActivationID,
                                'status' => 'Completed',
                            ]);

                            Log::info('Direct Referral commission written.', [
                                'UserID' => $uplineUser->UserID,
                                'ActivationID' => $activation->ActivationID,
                                'amount' => $drCommissionAmount,
                                'type' => $type,
                            ]);
                        }

                        /*
                        | TX - 10% of Direct Referral (ledger-only, regular flavor)
                        | No Wallet entry is created for TX.
                        */
                        if ($txAmount > 0) {
                            Ledger::create([
                                'RefID' => $uplineUser->UserID,
                                'amount' => $txAmount,
                                'Source' => 'TX',
                                'ActivationID' => $activation->ActivationID,
                                'status' => 'Completed',
                            ]);

                            Log::info('TX ledger entry created.', [
                                'UserID' => $uplineUser->UserID,
                                'ActivationID' => $activation->ActivationID,
                                'amount' => $txAmount,
                                'percentage' => '10%',
                            ]);
                        }
                    } else {
                        /*
                        | Levels 2-6 - Override (OV wallet + ledger)
                        */
                        $commissionAmount = $levelCommissionRates[$level] ?? 0.00;

                        if ($commissionAmount > 0) {
                            Wallet::create([
                                'UserID' => $uplineUser->UserID,
                                'ActivationID' => $activation->ActivationID,
                                'amount' => $commissionAmount,
                                'source' => 'OV',
                                'status' => 'Active',
                            ]);

                            Ledger::create([
                                'RefID' => $uplineUser->UserID,
                                'amount' => -$commissionAmount,
                                'Source' => 'OV',
                                'ActivationID' => $activation->ActivationID,
                                'status' => 'Completed',
                            ]);

                            Log::info(
                                "Override commission written for UserID: {$uplineUser->UserID} ".
                                "at Level {$level}: {$commissionAmount}"
                            );
                        }
                    }

                    // Traverse up to next upline level
                    $currentUplineCode = $uplineUser->uplineCode ?? null;
                    $level++;
                }
            });
        } catch (\RuntimeException $e) {
            // "No longer pending" race — the caller surfaces a friendly error.
            Log::warning('Activation approval aborted: '.$e->getMessage(), [
                'ActivationID' => $activation->ActivationID,
                'UserID' => $selectedUserID,
            ]);

            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Send Emails OUTSIDE Database Transaction (Prevents SMTP Latency Delays)
        |--------------------------------------------------------------------------
        */

        // Send email to the newly activated user
        if ($activatedUser && filled($activatedUser->email)) {
            try {
                Mail::to($activatedUser->email)
                    ->send(new ActivationApprovedMail($activatedUser, $activation, $totalMembershipFeesCost));
            } catch (\Throwable $e) {
                Log::error('Failed to send approval email to user.', ['error' => $e->getMessage()]);
            }
        }

        // Send email to direct recruiter/upline
        if ($referralUser && filled($referralUser->email)) {
            try {
                Mail::to($referralUser->email)
                    ->send(new InviteeApprovedMail($referralUser, $activatedUser, $activation));
            } catch (\Throwable $e) {
                Log::error('Failed to send approval email to upline.', ['error' => $e->getMessage()]);
            }
        }

        return true;
    }

    /**
     * Reject a pending activation: status + history + user Inactive + email.
     * The caller resolves the activation row and handles the remarks prompt.
     */
    public function reject(
        Activation $activation,
        int $selectedUserID,
        string $remarks
    ): void {
        DB::transaction(function () use ($activation, $selectedUserID, $remarks) {
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

        if ($user && filled($user->email)) {
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
                Log::error('Failed to send rejection email.', ['error' => $e->getMessage()]);
            }
        }
    }
}
