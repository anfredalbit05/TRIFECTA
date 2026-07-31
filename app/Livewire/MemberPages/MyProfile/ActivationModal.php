<?php

namespace App\Livewire\MemberPages\MyProfile;

use App\Models\Activation;
use App\Models\Beneficiary;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Setting; // <-- Make sure to import the Setting model
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

    /*
    |--------------------------------------------------------------------------
    | Open Modal
    |--------------------------------------------------------------------------
    */
    #[On('open-activation-modal')]
    public function openModal($UserID = null): void
    {
        $UserID = $UserID ?? Auth::id();

        Log::info('Activation modal opened', [
            'selected_UserID' => $UserID,
            'opened_by' => Auth::id(),
        ]);

        $this->selectedUserID = $UserID;
        $this->selectedUserRecord = User::find($UserID);

        $this->activationData = null;
        $this->resetValidation();

        $this->payment_method = '';
        $this->amount = $this->getMembershipFee(); // <-- Fetch dynamically here
        $this->upline_code = '';
        $this->reference_code = '';

        $this->loadBeneficiaries();

        $this->open = true;
    }

    #[On('open-rejected-activation')]
    public function openRejectedActivation($UserID = null): void
    {
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

        $this->amount = $this->getMembershipFee(); // <-- Fetch dynamically here
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

        $this->amount = $this->getMembershipFee(); // <-- Fetch dynamically here
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

        $this->resetValidation('payment_method');
        $this->resetValidation('reference_code');
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
        ]);

        $this->amount = $this->getMembershipFee(); // <-- Fetch dynamically here
        $this->resetValidation();
        $this->open = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Approve Activation Request
    |--------------------------------------------------------------------------
    */
    public function approveActivation(): void
    {
        $activation = Activation::where('UserID', $this->selectedUserID)
            ->latest()
            ->first();

        if (!$activation) {
            return;
        }

        DB::transaction(function () use ($activation) {
            $activation->update([
                'status' => 'Approved',
            ]);
        });

        Mail::raw(
            "Congratulations!\n\n" .
            "Your account has been activated successfully.\n\n" .
            "You can now log in and access all features.",
            function ($message) {
                $user = User::find($this->selectedUserID);

                if ($user && filled($user->email)) {
                    $message->to($user->email)
                            ->subject('Account Activated');
                }
            }
        );

        User::where('UserID', $this->selectedUserID)->update([
            'status' => 'Active',
        ]);

        $this->dispatch('activation-approved');
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
                'amount' => ['required', 'numeric', 'in:' . $membershipFee], // Validate against dynamic fee
                'upline_code' => ['required', 'string', 'max:100'],
                'reference_code' => ['nullable', 'string', 'max:100', 'required_unless:payment_method,Cash'],
                'beneficiaries' => ['required', 'array', 'max:3'],
                'beneficiaries.*.fullname' => ['nullable', 'string', 'max:150'],
                'beneficiaries.*.birthdate' => ['nullable', 'date'],
                'beneficiaries.*.relationship' => ['nullable', 'string', 'max:50'],
            ]);
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

            $isNewActivation = false;
            $adminEmail = config('mail.from.address', 'anfredalbit110525@gmail.com');

            $cleanBeneficiaries = [];
            foreach ($this->beneficiaries as $b) {
                if (filled($b['fullname']) || filled($b['birthdate']) || filled($b['relationship'])) {
                    $cleanBeneficiaries[] = [
                        'fullname' => $b['fullname'] ?: 'N/A',
                        'birthdate' => $b['birthdate'] ?: 'N/A',
                        'relationship' => $b['relationship'] ?: 'N/A',
                    ];
                }
            }

            $mailData = [
                'selectedUser' => $selectedUser,
                'currentUser' => $currentUser,
                'payment_method' => $this->payment_method,
                'amount' => $membershipFee,
                'upline_code' => $cleanUplineCode,
                'reference_code' => $paymentReference,
                'formattedBeneficiaries' => $cleanBeneficiaries,
            ];

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

                $activation = $rejectedActivation;
                $actionLabel = 'resubmitted successfully';
                $userSubject = 'Activation Request Resubmitted';
                $uplineSubject = 'Downline Activation Resubmitted';
                $adminSubject = 'Activation Request Resubmitted (Admin Notice)';

            } else {
                $activation = Activation::create([
                    'UserID' => $selectedUser->UserID,
                    'amount' => (float) $membershipFee,
                    'type' => $activationType,
                    'payment_method' => $this->payment_method,
                    'upline_code' => $cleanUplineCode,
                    'payment_reference' => $paymentReference,
                    'ProcessedBy' => $currentUser->UserID,
                    'status' => 'Pending',
                ]);

                $isNewActivation = true;
                $actionLabel = 'submitted successfully';
                $userSubject = 'Activation Request Submitted';
                $uplineSubject = 'New Downline Activation Request';
                $adminSubject = 'New Activation Request Submitted (Admin Notice)';
            }

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

            if ((int) $currentUser->RoleID === 1) {
                $selectedUser->update([
                    'status' => 'Active',
                ]);
            }

            if ($isNewActivation) {
                Wallet::create([
                    'UserID' => $referralUser->UserID,
                    'ActivationID' => $activation->ActivationID,
                    'amount' => $activation->amount - 1899,
                    'status' => 'Active',
                ]);
            }

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

            DB::commit();

            $this->dispatch('swal:close');
            $this->closeModal();
            $this->dispatch('account-activated');

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Activation request submitted successfully.',
            ]);

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
