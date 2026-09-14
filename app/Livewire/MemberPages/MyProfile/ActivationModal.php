<?php

namespace App\Livewire\MemberPages\MyProfile;

use App\Models\Activation;
use App\Models\Beneficiary;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\ActivationHistory;
use Illuminate\Support\Facades\Mail;


class ActivationModal extends Component
{
    public bool $open = false;
    public $activationData;
    public bool $isReactivation = false;

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
    public string $reference_code = '';// GCash, Maya, or Bank transaction reference

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

    /*
    |--------------------------------------------------------------------------
    | Modal State Variables
    |--------------------------------------------------------------------------
    */
    public ?int $selectedUserID = null;

    /*
    |--------------------------------------------------------------------------
    | Helper to Get Membership Fee Amount
    |--------------------------------------------------------------------------
    */
    private function getMembershipFee(): string
    {
        $setting = Setting::where('name', 'Membership Fee')->first();

        // Fallback to '1999' if the setting is not found
        return $setting ? (string) $setting->value : '1999';
    }

    private function loadPaymentSettings(): void
    {
        $this->gcashEnabled = filter_var(
            Setting::where('name', 'gcash_payment_enabled')->value('value'),
            FILTER_VALIDATE_BOOLEAN
        );

        $this->paymayaEnabled = filter_var(
            Setting::where('name', 'paymaya_payment_enabled')->value('value'),
            FILTER_VALIDATE_BOOLEAN
        );


        $this->bankEnabled = filter_var(
            Setting::where('name', 'bank_payment_enabled')->value('value'),
            FILTER_VALIDATE_BOOLEAN
        );

        $this->gcashConvenienceFeeEnabled = filter_var(
            Setting::where('name', 'gcash_convenience_fee_enabled')->value('value'),
            FILTER_VALIDATE_BOOLEAN
        );

        $this->paymayaConvenienceFeeEnabled = filter_var(
            Setting::where('name', 'paymaya_convenience_fee_enabled')->value('value'),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Open Modal
    |--------------------------------------------------------------------------
    */
    #[On('open-activation-modal')]
    public function openModal($UserID = null): void
    {
        $this->isReactivation = false;
        $UserID = $UserID ?? Auth::id();

        Log::info('Activation modal opened', [
            'selected_UserID' => $UserID,
            'opened_by' => Auth::id(),
        ]);

        $this->selectedUserID = $UserID;
        $this->selectedUserRecord = User::find($UserID);

        $this->loadPaymentSettings();
        $this->activationData = null;
        $this->resetValidation();

        $this->payment_method = '';
        $this->amount = $this->getMembershipFee();
        $this->upline_code = $this->selectedUserRecord?->uplineCode ?? '';
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

        $this->loadPaymentSettings();

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

            $this->loadPaymentDetails(); // <-- add this
        }

        $this->amount = $this->getMembershipFee();
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

        $this->amount = $this->getMembershipFee();
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

        if (!$user) {
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
    | Load GCash / Bank Details from Settings
    |--------------------------------------------------------------------------
    */
    private function loadPaymentDetails(): void
    {
        // reset first so switching methods doesn't leak old values
        $this->gcashName = '';
        $this->gcashNumber = '';
        $this->bankName = '';
        $this->bankAccountName = '';
        $this->bankAccountNumber = '';

        if ($this->payment_method === 'GCash') {
            $this->gcashNumber = Setting::where('name', 'gcash_number1')->value('value') ?? '';
            $this->gcashName = Setting::where('name', 'gcash_number1_name')->value('value') ?? '';

        } elseif ($this->payment_method === 'Bank Transfer') {
            $this->bankName = 'BPI'; // hardcoded since settings only has bpi_* keys right now
            $this->bankAccountName = Setting::where('name', 'bpi_account_name')->value('value') ?? '';
            $this->bankAccountNumber = Setting::where('name', 'bpi_account_number')->value('value') ?? '';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Close Modal
    |--------------------------------------------------------------------------
    */
    public function closeModal(): void
    {
        $this->reset([
            'beneficiaries',
            'payment_method',
            'upline_code',
            'reference_code',
            'activationData',
            'rejectionRemarks',
            'gcashName',
            'gcashNumber',
            'bankName',
            'bankAccountName',
            'bankAccountNumber',
        ]);

        $this->isReactivation = false;

        $this->amount = $this->getMembershipFee();
        $this->resetValidation();
        $this->open = false;
    }

    public function approveActivation($UserID = null): void
    {
        Log::info('=== APPROVE ACTIVATION STARTED ===', [
            'received_user_id' => $UserID,
            'selected_user_id' => $this->selectedUserID,
        ]);

        $userIdToApprove = $UserID ?? $this->selectedUserID;

        Log::info('User ID to approve', [
            'user_id_to_approve' => $userIdToApprove,
        ]);

        $activation = Activation::where('UserID', $userIdToApprove)
            ->latest()
            ->first();

        Log::info('Activation lookup result', [
            'found' => $activation !== null,
            'activation_id' => $activation?->ActivationID,
            'activation_user_id' => $activation?->UserID,
            'status' => $activation?->status,
        ]);

        if (! $activation && ! $UserID) {
            Log::warning('No activation found. Looking for latest pending activation.');

            $activation = Activation::where('status', 'Pending')
                ->latest()
                ->first();

            Log::info('Fallback activation result', [
                'found' => $activation !== null,
                'activation_id' => $activation?->ActivationID,
                'activation_user_id' => $activation?->UserID,
            ]);
        }

        if (! $activation) {
            Log::error('Approval failed: activation not found.');
            return;
        }

        $targetUserID = $activation->UserID;

        Log::info('Target user determined', [
            'target_user_id' => $targetUserID,
        ]);

        DB::transaction(function () use ($activation, $targetUserID) {
            // 1. Update activation status
            $activation->update([
                'status' => 'Approved',
            ]);

            // 2. Update user status to Active (and sync uplineCode from activation if available)
            User::where('UserID', $targetUserID)->update([
                'status' => 'Active',
                'uplineCode' => $activation->upline_code,
            ]);
        });

        $user = User::find($targetUserID);

        Log::info('Selected user information', [
            'user_exists' => $user !== null,
            'user_id' => $user?->UserID,
            'username' => $user?->username,
            'email' => $user?->email,
        ]);

        $upline = User::where(
            'referralCode',
            $activation->upline_code
        )->first();

        Log::info('Upline information', [
            'upline_exists' => $upline !== null,
            'upline_id' => $upline?->UserID,
            'upline_username' => $upline?->username,
            'upline_email' => $upline?->email,
            'upline_code' => $activation->upline_code,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Send email to selected user
        |--------------------------------------------------------------------------
        */
        if ($user && filled($user->email)) {
            Log::info('Attempting to send approval email to selected user', [
                'to' => $user->email,
            ]);

            try {
                Mail::raw(
                    "Congratulations!\n\n" .
                    "Your account has been activated successfully.\n\n" .
                    "You can now log in and access all features.",
                    function ($message) use ($user) {
                        $message->to($user->email)
                            ->subject('Account Activated');
                    }
                );

                Log::info('Approval email sent successfully', [
                    'to' => $user->email,
                ]);
            } catch (\Throwable $e) {
                Log::error('Failed to send approval email to selected user', [
                    'user_id' => $user->UserID,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        } else {
            Log::warning('Selected user email missing', [
                'user_exists' => $user !== null,
                'email' => $user?->email,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Send email to upline
        |--------------------------------------------------------------------------
        */
        if ($upline && filled($upline->email) && $user) {
            Log::info('Attempting to send email to upline', [
                'to' => $upline->email,
            ]);

            try {
                Mail::raw(
                    "Your downline {$user->username} has been approved.",
                    function ($message) use ($upline) {
                        $message->to($upline->email)
                            ->subject('Downline Approved');
                    }
                );

                Log::info('Upline email sent successfully', [
                    'to' => $upline->email,
                ]);
            } catch (\Throwable $e) {
                Log::error('Failed to send approval email to upline', [
                    'upline_id' => $upline->UserID,
                    'email' => $upline->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        } else {
            Log::warning('Cannot send email to upline', [
                'upline_exists' => $upline !== null,
                'email' => $upline?->email,
            ]);
        }

        Log::info('=== APPROVE ACTIVATION COMPLETED ===');

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
                "Unfortunately, your activation request has been rejected.",
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
        $this->loadPaymentSettings();
        $membershipFee = $this->getMembershipFee();

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
            $this->validate([
                'payment_method' => ['required', 'in:Cash,GCash,Maya,Bank Transfer'],
                'amount' => ['required', 'numeric', 'in:' . $membershipFee],
                'upline_code' => ['required', 'string', 'max:100'],
                'reference_code' => ['nullable', 'string', 'max:100', 'required_unless:payment_method,Cash'],
                'beneficiaries' => ['required', 'array', 'max:3'],
                'beneficiaries.*.fullname' => ['nullable', 'string', 'max:150'],
                'beneficiaries.*.birthdate' => ['nullable', 'date'],
                'beneficiaries.*.relationship' => ['nullable', 'string', 'max:50'],
            ]);

                // Check payment availability from settings
            if ($this->payment_method === 'GCash' && !$this->gcashEnabled) {
                $this->addError(
                    'payment_method',
                    'GCash payment is currently unavailable.'
                );
                return;
            }
            if ($this->payment_method === 'PayMaya' && !$this->paymayaEnabled) {
                $this->addError(
                    'payment_method',
                    'PayMaya payment is currently unavailable.'
                );
                return;
            }

            if ($this->payment_method === 'Bank Transfer' && !$this->bankEnabled) {
                $this->addError(
                    'payment_method',
                    'Bank Transfer is currently unavailable.'
                );
                return;
            }

        } catch (ValidationException $e) {
            $this->dispatch('swal:close');
            throw $e;
        }

        $currentUser = Auth::user();
        $selectedUser = User::find($this->selectedUserID);

        if (!$currentUser || !$selectedUser) {
            $this->dispatch('swal:close');
            $this->dispatch('toast', type: 'error', message: 'Authentication or user error occurred.');
            return;
        }

        $cleanUplineCode = trim($this->upline_code);
        $referralUser = User::where('referralCode', $cleanUplineCode)->first();

        if (!$referralUser) {
            $this->dispatch('swal:close');
            $this->addError('upline_code', 'The referral/upline code is invalid.');
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

            if ($rejectedActivation) {
                $rejectedActivation->update([
                    'amount' => (float) $membershipFee,
                    'type' => $activationType,
                    'payment_method' => $this->payment_method,
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
                    'UserID' => $selectedUser->UserID,
                    'amount' => (float) $membershipFee,
                    'type' => $activationType,
                    'payment_method' => $this->payment_method,
                    'upline_code' => $cleanUplineCode,
                    'payment_reference' => $paymentReference,
                    'ProcessedBy' => $currentUser->UserID,
                    'status' => 'Pending',
                ]);

                $actionLabel = 'submitted successfully';
                $userSubject = 'Activation Request Submitted';
                $uplineSubject = 'New Downline Activation Request';
                $adminSubject = 'New Activation Request Submitted (Admin Notice)';
            }

            // Store/Update uplineCode on the User table column upon submission
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

            if (filled($selectedUser->email)) {
                $mailData['emailTitle'] = "Activation Request Status";
                $mailData['introMessage'] = "Your activation request has been {$actionLabel}. Below are your complete details:";

                Mail::send('emails.activation-request', $mailData, function ($message) use ($selectedUser, $userSubject) {
                    $message->to($selectedUser->email)->subject($userSubject);
                });
            }

            if ($referralUser && filled($referralUser->email)) {
                $mailData['emailTitle'] = "Downline Activation Notice";
                $mailData['introMessage'] = "Hello " . ($referralUser->firstname ?? $referralUser->username) . ", your downline (" . $selectedUser->username . ") has {$actionLabel} an activation request.";

                Mail::send('emails.activation-request', $mailData, function ($message) use ($referralUser, $uplineSubject) {
                    $message->to($referralUser->email)->subject($uplineSubject);
                });
            }

            if (filled($adminEmail)) {
                $mailData['emailTitle'] = "Admin Notice: Activation Request";
                $mailData['introMessage'] = "An activation request has been {$actionLabel} with the following comprehensive details:";

                Mail::send('emails.activation-request', $mailData, function ($message) use ($adminEmail, $adminSubject) {
                    $message->to($adminEmail)->subject($adminSubject);
                });
            }

            if ($currentUser && filled($currentUser->email) && $currentUser->UserID !== $selectedUser->UserID) {
                $mailData['emailTitle'] = "Action Notice: Activation Submission";
                $mailData['introMessage'] = "You have successfully submitted an activation request for " . $selectedUser->username . ". Here are the details:";

                Mail::send('emails.activation-request', $mailData, function ($message) use ($currentUser) {
                    $message->to($currentUser->email)->subject('Activation Request Submitted on Behalf');
                });
            }

            DB::commit();

            $this->dispatch('swal:close');
            $this->closeModal();
            $this->dispatch('account-activated');

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Activation request submitted successfully.',
            ]);

            $this->dispatch('members-refresh');
            $this->dispatch('close-activation-modal');

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

    public function confirmActivate(): void
    {
        $this->loadPaymentSettings();
        $membershipFee = $this->getMembershipFee();

        try {
            $this->validate([
                'payment_method' => ['required', 'in:Cash,GCash,Maya,Bank Transfer'],
                'amount' => ['required', 'numeric', 'in:' . $membershipFee],
                'upline_code' => ['required', 'string', 'max:100'],
                'reference_code' => ['nullable', 'string', 'max:100', 'required_unless:payment_method,Cash'],
                'beneficiaries' => ['required', 'array', 'max:3'],
                'beneficiaries.*.fullname' => ['nullable', 'string', 'max:150'],
                'beneficiaries.*.birthdate' => ['nullable', 'date'],
                'beneficiaries.*.relationship' => ['nullable', 'string', 'max:50'],
            ]);

             // Check payment availability
            if ($this->payment_method === 'GCash' && !$this->gcashEnabled) {
                $this->addError(
                    'payment_method',
                    'GCash payment is currently unavailable.'
                );

                return;
            }

            if ($this->payment_method === 'Bank Transfer' && !$this->bankEnabled) {
                $this->addError(
                    'payment_method',
                    'Bank Transfer is currently unavailable.'
                );

                return;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        }

        $this->dispatch('swal:confirm', [
            'title' => 'Submit Activation?',
            'text' => 'Please review your details before submitting your activation.',
            'icon' => 'question',
            'method' => 'activate',
            'showLoading' => true
        ]);
    }

    public function render()
    {
        return view('livewire.member-pages.my-profile.activation-modal');
    }
}
