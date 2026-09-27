<?php

namespace App\Livewire\AdminPages\Masterlist;

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
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Validation\ValidationException;


class ActivationReviewModal extends Component
{
    public bool $open = false;

    public ?int $selectedUserID = null;

    public ?User $member = null;

    public ?Activation $activation = null;

    public ?User $referralUser = null;

    public array $beneficiaries = [];

    public string $remarks = '';

    public bool $isRejecting = false;

    public bool $gcashConvenienceFeeEnabled = false;

    public bool $paymayaConvenienceFeeEnabled = false;

    public float $totalMembershipFeesCost = 0.00;


    public string $policy_number = '';

    public string $receipt_number = '';

    public bool $policyNumberExists = false;
    public bool $receiptNumberExists = false;



    /*
    |--------------------------------------------------------------------------
    | Open Review Modal
    |--------------------------------------------------------------------------
    */

    private function loadPaymentSettings(): void
    {
        $this->gcashConvenienceFeeEnabled = filter_var(
            Setting::where('name', 'gcash_convenience_fee_enabled')->value('value'),
            FILTER_VALIDATE_BOOLEAN
        );
        $this->paymayaConvenienceFeeEnabled = filter_var(
            Setting::where('name', 'paymaya_convenience_fee_enabled')->value('value'),
            FILTER_VALIDATE_BOOLEAN
        );

        $this->totalMembershipFeesCost = (float) MembershipFee::sum('Cost');
    }

    #[On('open-activation-review')]
    public function openModal($UserID): void
    {
        Log::info('Opening activation review modal.', ['UserID' => $UserID]);

        $this->selectedUserID = (int) $UserID;

        $this->loadPaymentSettings();

        $this->member = User::find($this->selectedUserID);

        /*
        |--------------------------------------------------------------------------
        | Find Pending Activation by UserID
        |--------------------------------------------------------------------------
        */
        $this->activation = Activation::where('UserID', $this->selectedUserID)
            ->where('status', 'Pending')
            ->latest('ActivationID')
            ->first();

            /*
            |--------------------------------------------------------------------------
            | Load Policy / Receipt Numbers
            |--------------------------------------------------------------------------
            */
            $this->policy_number = (string) ($this->activation?->policy_number ?? '');
            $this->receipt_number = (string) ($this->activation?->receipt_number ?? '');

        /*
        |--------------------------------------------------------------------------
        | Load Referral User
        |--------------------------------------------------------------------------
        */
        $this->referralUser = null;

        if ($this->activation && filled($this->activation->upline_code)) {
            $this->referralUser = User::where(
                'referralCode',
                trim($this->activation->upline_code)
            )->first();
        }

        /*
        |--------------------------------------------------------------------------
        | Load Member Beneficiaries
        |--------------------------------------------------------------------------
        */
        $this->beneficiaries = Beneficiary::where('UserID', $this->selectedUserID)
            ->where('status', true)
            ->orderBy('BeneficiaryID')
            ->get()
            ->toArray();

        $this->open = true;
    }

    public function checkActivationNumberDuplicates(): void
{
    if (! $this->activation) {
        $this->policyNumberExists = false;
        $this->receiptNumberExists = false;
        return;
    }

    $policy = strtoupper(trim($this->policy_number));
    $receipt = trim($this->receipt_number);

    $this->policyNumberExists = $policy !== ''
        && Activation::where('policy_number', $policy)
            ->where('ActivationID', '!=', $this->activation->ActivationID)
            ->exists();

    $this->receiptNumberExists = $receipt !== ''
        && Activation::where('receipt_number', $receipt)
            ->where('ActivationID', '!=', $this->activation->ActivationID)
            ->exists();
}

public function generatePolicyNumber(): void
{
    $nextSequence = $this->getNextPolicySequence();

    $this->policy_number = sprintf(
        'TRI-%05d-TS%d-%05d',
        $nextSequence,
        now()->year,
        $nextSequence
    );

    $this->resetValidation('policy_number');
}

public function getNextPolicyNumberProperty(): string
{
    $sequence = $this->getNextPolicySequence();

    return sprintf(
        'TRI-%05d-TS%d-%05d',
        $sequence,
        now()->year,
        $sequence
    );
}

private function getNextPolicySequence(): int
{
    $currentYear = now()->year;

    $policies = Activation::query()
        ->whereNotNull('policy_number')
        ->where(
            'policy_number',
            'like',
            'TRI-%-TS' . $currentYear . '-%'
        )
        ->pluck('policy_number');

    $highestSequence = 0;

    foreach ($policies as $policy) {
        if (preg_match(
            '/^TRI-(\d+)-TS\d{4}-(\d+)$/',
            $policy,
            $matches
        )) {
            $firstSequence = (int) $matches[1];
            $finalSequence = (int) $matches[2];

            $highestSequence = max(
                $highestSequence,
                $firstSequence,
                $finalSequence
            );
        }
    }

    return $highestSequence + 1;
}


public function getNextReceiptNumberProperty(): string
{
    return str_pad(
        (string) Activation::nextReceiptNumber(),
        6,
        '0',
        STR_PAD_LEFT
    );
}


    /*
    |--------------------------------------------------------------------------
    | Approve Activation
    |--------------------------------------------------------------------------
    */

    /*
    public function approve(): void
    {
        if (!$this->activation || !$this->selectedUserID) {
            Log::warning('Approval failed: activation or selected user missing.');
            return;
        }

        DB::transaction(function () {

            Log::info('Starting approval transaction for ActivationID: ' . $this->activation->ActivationID);

            // 1. Update Activation Status
            $this->activation->update([
                'status' => 'Approved',
            ]);

          // Get the currently authenticated admin user
            $admin = Auth::user();

            // Build the full name, safely handling any nullable middle name
            $adminName = trim(implode(' ', [
                $admin->firstname ?? '',
                $admin->middlename ?? '',
                $admin->lastname ?? ''
            ]));

            // Log the approval history with the admin's full name
            ActivationHistory::create([
                'ActivationID' => $this->activation->ActivationID,
                'AdminID' => Auth::id(),
                'status' => 'Approved',
                'remarks' => "Activation approved by {$adminName}.",
            ]);

            // Get CC Admins
            $adminEmails = User::join('roles', 'users.RoleID', '=', 'roles.RoleID')
                ->whereIn('roles.rolename', ['Super Administrator', 'Administrator'])
                ->whereNotNull('users.email')
                ->pluck('users.email')
                ->toArray();

            // 2. Activate Member User Account
            User::where('UserID', $this->selectedUserID)->update([
                'status' => 'Active',
            ]);

            $activatedUser = User::find($this->selectedUserID);


            // |--------------------------------------------------------------------------
            // | Record Membership Fees (MF) breakdown into Ledger for the Activated User
            // |--------------------------------------------------------------------------

            $membershipFees = MembershipFee::all();

            foreach ($membershipFees as $fee) {
                Ledger::create([
                    'RefID'        => $fee->FeeID,
                    'amount'       => $fee->Cost,
                    'Source'       => 'MF', // Membership Fee
                    'ActivationID' => $this->activation->ActivationID,
                    'status'       => 'Completed',
                ]);

                Log::info("Membership Fee ledger entry [{$fee->FeeName}] written with RefID: {$fee->FeeID} and amount: {$fee->Cost}");
            }


            // |--------------------------------------------------------------------------
            // | Add PF (50.00) to the Direct Upline / Referral User Wallet & Ledger
            // |--------------------------------------------------------------------------

            if ($this->referralUser) {
                Wallet::create([
                    'UserID'       => $this->referralUser->UserID,
                    'ActivationID' => $this->activation->ActivationID,
                    'amount'       => 50.00,
                    'source'       => 'PF',
                    'status'       => 'Active',
                ]);

                Ledger::create([
                    'RefID'        => $this->referralUser->UserID,
                    'amount'       => -50.00,
                    'Source'       => 'PF',
                    'ActivationID' => $this->activation->ActivationID,
                    'status'       => 'Completed',
                ]);

                Log::info("PF entry (50.00) written for referral UserID: {$this->referralUser->UserID}");
            }


            // |--------------------------------------------------------------------------
            // | Multi-Tier Referral Commission Structure with Dynamic Sponsoring Count
            // |--------------------------------------------------------------------------

            $currentUplineCode = $this->activation->upline_code;
            $level = 1;
            $maxLevels = 6;

            $drFee = MembershipFee::where('FeeName', 'Direct Referral')->value('Cost') ?? 400.00;

            // Standard hierarchy level rates (fallback if needed)
            $commissionRates = [
                1 => (float) $drFee,
                2 => 50.00,
                3 => 40.00,
                4 => 30.00,
                5 => 20.00,
                6 => 10.00,
            ];

            Log::info('Initial Upline Code for Commission Loop: ' . ($currentUplineCode ?? 'NONE'));

            while ($currentUplineCode && $level <= $maxLevels) {
                $uplineUser = User::where('referralCode', $currentUplineCode)->first();

                if (!$uplineUser) {
                    Log::warning("Upline user not found for referral code: {$currentUplineCode} at Level {$level}");
                    break;
                }

                $commissionAmount = 0.00;
                $sourceType = ($level === 1) ? 'DR' : 'OV';

                if ($level === 1) {
                    // Level 1 always gets the Direct Referral amount
                    $commissionAmount = $commissionRates[1];
                } else {
                    // For higher levels (e.g., Level 2 like User 1), check how many direct invitees
                    // the immediate sponsor (e.g., User 2) has successfully registered up to now.
                    $sponsorUserID = User::where('referralCode', $this->activation->upline_code)->value('UserID');

                    // Count how many approved activations belong to this sponsor's direct referral code
                    $directInviteCount = Activation::where('status', 'Approved')
                        ->where('upline_code', $this->activation->upline_code)
                        ->count();

                    // Map direct invite sequence count to decreasing commission rates:
                    // 1st invite = 50, 2nd = 40, 3rd = 30, 4th = 20, 5th = 10, 6th+ = 0
                    $dynamicOverrideRates = [
                        1 => 50.00,
                        2 => 40.00,
                        3 => 30.00,
                        4 => 20.00,
                        5 => 10.00,
                    ];

                    $commissionAmount = $dynamicOverrideRates[$directInviteCount] ?? 0.00;
                }

                Log::info("Processing Level {$level}: Upline UserID {$uplineUser->UserID}, Commission: {$commissionAmount}");

                if ($commissionAmount > 0) {
                    // Create Wallet Entry for Upline
                    Wallet::create([
                        'UserID'       => $uplineUser->UserID,
                        'ActivationID' => $this->activation->ActivationID,
                        'amount'       => $commissionAmount,
                        'source'       => $sourceType,
                        'status'       => 'Active',
                    ]);

                    $negativeCommission = -$commissionAmount;

                    // Create Ledger Entry for Upline Commission/Override
                    Ledger::create([
                        'RefID'        => $uplineUser->UserID,
                        'amount'       => $negativeCommission,
                        'Source'       => $sourceType,
                        'ActivationID' => $this->activation->ActivationID,
                        'status'       => 'Completed',
                    ]);

                    Log::info("Ledger & Wallet successfully written for UserID: {$uplineUser->UserID} at Level {$level} as type [{$sourceType}] with amount: {$commissionAmount}");
                } else {
                    Log::info("No commission generated for UserID: {$uplineUser->UserID} at Level {$level} due to invite threshold limit.");
                }

                $currentUplineCode = $uplineUser->uplineCode ?? null;
                $level++;
            }

            // Send Email to Activated User (Without CCing admins to prevent spam flagging)
            if ($activatedUser && filled($activatedUser->email)) {
                try {
                    $totalAmount = MembershipFee::sum('Cost');

                    Mail::to($activatedUser->email)
                        ->send(new ActivationApprovedMail($activatedUser, $this->activation, (float) $totalAmount));
                } catch (\Throwable $e) {
                    Log::error('Failed to send approval email to activated user.', ['error' => $e->getMessage()]);
                }
            }

            //    // Send Email to Activated User
            // if ($activatedUser && filled($activatedUser->email)) {
            //     try {
            //         $totalAmount = MembershipFee::sum('Cost');

            //         Mail::to($activatedUser->email)
            //             ->cc($adminEmails)
            //             ->send(new ActivationApprovedMail($activatedUser, $this->activation, $totalAmount));

            //     } catch (\Throwable $e) {
            //         Log::error('Failed to send approval email to activated user.', ['error' => $e->getMessage()]);
            //     }
            // }

            // Send Email to Upline / Referral User
            if ($this->referralUser && filled($this->referralUser->email)) {
                try {
                    Mail::to($this->referralUser->email)
                        ->cc($adminEmails)
                        ->send(new InviteeApprovedMail($this->referralUser, $activatedUser, $this->activation));
                } catch (\Throwable $e) {
                    Log::error('Failed to send approval email to upline.', ['error' => $e->getMessage()]);
                }
            }
        });

        $this->dispatch('members-refresh');
        $this->dispatch('swal:success', title: 'Success!', message: 'Activation approved successfully.');
        $this->closeModal();
    }
    */

    public function generateReceiptNumber(): void
{
    $nextReceiptNumber = (int) Activation::nextReceiptNumber();

    $this->receipt_number = str_pad(
        (string) $nextReceiptNumber,
        6,
        '0',
        STR_PAD_LEFT
    );

    $this->resetValidation('receipt_number');
}

private function showApprovalValidationError(string $message): void
{
    $this->dispatch(
        'swal:error',
        title: 'Cannot Approve Activation',
        message: $message
    );
}

public function getNextPolicySequenceForJsProperty(): int
{
    return $this->getNextPolicySequence();
}

    public function approve(): void
    {
       // ==========================================================
    // GUARD CLAUSES
    // ==========================================================

    if (! $this->activation || ! $this->selectedUserID) {
        Log::warning(
            'Approval failed: Activation or selected user ID is missing.'
        );

        $this->showApprovalValidationError(
            'The activation request could not be found. Please close this window and try again.'
        );

        return;
    }

    // Refresh the activation from database so we validate
    // against the latest status/data.
    $this->activation = Activation::find(
        $this->activation->ActivationID
    );

    if (! $this->activation) {
        $this->showApprovalValidationError(
            'The activation request could not be found.'
        );

        return;
    }

    if ($this->activation->status === 'Approved') {
        Log::warning(
            'Approval aborted: Activation is already approved.',
            [
                'activation_id' => $this->activation->ActivationID,
            ]
        );

        $this->showApprovalValidationError(
            'This activation has already been approved.'
        );

        return;
    }

    $this->loadPaymentSettings();

    // ==========================================================
    // NORMALIZE INPUT
    // ==========================================================

    $this->policy_number = strtoupper(
        trim($this->policy_number)
    );

    $this->receipt_number = trim(
        $this->receipt_number
    );

    // ==========================================================
    // BASIC REQUIRED / FORMAT VALIDATION
    // ==========================================================

    try {
        $this->validate([
            'policy_number' => [
                'required',
                'string',
                'max:100',
                'regex:/^TRI-\d+-TS\d{4}-\d+$/',
            ],

            'receipt_number' => [
                'required',
                'string',
                'regex:/^\d{6}$/',
            ],
        ], [
            'policy_number.required' =>
                'The policy number is required.',

            'policy_number.regex' =>
                "The policy number must use the format TRI-00001-TS" .
                now()->year .
                "-00001.",

            'receipt_number.required' =>
                'The receipt number is required.',

            'receipt_number.regex' =>
                'The receipt number must contain exactly 6 digits.',
        ]);
    } catch (ValidationException $e) {

        $errors = $e->validator->errors()->all();

        $message = $errors[0]
            ?? 'Please correct the required fields before approving.';

        $this->showApprovalValidationError($message);

        return;
    }

    // ==========================================================
    // PARSE POLICY NUMBER
    // ==========================================================

    if (! preg_match(
        '/^TRI-(\d+)-TS(\d{4})-(\d+)$/',
        $this->policy_number,
        $matches
    )) {
        $this->addError(
            'policy_number',
            'The policy number format is invalid.'
        );

        $this->showApprovalValidationError(
            'The policy number format is invalid.'
        );

        return;
    }

    $policyFirstSequence = (int) $matches[1];
    $policyYear = (int) $matches[2];
    $policyFinalSequence = (int) $matches[3];

    $currentYear = now()->year;

    // ==========================================================
    // POLICY YEAR
    // ==========================================================

    if ($policyYear !== $currentYear) {

        $message =
            "The policy number must use the current year {$currentYear}.";

        $this->addError(
            'policy_number',
            $message
        );

        $this->showApprovalValidationError($message);

        return;
    }

    // ==========================================================
    // POLICY SEQUENCES MUST MATCH
    // ==========================================================

    if ($policyFirstSequence !== $policyFinalSequence) {

        $message =
            'The first and last sequence numbers of the policy number must match.';

        $this->addError(
            'policy_number',
            'The policy number sequence must match.'
        );

        $this->showApprovalValidationError($message);

        return;
    }

    // ==========================================================
    // DUPLICATE POLICY NUMBER
    // ==========================================================

$policyExists = Activation::query()
    ->whereRaw(
        'UPPER(TRIM(policy_number)) = ?',
        [strtoupper(trim($this->policy_number))]
    )
    ->where(
        'ActivationID',
        '!=',
        $this->activation->ActivationID
    )
    ->exists();

if ($policyExists) {
    $message = 'This policy number has already been used.';

    $this->addError('policy_number', $message);

    $this->dispatch(
        'swal:error',
        title: 'Cannot Approve Activation',
        message: $message
    );

    return;
}

    // ==========================================================
    // POLICY NUMBER CANNOT JUMP AHEAD
    // ==========================================================

    $nextPolicySequence = $this->getNextPolicySequence();

    if ($policyFirstSequence > $nextPolicySequence) {

        $formattedNextPolicy = str_pad(
            $nextPolicySequence,
            5,
            '0',
            STR_PAD_LEFT
        );

        $message =
            "The policy number cannot jump ahead. " .
            "The next available sequence is {$formattedNextPolicy}.";

        $this->addError(
            'policy_number',
            $message
        );

        $this->showApprovalValidationError($message);

        return;
    }

    // ==========================================================
    // RECEIPT NUMBER BASIC VALIDATION
    // ==========================================================

    $receiptNumber = (int) $this->receipt_number;

    // ==========================================================
    // DUPLICATE RECEIPT NUMBER
    // ==========================================================

$receiptExists = Activation::query()
    ->whereRaw(
        'TRIM(receipt_number) = ?',
        [trim($this->receipt_number)]
    )
    ->where(
        'ActivationID',
        '!=',
        $this->activation->ActivationID
    )
    ->exists();

   if ($receiptExists) {
    $message = 'This receipt number has already been issued.';

    $this->addError('receipt_number', $message);

    $this->dispatch(
        'swal:error',
        title: 'Cannot Approve Activation',
        message: $message
    );

    return;
}

    // ==========================================================
    // RECEIPT NUMBER CANNOT JUMP AHEAD
    // ==========================================================

    $nextReceiptNumber = (int) Activation::nextReceiptNumber();

    if ($receiptNumber > $nextReceiptNumber) {

        $formattedNextReceipt = str_pad(
            $nextReceiptNumber,
            6,
            '0',
            STR_PAD_LEFT
        );

        $message =
            "The receipt number cannot jump ahead. " .
            "The next available receipt number is {$formattedNextReceipt}.";

        $this->addError(
            'receipt_number',
            $message
        );

        $this->showApprovalValidationError($message);

        return;
    }

    // ==========================================================
    // EVERYTHING IS VALID
    // ==========================================================

    // Only after ALL validation passes do we continue
    // with the actual approval transaction.
    /** @var User|null $activatedUser */
    $activatedUser = null;

    DB::transaction(function () use (&$activatedUser) {


            Log::info('Starting approval transaction for ActivationID: '.$this->activation->ActivationID);

            // Update Activation Status + issue the next sequential receipt
            // number (generated INSIDE the transaction so concurrent approvals
            // can't collide on the same number).
            $this->activation->update([
                'status' => 'Approved',
                'policy_number' => $this->policy_number,
                'receipt_number' => $this->receipt_number,

                'cashier_name' => Setting::where('name', 'cashier_name')->value('value'),
                'cashier_signature' => Setting::where('name', 'cashier_signature')->value('value'),
            ]);

            Log::info('Receipt number issued with cashier identity.', [
                'activation_id' => $this->activation->ActivationID,
                'receipt_number' => $this->activation->receipt_number,
                'cashier_name' => $this->activation->cashier_name,
            ]);

            // Build Admin Name for Log History
            $admin = Auth::user();
            $adminName = trim(implode(' ', array_filter([
                $admin->firstname ?? '',
                $admin->middlename ?? '',
                $admin->lastname ?? '',
            ])));

            // Log Approval History
            ActivationHistory::create([
                'ActivationID' => $this->activation->ActivationID,
                'AdminID' => Auth::id(),
                'status' => 'Approved',
                'remarks' => "Activation approved by {$adminName}.",
            ]);

            // Activate User Account
            User::where('UserID', $this->selectedUserID)->update([
                'status' => 'Active',
            ]);

            $activatedUser = User::find($this->selectedUserID);

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
                    'ActivationID' => $this->activation->ActivationID,
                    'status' => 'Completed',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | GCash Convenience Fee (CF)
            |--------------------------------------------------------------------------
            | Only applies when payment_method is Gcash.
            | CF = 1% of Total Cost Membership = 19.99
            |--------------------------------------------------------------------------
            */

            if (
                (strtolower(trim($this->activation->payment_method)) === 'gcash'
                && $this->gcashConvenienceFeeEnabled) || strtolower(trim($this->activation->payment_method)) === 'paymaya'
                && $this->paymayaConvenienceFeeEnabled
            ) {

                $totalAmount = (float) MembershipFee::sum('Cost');
                $cfAmount = round($totalAmount * 0.01, 2);

                Ledger::create([
                    'RefID' => $this->selectedUserID,
                    'amount' => $cfAmount,
                    'Source' => 'CF',
                    'ActivationID' => $this->activation->ActivationID,
                    'status' => 'Completed',
                ]);

                Log::info('GCash | Paymaya CF ledger entry created.', [
                    'UserID' => $this->selectedUserID,
                    'ActivationID' => $this->activation->ActivationID,
                    'payment_method' => $this->activation->payment_method,
                    'amount' => $cfAmount,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Provident Fund (PF: 50.00) to Direct Recruiter / Upline
            |--------------------------------------------------------------------------
            */
            if ($this->referralUser) {
                Wallet::create([
                    'UserID' => $this->referralUser->UserID,
                    'ActivationID' => $this->activation->ActivationID,
                    'amount' => 50.00,
                    'source' => 'PF',
                    'status' => 'Active',
                ]);

                Ledger::create([
                    'RefID' => $this->referralUser->UserID,
                    'amount' => -50.00,
                    'Source' => 'PF',
                    'ActivationID' => $this->activation->ActivationID,
                    'status' => 'Completed',
                ]);

                Log::info("PF entry (50.00) written for referral UserID: {$this->referralUser->UserID}");
            }

            /*
            |--------------------------------------------------------------------------
            | Multi-Tier Commission Hierarchy
            |--------------------------------------------------------------------------
            | Level 1: Direct Referral
            |   - DR = 90% of Direct Referral Fee
            |   - TX = 10% of Direct Referral Fee
            |
            | Level 2: 1st Level Override (50)
            | Level 3: 2nd Level Override (40)
            | Level 4: 3rd Level Override (30)
            | Level 5: 4th Level Override (20)
            | Level 6: 5th Level Override (10)
            |--------------------------------------------------------------------------
            */

            $drFee = (float) (
                MembershipFee::where('FeeName', 'Direct Referral')->value('Cost')
                ?? 400.00
            );

            // Split the Direct Referral fee
            $drCommissionAmount = round($drFee * 0.90, 2); // 90% = 360.00
            $txAmount = round($drFee * 0.10, 2);           // 10% = 40.00

            $levelCommissionRates = [
                2 => 50.00, // 1st Level Override
                3 => 40.00, // 2nd Level Override
                4 => 30.00, // 3rd Level Override
                5 => 20.00, // 4th Level Override
                6 => 10.00, // 5th Level Override
            ];

            $currentUplineCode = $this->activation->upline_code;
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

                /*
                |--------------------------------------------------------------------------
                | Level 1 - Direct Referral
                |--------------------------------------------------------------------------
                | DR receives only 90% of the original DR fee.
                |--------------------------------------------------------------------------
                */
                if ($level === 1) {

                    if ($drCommissionAmount > 0) {

                        Wallet::create([
                            'UserID' => $uplineUser->UserID,
                            'ActivationID' => $this->activation->ActivationID,
                            'amount' => $drCommissionAmount,
                            'source' => 'DR',
                            'status' => 'Active',
                        ]);

                        Ledger::create([
                            'RefID' => $uplineUser->UserID,
                            'amount' => -$drCommissionAmount,
                            'Source' => 'DR',
                            'ActivationID' => $this->activation->ActivationID,
                            'status' => 'Completed',
                        ]);

                        Log::info('Direct Referral commission written.', [
                            'UserID' => $uplineUser->UserID,
                            'ActivationID' => $this->activation->ActivationID,
                            'amount' => $drCommissionAmount,
                            'percentage' => '90%',
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | TX - 10% of Direct Referral
                    |--------------------------------------------------------------------------
                    | TX is ONLY added to the Ledger.
                    | No Wallet entry is created for TX.
                    |--------------------------------------------------------------------------
                    */

                    if ($txAmount > 0) {

                        Ledger::create([
                            'RefID' => $uplineUser->UserID,
                            'amount' => -$txAmount,
                            'Source' => 'TX',
                            'ActivationID' => $this->activation->ActivationID,
                            'status' => 'Completed',
                        ]);

                        Log::info('TX ledger entry created.', [
                            'UserID' => $uplineUser->UserID,
                            'ActivationID' => $this->activation->ActivationID,
                            'amount' => $txAmount,
                            'percentage' => '10%',
                        ]);
                    }

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | Levels 2-6 - Override
                    |--------------------------------------------------------------------------
                    */

                    $commissionAmount = $levelCommissionRates[$level] ?? 0.00;

                    if ($commissionAmount > 0) {

                        Wallet::create([
                            'UserID' => $uplineUser->UserID,
                            'ActivationID' => $this->activation->ActivationID,
                            'amount' => $commissionAmount,
                            'source' => 'OV',
                            'status' => 'Active',
                        ]);

                        Ledger::create([
                            'RefID' => $uplineUser->UserID,
                            'amount' => -$commissionAmount,
                            'Source' => 'OV',
                            'ActivationID' => $this->activation->ActivationID,
                            'status' => 'Completed',
                        ]);

                        Log::info(
                            "Override commission written for UserID: {$uplineUser->UserID} " .
                            "at Level {$level}: {$commissionAmount}"
                        );
                    }
                }

                // Traverse up to next upline level
                $currentUplineCode = $uplineUser->uplineCode ?? null;
                $level++;
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Send Emails OUTSIDE Database Transaction (Prevents SMTP Latency Delays)
        |--------------------------------------------------------------------------
        */
        // Send email to the newly activated user
        if ($activatedUser && filled($activatedUser->email)) {
            try {

                Mail::to($activatedUser->email)
                    ->send(new ActivationApprovedMail($activatedUser, $this->activation, $this->totalMembershipFeesCost));
            } catch (\Throwable $e) {
                Log::error('Failed to send approval email to user.', ['error' => $e->getMessage()]);
            }
        }

        // Send email to direct recruiter/upline
        if ($this->referralUser && filled($this->referralUser->email)) {
            try {
                Mail::to($this->referralUser->email)
                    ->send(new InviteeApprovedMail($this->referralUser, $activatedUser, $this->activation));
            } catch (\Throwable $e) {
                Log::error('Failed to send approval email to upline.', ['error' => $e->getMessage()]);
            }
        }

        // 4. UI Events
        $this->dispatch('members-refresh');
        $this->dispatch('swal:success', title: 'Success!', message: 'Activation approved successfully.');
        $this->closeModal();
    }

    /*
    |--------------------------------------------------------------------------
    | Reject Activation Request
    |--------------------------------------------------------------------------
    */

    public function confirmReject(): void
    {
        $this->dispatch('swal:reject-confirm', [
            'title' => 'Reject activation?',
            'text' => 'Please provide the reason for rejection.',
        ]);
    }

    #[On('submit-rejection')]
    public function submitRejection($remarks): void
    {
        $this->remarks = is_array($remarks) ? ($remarks['remarks'] ?? '') : $remarks;
        $this->rejectActivation();
    }

    public function rejectActivation(): void
    {
        if ($this->isRejecting) {
            return;
        }

        $this->isRejecting = true;

        try {
            $this->validate([
                'remarks' => ['required', 'string', 'max:1000'],
            ]);

            $activation = Activation::where('ActivationID', $this->activation?->ActivationID)
                ->latest()
                ->first();

            if (! $activation) {
                return;
            }

            DB::transaction(function () use ($activation) {
                $activation->update([
                    'status' => 'Rejected',
                ]);

                ActivationHistory::create([
                    'ActivationID' => $activation->ActivationID,
                    'AdminID' => Auth::id(),
                    'status' => 'Rejected',
                    'remarks' => $this->remarks,
                ]);

                User::where('UserID', $this->selectedUserID)->update([
                    'status' => 'Inactive',
                ]);

                $user = User::find($this->selectedUserID);

                if ($user && filled($user->email)) {
                    Mail::to($user->email)->send(
                        new ActivationRejectedMail(
                            $user,
                            $activation,
                            $this->beneficiaries,
                            $this->remarks
                        )
                    );
                }
            });

            $this->dispatch('members-refresh');
            $this->dispatch('swal:success', title: 'Rejected!', message: 'Activation rejected successfully.');
            $this->closeModal();
        } finally {
            $this->isRejecting = false;
        }
    }

    public function closeModal(): void
    {
        $this->open = false;
        $this->selectedUserID = null;
        $this->member = null;
        $this->activation = null;
        $this->referralUser = null;
        $this->beneficiaries = [];
        $this->remarks = '';

        $this->policy_number = '';
        $this->receipt_number = '';
    }

    public function render()
    {
        return view('livewire.admin-pages.masterlist.activation-review-modal');
    }
}
