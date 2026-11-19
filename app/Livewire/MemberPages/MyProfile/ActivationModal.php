<?php

namespace App\Livewire\MemberPages\MyProfile;

use App\Jobs\SendActivationEmail;
use App\Models\Activation;
use App\Models\ActivationHistory;
use App\Models\Beneficiary;
use App\Models\MembershipFee;
use App\Models\Setting;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class ActivationModal extends Component
{


    public bool $open = false;

    public $activationData;

    public bool $isReactivation = false;

    // Renewal mode: the member's previous activation EXPIRED. Filing
    // creates a brand-new Pending activation instead of resubmitting
    // a rejected one.
    public bool $isRenewal = false;

    public ?string $rejectionRemarks = null;

    public ?User $selectedUserRecord = null;

    /*
    |--------------------------------------------------------------------------
    | Beneficiaries
    |--------------------------------------------------------------------------
    */
    public array $beneficiaries = [];

    /*
    |--------------------------------------------------------------------------
    | Payment Information
    |--------------------------------------------------------------------------
    */
    public string $payment_method = ''; // Cash, GCash, Maya, Bank Transfer

    public string $amount = '';          // Dynamic activation amount

    public string $upline_code = ''; // User-entered referral/upline code

    public string $reference_code = ''; // GCash, Maya, or Bank transaction reference

    // Display-only: pulled from settings when GCash/Bank is selected
    public string $gcashName = '';

    public string $gcashNumber = '';

    public string $paymayaName = '';

    public string $paymayaNumber = '';

    public string $bankName = '';

    public string $bankAccountName = '';

    public string $bankAccountNumber = '';

    public bool $gcashEnabled = false;

    public bool $paymayaEnabled = false;

    public bool $bankEnabled = false;

    public bool $gcashConvenienceFeeEnabled = false;

    public bool $paymayaConvenienceFeeEnabled = false;

    public float $totalMembershipFeesCost = 0.00;


    /*
    |--------------------------------------------------------------------------
    | Membership Variant
    |--------------------------------------------------------------------------
    */
    public $variants = [];

    public $selectedVariantId = null;

    public bool $showBenefitsModal = false;

    public ?int $benefitsVariantId = null;

    public string $benefitsVariantName = '';

    public $variantBenefits = [];

    /*
    |--------------------------------------------------------------------------
    | Modal State Variables
    |--------------------------------------------------------------------------
    */
    public ?int $selectedUserID = null;


    /*
    |--------------------------------------------------------------------------
    | Branch Payment Settings
    |
    | Reads "<branch prefix><name>" from the settings table. A branch with
    | no matching row gets $default (disabled / empty) — it never falls
    | back to another branch's account details.
    |--------------------------------------------------------------------------
    */
    /*
    |--------------------------------------------------------------------------
    | Centralized Payment Settings
    |--------------------------------------------------------------------------
    */

    private function setting(string $name, $default = null)
    {
        return Setting::where('name', $name)->value('value') ?? $default;
    }

    private function settingFlag(string $name): bool
    {
        return filter_var(
            $this->setting($name, 'false'),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Branch Variant Grants
    |
    | Only variants granted to the resolved branch (an Active row in
    | branch_variant_grants) can be shown or selected.
    |--------------------------------------------------------------------------
    */

    /**
     * Active variants granted to the resolved branch.
     */
    private function loadAvailableVariants(): array
    {
        return Variant::query()
            ->where('status', 'Active')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    /**
     * VIDs the resolved branch is allowed to use (for server-side validation).
     *
     * @return array<int, int>
     */

    /*
    |--------------------------------------------------------------------------
    | Helper to Get Membership Fee Amount
    |--------------------------------------------------------------------------
    */
    /**
     * Get the total membership fee for a specific variant.
     */
    private function getMembershipFee(?int $variantId = null): string
    {
        if ($variantId === null) {
            return '0.00';
        }

        return number_format(
            (float) MembershipFee::where('VID', $variantId)->sum('Cost'),
            2,
            '.',
            ''
        );
    }

    /**
     * Update activation amount when membership variant changes.
     */
    public function updatedSelectedVariantId($value): void
    {
        $variantId = filled($value) ? (int) $value : null;

        $this->amount = $this->getMembershipFee($variantId);

        $this->totalMembershipFeesCost = $variantId
            ? (float) MembershipFee::where('VID', $variantId)->sum('Cost')
            : 0.00;

        $this->resetValidation('selectedVariantId');
    }

    /**
     * When the upline code changes, the branch (and therefore the
     * available variants and payment accounts) may change too —
     * reload everything that depends on the branch.
     */
    public function updatedUplineCode(): void
    {
        // Not in the submit form (closed, or read-only "view pending" mode).
        if (! $this->open || $this->activationData) {
            return;
        }

        $this->variants = $this->loadAvailableVariants();

        $allowedIds = array_map('strval', array_column($this->variants, 'VID'));

        // Keep the current variant if the new branch still grants it.
        if (! in_array((string) $this->selectedVariantId, $allowedIds, true)) {
            $this->selectedVariantId = $this->variants[0]['VID'] ?? null;
        }

        $this->amount = $this->getMembershipFee(
            filled($this->selectedVariantId)
                ? (int) $this->selectedVariantId
                : null
        );

        $this->loadPaymentSettings();

        // Drop the chosen payment method if the new branch doesn't offer it.
        $stillAvailable = match ($this->payment_method) {
            'GCash' => $this->gcashEnabled,
            'Maya' => $this->paymayaEnabled,
            'Bank Transfer' => $this->bankEnabled,
            default => true,
        };

        if (! $stillAvailable) {
            $this->payment_method = '';
            $this->reference_code = '';
        }

        $this->loadPaymentDetails();

        $this->resetValidation('upline_code');
        $this->resetValidation('selectedVariantId');
    }

        private function loadPaymentSettings(): void
    {
        $this->gcashEnabled = $this->settingFlag('gcash_payment_enabled');

        $this->paymayaEnabled = $this->settingFlag('paymaya_payment_enabled');

        $this->bankEnabled = $this->settingFlag('bank_payment_enabled');

        $this->gcashConvenienceFeeEnabled = $this->settingFlag('gcash_convenience_fee_enabled');

        $this->paymayaConvenienceFeeEnabled = $this->settingFlag('paymaya_convenience_fee_enabled');

        $this->totalMembershipFeesCost = filled($this->selectedVariantId)
            ? (float) MembershipFee::where(
                'VID',
                $this->selectedVariantId
            )->sum('Cost')
            : 0.00;
    }

    /*
    |--------------------------------------------------------------------------
    | Open Modal
    |--------------------------------------------------------------------------
    */
    #[On('open-activation-modal')]
    public function openModal($UserID = null, bool $renew = false): void
    {
        $this->isReactivation = false;
        $this->isRenewal = $renew;
        $UserID = $UserID ?? Auth::id();

        Log::info('Activation modal opened', [
            'selected_UserID' => $UserID,
            'opened_by' => Auth::id(),
        ]);

        // The member record and upline code must be set BEFORE the variants
        // and payment settings, because the branch is resolved from them.
        $this->selectedUserID = $UserID;
        $this->selectedUserRecord = User::find($UserID);
        $this->upline_code = $this->selectedUserRecord?->uplineCode ?? '';

        $this->variants = $this->loadAvailableVariants();

        // Default to the first granted variant.
        $this->selectedVariantId = $this->variants[0]['VID'] ?? null;

        $this->amount = $this->getMembershipFee(
            filled($this->selectedVariantId)
                ? (int) $this->selectedVariantId
                : null
        );

        $this->totalMembershipFeesCost = (float) $this->amount;

        $this->loadPaymentSettings();
        $this->activationData = null;
        $this->resetValidation();
        $this->payment_method = '';
            // Calculate the amount using the selected variant.
        $this->amount = $this->getMembershipFee(
            filled($this->selectedVariantId)
                ? (int) $this->selectedVariantId
                : null
        );
        $this->reference_code = '';

        $this->loadBeneficiaries();

        $this->open = true;
    }

    #[On('open-rejected-activation')]
    public function openRejectedActivation($UserID = null): void
    {
        $this->isReactivation = true;
        $UserID = $UserID ?? Auth::id();

        $this->selectedUserID = $UserID;
        $this->selectedUserRecord = User::find($UserID);

        $activation = Activation::where('UserID', $UserID)
            ->where('status', 'Rejected')
            ->latest()
            ->first();

        if ($activation) {
            $history = ActivationHistory::where('ActivationID', $activation->ActivationID)
                ->where('status', 'Rejected')
                ->latest()
                ->first();

            $this->payment_method = $activation->payment_method ?? '';
            $this->upline_code = $activation->upline_code ?? '';
            $this->reference_code = $activation->payment_reference ?? '';
            $this->rejectionRemarks = $history?->remarks;
        }

        // Payment settings/details are loaded AFTER the upline code is set,
        // because the branch is resolved from it.
        $this->loadPaymentSettings();

        if ($activation) {
            $this->loadPaymentDetails(); // <-- add this
        }

        // Only variants granted to the branch.
        $this->variants = $this->loadAvailableVariants();

        $activeVariantIds = array_column($this->variants, 'VID');

        $this->selectedVariantId = (
            $activation
            && in_array($activation->VID, $activeVariantIds)
        )
            ? $activation->VID
            : ($this->variants[0]['VID'] ?? null);

        $this->amount = $this->getMembershipFee(
            filled($this->selectedVariantId)
                ? (int) $this->selectedVariantId
                : null
        );

        $this->totalMembershipFeesCost = (float) $this->amount;
        $this->loadBeneficiaries();
        $this->resetValidation();

        $this->open = true;
    }

    /*
    |--------------------------------------------------------------------------
    | View Pending Activation Modal
    |--------------------------------------------------------------------------
    */
    #[On('open-pending-activation')]
    public function viewActivation($UserID = null): void
    {
        $UserID = $UserID ?? Auth::id();

        $this->selectedUserID = $UserID;
        $this->selectedUserRecord = User::find($UserID);

        $this->activationData = Activation::where('UserID', $UserID)
            ->latest()
            ->first();

        $this->amount = $this->activationData
        ? number_format((float) $this->activationData->amount, 2, '.', '')
        : '0.00';
        $this->loadBeneficiaries();

        $this->open = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Load Existing Beneficiaries
    |--------------------------------------------------------------------------
    */
    private function loadBeneficiaries(): void
    {
        /** @var User|null $user */
        $user = User::find($this->selectedUserID);

        if (! $user) {
            $this->beneficiaries = $this->emptyBeneficiaryRows();

            return;
        }

        $existingBeneficiaries = $user
            ->beneficiaries()
            ->where('status', true)
            ->orderBy('BeneficiaryID')
            ->take(3)
            ->get()
            ->values();

        $this->beneficiaries = [];

        for ($i = 0; $i < 3; $i++) {
            if (isset($existingBeneficiaries[$i])) {
                $this->beneficiaries[] = [
                    'fullname' => $existingBeneficiaries[$i]->fullname ?? '',
                    'birthdate' => $existingBeneficiaries[$i]->birthdate ?? '',
                    'relationship' => $existingBeneficiaries[$i]->relationship ?? '',
                ];
            } else {
                $this->beneficiaries[] = [
                    'fullname' => '',
                    'birthdate' => '',
                    'relationship' => '',
                ];
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Empty Beneficiary Rows Structure
    |--------------------------------------------------------------------------
    */
    private function emptyBeneficiaryRows(): array
    {
        return [
            ['fullname' => '', 'birthdate' => '', 'relationship' => ''],
            ['fullname' => '', 'birthdate' => '', 'relationship' => ''],
            ['fullname' => '', 'birthdate' => '', 'relationship' => ''],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Clear Payment Reference When Payment Method Changes
    |--------------------------------------------------------------------------
    */
    public function updatedPaymentMethod(): void
    {
        if ($this->payment_method === 'Cash') {
            $this->reference_code = '';
        }

        $this->loadPaymentDetails();

        $this->resetValidation('payment_method');
        $this->resetValidation('reference_code');
    }

    /*
    |--------------------------------------------------------------------------
    | Load GCash / Maya / Bank Details from the Branch's Settings
    |--------------------------------------------------------------------------
    */
    private function loadPaymentDetails(): void
    {
        // Reset payment details first.
        $this->gcashName = '';
        $this->gcashNumber = '';

        $this->paymayaName = '';
        $this->paymayaNumber = '';

        $this->bankName = '';
        $this->bankAccountName = '';
        $this->bankAccountNumber = '';

        if ($this->payment_method === 'GCash') {

            $this->gcashNumber = (string) (
                $this->setting('gcash_number1')
                ?? $this->setting('gcash_number', '')
            );

            $this->gcashName = (string) $this->setting(
                'gcash_number1_name',
                ''
            );

        } elseif ($this->payment_method === 'Maya') {

            $this->paymayaNumber = (string) $this->setting(
                'paymaya_account_number',
                ''
            );

            $this->paymayaName = (string) $this->setting(
                'paymaya_account_name',
                ''
            );

        } elseif ($this->payment_method === 'Bank Transfer') {

            $this->bankName = (string) $this->setting(
                'bank_name',
                ''
            );

            $this->bankAccountName = (string) $this->setting(
                'bank_account_name',
                ''
            );

            $this->bankAccountNumber = (string) $this->setting(
                'bank_account_number',
                ''
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Close Modal
    |--------------------------------------------------------------------------
    */
    public function closeModal(): void
    {
        $this->isRenewal = false;

        $this->reset([
            'beneficiaries',
            'payment_method',
            'upline_code',
            'reference_code',
            'activationData',
            'rejectionRemarks',
            'gcashName',
            'gcashNumber',
            'paymayaName',
            'paymayaNumber',
            'bankName',
            'bankAccountName',
            'bankAccountNumber',
            'selectedVariantId',
            'variants',
        ]);

        $this->isReactivation = false;

        $this->amount = $this->getMembershipFee();
        $this->resetValidation();
        $this->open = false;

        $this->showBenefitsModal = false;
        $this->benefitsVariantId = null;
        $this->benefitsVariantName = '';
        $this->variantBenefits = [];

        $this->amount = '0.00';
    }


public function approveActivation($UserID = null): void
{
    $userIdToApprove = $UserID ?? $this->selectedUserID;

    $activation = Activation::where('UserID', $userIdToApprove)
        ->where('status', 'Pending')
        ->latest()
        ->first();

    if (! $activation) {
        Log::warning('Approval failed: pending activation not found.', [
            'user_id' => $userIdToApprove,
        ]);

        $this->dispatch(
            'toast',
            type: 'error',
            message: 'No pending activation request was found.'
        );

        return;
    }

    $targetUserID = $activation->UserID;
    $uplineCode = trim((string) $activation->upline_code);

    // Find the direct referral using the submitted referral code.
    $upline = User::where('referralCode', $uplineCode)->first();

    if (! $upline || $upline->status !== 'Active') {
        $this->dispatch(
            'toast',
            type: 'error',
            message: 'The direct referral is missing or inactive. Approval cannot proceed.'
        );

        return;
    }

    try {
        DB::transaction(function () use (
            $activation,
            $targetUserID,
            $upline,
            $uplineCode
        ) {
            // Lock and retrieve the member being activated.
            $user = User::where('UserID', $targetUserID)
                ->lockForUpdate()
                ->firstOrFail();

            // Assign the same branch as the direct referral.
            $user->update([
                'status' => 'Active',
                'uplineCode' => $uplineCode,
            ]);

        });
    } catch (\Throwable $e) {
        Log::error('Activation approval failed.', [
            'user_id' => $targetUserID,
            'activation_id' => $activation->ActivationID,
            'error' => $e->getMessage(),
        ]);

        $this->dispatch(
            'toast',
            type: 'error',
            message: 'Approval failed. Please try again.'
        );

        return;
    }

    // Reload the approved member and direct referral for notifications.
    $user = User::find($targetUserID);

    $upline = User::where('referralCode', $uplineCode)->first();

    // Notify the activated member.
    if ($user && filled($user->email)) {
        try {
            Mail::raw(
                "Congratulations!\n\n"
                . "Your account has been activated successfully.\n\n"
                . 'You can now log in and access all features.',
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Account Activated');
                }
            );
        } catch (\Throwable $e) {
            Log::error('Failed to send approval email to member.', [
                'user_id' => $user->UserID,
                'error' => $e->getMessage(),
            ]);
        }
    }

    // Notify the direct referral/upline.
    if ($upline && filled($upline->email) && $user) {
        try {
            Mail::raw(
                "Your downline {$user->username} has been approved.",
                function ($message) use ($upline) {
                    $message->to($upline->email)
                        ->subject('Downline Approved');
                }
            );
        } catch (\Throwable $e) {
            Log::error('Failed to send approval email to upline.', [
                'upline_id' => $upline->UserID,
                'error' => $e->getMessage(),
            ]);
        }
    }

    Log::info('Activation approved and branch assigned.', [
        'user_id' => $targetUserID,
        'upline_code' => $uplineCode,
    ]);

    $this->dispatch('activation-approved');
    $this->closeModal();
}


    /*
    |--------------------------------------------------------------------------
    | Reject Activation Request
    |--------------------------------------------------------------------------
    */
    public function rejectActivation($UserID = null): void
    {
        $userIdToReject = $UserID ?? $this->selectedUserID;

        $activation = Activation::where('UserID', $userIdToReject)
            ->latest()
            ->first();

        if (! $activation) {
            return;
        }

        $activation->update([
            'status' => 'Rejected',
        ]);

        $user = User::find($userIdToReject);

        $upline = User::where(
            'referralCode',
            $activation->upline_code
        )->first();

        if ($user && filled($user->email)) {
            Mail::raw(
                'Unfortunately, your activation request has been rejected.',
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Activation Rejected');
                }
            );
        }

        if ($upline && filled($upline->email) && $user) {
            Mail::raw(
                "The activation request for your downline {$user->username} has been rejected.",
                function ($message) use ($upline) {
                    $message->to($upline->email)
                        ->subject('Downline Activation Rejected');
                }
            );
        }

        $this->dispatch('activation-rejected');
        $this->closeModal();
    }

    /*
    |--------------------------------------------------------------------------
    | Submit Activation Process
    |--------------------------------------------------------------------------
    */
    #[On('activate')]
    public function activate(): void
    {
        // Guard: a stale/ghost instance (e.g. from a duplicated mount listening
        // to the same global events) must never run the submission flow.
        if (! $this->open || $this->selectedUserID === null) {
            return;
        }

        $this->loadPaymentSettings();

        $variantId = filled($this->selectedVariantId)
            ? (int) $this->selectedVariantId
            : null;

        $membershipFee = $this->getMembershipFee($variantId);

        // Always synchronize the amount with the selected variant.
        $this->amount = $membershipFee;

        Log::info('--- ACTIVATION PROCESS STARTED ---', [
            'authenticated' => Auth::check(),
            'current_user_id' => Auth::id(),
            'selected_user_id' => $this->selectedUserID,
            'payment_method' => $this->payment_method,
            'amount' => $membershipFee,
            'upline_code' => $this->upline_code,
            'reference_code' => $this->reference_code,
        ]);

        try {
            $this->validate($this->activationRules($membershipFee));
            $this->assertPaymentMethodAvailable();
        } catch (ValidationException $e) {
            $this->dispatch('swal:close');
            throw $e;
        }

        $currentUser = Auth::user();
        $selectedUser = User::find($this->selectedUserID);

        if (! $currentUser || ! $selectedUser) {
            $this->dispatch('swal:close');
            $this->dispatch('toast', type: 'error', message: 'Authentication or user error occurred.');

            return;
        }

        $cleanUplineCode = trim($this->upline_code);
        $referralUser = User::where('referralCode', $cleanUplineCode)->first();

        if (! $referralUser) {
            $this->dispatch('swal:close');
            $this->addError('upline_code', 'The referral/upline code is invalid.');

            return;
        }

        if ($referralUser->status !== 'Active') {
            $this->dispatch('swal:close');
            $this->addError(
                'upline_code',
                'This upline account is Inactive/Suspended and cannot be used.'
            );

            return;
        }

        $existingActivation = Activation::where('UserID', $selectedUser->UserID)
            ->whereIn('status', ['Pending', 'Approved'])
            ->first();

        if ($existingActivation) {
            $this->dispatch('swal:close');
            $this->dispatch(
                'toast',
                type: 'error',
                message: 'This account already has an active or pending activation request.'
            );
            $this->dispatch('activation-rejected');

            return;
        }

        // In renewal mode the guard above already passed: the member's
        // latest activation is Expired (or Rejected), so filing a fresh
        // one is legitimate. The expired row is kept untouched as history.

        $activationType = $this->payment_method === 'Cash' ? 'W' : 'O';
        $paymentReference = $this->payment_method === 'Cash' ? null : trim($this->reference_code);

        DB::beginTransaction();

        try {
            $rejectedActivation = Activation::where('UserID', $selectedUser->UserID)
                ->where('status', 'Rejected')
                ->latest()
                ->first();

            $adminEmail = config('mail.from.address', 'anfredalbit110525@gmail.com');

            $cleanBeneficiaries = [];
            foreach ($this->beneficiaries as $index => $beneficiary) {
                $fullname = trim($beneficiary['fullname'] ?? '');
                $birthdate = trim($beneficiary['birthdate'] ?? '');
                $relationship = trim($beneficiary['relationship'] ?? '');

                if ($fullname === '' && $birthdate === '' && $relationship === '') {
                    continue;
                }

                if ($fullname === '' || $birthdate === '' || $relationship === '') {
                    throw ValidationException::withMessages([
                        "beneficiaries.$index.fullname" => 'Full name is required.',
                        "beneficiaries.$index.birthdate" => 'Birthdate is required.',
                        "beneficiaries.$index.relationship" => 'Relationship is required.',
                    ]);
                }

                $cleanBeneficiaries[] = [
                    'fullname' => $fullname,
                    'birthdate' => $birthdate,
                    'relationship' => $relationship,
                ];

                $existingBeneficiary = Beneficiary::where('UserID', $selectedUser->UserID)
                    ->where('fullname', $fullname)
                    ->first();

                if ($existingBeneficiary) {
                    $existingBeneficiary->update([
                        'birthdate' => $birthdate,
                        'relationship' => $relationship,
                        'status' => true,
                    ]);
                } else {
                    Beneficiary::create([
                        'BenefitID' => 1,
                        'UserID' => $selectedUser->UserID,
                        'fullname' => $fullname,
                        'birthdate' => $birthdate,
                        'relationship' => $relationship,
                        'status' => true,
                    ]);
                }
            }

            // A renewal NEVER resubmits the expired row — it files a
            // brand-new activation so the expired one stays as history.
            if ($rejectedActivation && ! $this->isRenewal) {
                $rejectedActivation->update([
                    'amount' => (float) $membershipFee,
                    'VID' => $this->selectedVariantId,
                    'type' => $activationType,
                    'payment_method' => $this->payment_method === 'Maya' ? 'Maya' : $this->payment_method,
                    'upline_code' => $cleanUplineCode,
                    'payment_reference' => $paymentReference,
                    'ProcessedBy' => $currentUser->UserID,
                    'status' => 'Pending',
                ]);

                $actionLabel = 'resubmitted successfully';
                $userSubject = 'Activation Request Resubmitted';
                $uplineSubject = 'Downline Activation Resubmitted';
                $adminSubject = 'Activation Request Resubmitted (Admin Notice)';
            } else {
                Activation::create([
                    'VID' => $this->selectedVariantId,
                    'UserID' => $selectedUser->UserID,
                    'amount' => (float) $membershipFee,
                    'type' => $activationType,
                    'payment_method' => $this->payment_method === 'Maya' ? 'Maya' : $this->payment_method,
                    'upline_code' => $cleanUplineCode,
                    'payment_reference' => $paymentReference,
                    'ProcessedBy' => $currentUser->UserID,
                    'status' => 'Pending',
                ]);

                $actionLabel = $this->isRenewal
                    ? 'renewed successfully — a new activation has been filed'
                    : 'submitted successfully';
                $userSubject = $this->isRenewal ? 'Membership Renewal Submitted' : 'Activation Request Submitted';
                $uplineSubject = $this->isRenewal ? 'Downline Membership Renewal' : 'New Downline Activation Request';
                $adminSubject = $this->isRenewal
                    ? 'Membership Renewal Submitted (Admin Notice)'
                    : 'New Activation Request Submitted (Admin Notice)';
            }

            // Store the submitted upline referral code.
            $selectedUser->update([
                'uplineCode' => $cleanUplineCode,
            ]);

            $mailData = [
                'selectedUser' => $selectedUser,
                'currentUser' => $currentUser,
                'payment_method' => $this->payment_method,
                'amount' => $membershipFee,
                'upline_code' => $cleanUplineCode,
                'reference_code' => $paymentReference,
                'formattedBeneficiaries' => $cleanBeneficiaries,
            ];

            DB::commit();

            if (filled($selectedUser->email)) {
                SendActivationEmail::dispatch(
                    $selectedUser->email,
                    $userSubject,
                    'Activation Request Status',
                    "Your activation request has been {$actionLabel}. Below are your complete details:",
                    $mailData
                );
            }

            if ($referralUser && filled($referralUser->email)) {
                SendActivationEmail::dispatch(
                    $referralUser->email,
                    $uplineSubject,
                    'Downline Activation Notice',
                    'Hello '.($referralUser->firstname ?? $referralUser->username)
                        .', your downline ('.$selectedUser->username
                        .") has {$actionLabel} an activation request.",
                    $mailData
                );
            }

            if (filled($adminEmail)) {
                SendActivationEmail::dispatch(
                    $adminEmail,
                    $adminSubject,
                    'Admin Notice: Activation Request',
                    "An activation request has been {$actionLabel} with the following comprehensive details:",
                    $mailData
                );
            }

            if (
                $currentUser &&
                filled($currentUser->email) &&
                $currentUser->UserID !== $selectedUser->UserID
            ) {
                SendActivationEmail::dispatch(
                    $currentUser->email,
                    'Activation Request Submitted on Behalf',
                    'Action Notice: Activation Submission',
                    'You have successfully submitted an activation request for '
                        .$selectedUser->username.'. Here are the details:',
                    $mailData
                );
            }

            Log::info('ACTIVATION COMMITTED SUCCESSFULLY', [
                'selected_user_id' => $selectedUser->UserID,
                'payment_method' => $this->payment_method,
                'upline_code' => $cleanUplineCode,
            ]);

            $wasRenewal = $this->isRenewal;

            $this->closeModal();

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => $wasRenewal
                    ? 'Membership renewal submitted successfully. A new activation has been filed for review.'
                    : 'Activation request submitted successfully.',
            ]);

            // Refresh other components
            $this->dispatch('account-activated');
            $this->dispatch('members-refresh');

        } catch (ValidationException $e) {
            DB::rollBack();

            $this->dispatch('swal:close');
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatch('swal:close');

            Log::error('Activation failed', [
                'error' => $e->getMessage(),
            ]);

            $this->dispatch(
                'toast',
                type: 'error',
                message: 'Activation failed. Please try again.'
            );
        }
    }

    /**
     * Get the IDs of variants available to the resolved branch.
     *
     * @return array<int, int>
     */
    private function allowedVariantIds(): array
    {
        return array_map(
            'intval',
            array_column($this->loadAvailableVariants(), 'VID')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Shared Activation Validation + Payment Availability
    |--------------------------------------------------------------------------
    */
    /**
     * @return array<string, array<int, mixed>>
     */
    private function activationRules(string $membershipFee): array
    {
        return [
            'payment_method' => ['required', 'in:Cash,GCash,Maya,Bank Transfer'],
            'amount' => ['required', 'numeric', 'in:'.$membershipFee],
            'upline_code' => ['required', 'string', 'max:100'],
            'reference_code' => ['nullable', 'string', 'max:100', 'required_unless:payment_method,Cash'],
            'beneficiaries' => ['required', 'array', 'max:3'],
            'beneficiaries.*.fullname' => ['nullable', 'string', 'max:150'],
            'beneficiaries.*.birthdate' => ['nullable', 'date'],
            'beneficiaries.*.relationship' => ['nullable', 'string', 'max:50'],
            'selectedVariantId' => [
                'required',
                'integer',
                // Must be an Active variant AND granted to the branch.
                \Illuminate\Validation\Rule::exists('variants', 'VID')
                    ->where('status', 'Active')
                    ->whereIn('VID', $this->allowedVariantIds()),
            ],
        ];
    }

    private function assertPaymentMethodAvailable(): void
    {
        $unavailable = match ($this->payment_method) {
            'GCash' => $this->gcashEnabled ? null : 'GCash payment',
            'Maya' => $this->paymayaEnabled ? null : 'Maya payment',
            'Bank Transfer' => $this->bankEnabled ? null : 'Bank Transfer',
            default => null,
        };

        if ($unavailable !== null) {
            throw ValidationException::withMessages([
                'payment_method' => "{$unavailable} is currently unavailable.",
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Step 1: Validate inputs, then ask for the typed SweetAlert confirmation
    |--------------------------------------------------------------------------
    */


public function confirmActivate(): void
{
    $this->loadPaymentSettings();

    // Get the fee for the selected membership variant.
    // $variantId = filled($this->selectedVariantId)
    //     ? (int) $this->selectedVariantId
    //     : null;

        // TEMPORARY: Force Variant 1 while the variant selector is hidden
        $variantId = filled($this->selectedVariantId)
            ? (int) $this->selectedVariantId
            : null;

    $membershipFee = $this->getMembershipFee($variantId);

    // Keep the amount synchronized with the selected variant.
    $this->amount = $membershipFee;

    Log::info('Activation confirmation fee check', [
        'selected_user_id' => $this->selectedUserID,
        'selected_variant_id' => $this->selectedVariantId,
        'amount' => $this->amount,
        'membership_fee' => $membershipFee,
    ]);

    try {
        $this->validate($this->activationRules($membershipFee));
        $this->assertPaymentMethodAvailable();
    } catch (ValidationException $e) {
        $this->dispatch('swal:close');

        Log::warning('Activation validation failed', [
            'errors' => $e->errors(),
            'selected_user_id' => $this->selectedUserID,
            'selected_variant_id' => $this->selectedVariantId,
            'amount' => $this->amount,
            'membership_fee' => $membershipFee,
        ]);

        throw $e;
    }

    // Find the upline using the entered referral code.
    $cleanUplineCode = trim($this->upline_code);

    $upline = User::where('referralCode', $cleanUplineCode)->first();

    if (! $upline) {
        $this->addError(
            'upline_code',
            'The referral/upline code is invalid.'
        );

        return;
    }

    if ($upline->status !== 'Active') {
        $this->addError(
            'upline_code',
            'This upline account is inactive or suspended and cannot be used.'
        );

        return;
    }

    $uplineFullName = trim(implode(' ', array_filter([
        $upline->firstname,
        $upline->middlename,
        $upline->lastname,
    ])));

    if ($this->isRenewal) {
        $action = 'Renew';
    } elseif ($this->isReactivation) {
        $action = 'ReActivate';
    } else {
        $action = 'Activate';
    }

    $this->dispatch('swal:confirm', [
        'title' => "{$action} Account?",
        'html' => 'This will submit the activation request for admin review.'
            . '<br><br>'
            . '<strong>Upline:</strong> '
            . e($uplineFullName ?: $upline->username)
            . '<br>'
            . '<strong>Referral Code:</strong> '
            . e($cleanUplineCode)
            . '<br><br>'
            . 'Type <strong>ACTIVATE</strong> to confirm.',
        'icon' => 'question',
        'requireConfirmWord' => 'ACTIVATE',
        'confirmText' => "Yes, {$action}!",
        'method' => 'activate',
        'loadingTitle' => 'Processing Activation...',
        'loadingText' => 'Please wait while we process your request.',
    ]);
}



    /*
    |--------------------------------------------------------------------------
    | View Benefits for a Membership Variant
    |--------------------------------------------------------------------------
    */
    public function viewBenefits($vid): void
    {
        $variant = Variant::where('VID', $vid)
            ->where('status', 'Active')
            ->first();

        if (! $variant) {
            $this->addError(
                'selectedVariantId',
                'The selected membership variant was not found or is inactive.'
            );

            return;
        }

        $this->benefitsVariantId = (int) $variant->VID;
        $this->benefitsVariantName = $variant->name;

        $this->variantBenefits = DB::table('benefits')
            ->where('VID', $variant->VID)
            ->where('status', 1)
            ->orderBy('holdertype')
            ->orderBy('name')
            ->get()
            ->map(fn ($benefit) => (array) $benefit)
            ->all();

        $this->showBenefitsModal = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Close Benefits Modal
    |--------------------------------------------------------------------------
    */
    public function closeBenefitsModal(): void
    {
        $this->showBenefitsModal = false;
        $this->benefitsVariantId = null;
        $this->benefitsVariantName = '';
        $this->variantBenefits = [];
    }


    public function render()
    {
        return view('livewire.member-pages.my-profile.activation-modal');
    }
}
