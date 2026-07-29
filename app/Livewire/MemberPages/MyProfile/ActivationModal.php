<?php

namespace App\Livewire\MemberPages\MyProfile;

use App\Models\Activation;
use App\Models\Beneficiary;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class ActivationModal extends Component
{
    public bool $open = false;
    public $activationData;

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
    public string $amount = '1900';    // Fixed activation amount
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

        // 🔥 Clear out any lingering activation data from previous queries
        $this->activationData = null;

        $this->resetValidation();

        $this->payment_method = '';
        $this->amount = '1900';
        $this->upline_code = '';
        $this->reference_code = '';

        $this->loadBeneficiaries();

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
        ]);

        $this->amount = '1900';
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

            User::where('UserID', $this->selectedUserID)->update([
                'status' => 'Active',
            ]);
        });

        $this->dispatch('toast', type: 'success', message: 'Account approved.');
        $this->closeModal();
    }

    /*
    |--------------------------------------------------------------------------
    | Reject Activation Request
    |--------------------------------------------------------------------------
    */
    public function rejectActivation(): void
    {
        $activation = Activation::where('UserID', $this->selectedUserID)
            ->latest()
            ->first();

        if (!$activation) {
            return;
        }

        DB::transaction(function () use ($activation) {
            $activation->update([
                'status' => 'Rejected',
            ]);

            User::where('UserID', $this->selectedUserID)->update([
                'status' => 'Inactive',
            ]);
        });

        $this->dispatch('toast', type: 'success', message: 'Activation rejected.');
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
        // 🚀 Dispatch loading sweetalert before processing starts
        $this->dispatch('swal:loading', [
            'title' => 'Processing Activation...',
            'text' => 'Please wait while we process your request.',
        ]);

        Log::info('--- ACTIVATION PROCESS STARTED ---', [
            'authenticated' => Auth::check(),
            'current_user_id' => Auth::id(),
            'selected_user_id' => $this->selectedUserID,
            'payment_method' => $this->payment_method,
            'upline_code' => $this->upline_code,
            'reference_code' => $this->reference_code,
        ]);

        // 1. Form Validation
        try {
            $this->validate([
                'payment_method' => ['required', 'in:Cash,GCash,Maya,Bank Transfer'],
                'amount' => ['required', 'numeric', 'in:1900'],
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

        $currentUser = Auth::user();
        $selectedUser = User::find($this->selectedUserID);

        if (!$currentUser || !$selectedUser) {
            $this->dispatch('swal:close'); // Close loading alert
            $this->dispatch('toast', type: 'error', message: 'Authentication or user error occurred.');
            return;
        }

        $cleanUplineCode = trim($this->upline_code);
        $referralUser = User::where('referralCode', $cleanUplineCode)->first();

        if (!$referralUser) {
            $this->dispatch('swal:close'); // Close loading alert
            $this->addError('upline_code', 'The referral/upline code is invalid.');
            return;
        }

        if ((int) $referralUser->UserID === (int) $selectedUser->UserID) {
            $this->dispatch('swal:close'); // Close loading alert
            $this->addError('upline_code', 'A user cannot use their own referral code.');
            return;
        }

        $existingActivation = Activation::where('UserID', $selectedUser->UserID)
            ->whereIn('status', ['Pending', 'Approved'])
            ->first();

        if ($existingActivation) {
            $this->dispatch('swal:close'); // Close loading alert
            $this->dispatch('toast', type: 'error', message: 'This account already has an active or pending activation request.');
            return;
        }

        $activationType = ($this->payment_method === 'Cash') ? 'W' : 'O';
        $paymentReference = ($this->payment_method === 'Cash') ? null : trim($this->reference_code);

        DB::beginTransaction();

        try {
            $activation = Activation::create([
                'UserID' => $selectedUser->UserID,
                'amount' => 1900.00,
                'type' => $activationType,
                'payment_method' => $this->payment_method,
                'upline_code' => $cleanUplineCode,
                'payment_reference' => $paymentReference,
                'ProcessedBy' => $currentUser->UserID,
                'status' => 'Pending',
            ]);

            if ((int) $currentUser->RoleID === 1) {
                $selectedUser->update([
                    'status' => 'Active',
                ]);
            }

            $walletAmount = $activation->amount * 0.20;

            Wallet::create([
                'UserID' => $referralUser->UserID,
                'ActivationID' => $activation->ActivationID,
                'amount' => $walletAmount,
                'status' => 'Active',
            ]);

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

            $this->closeModal();

            // 🔥 Dispatch the event to update the profile UI instantly
            $this->dispatch('account-activated');

            // 🎉 Dispatch success SweetAlert
            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Account activated successfully.',
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            $this->dispatch('swal:close');
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatch('swal:close');

            Log::error('CRITICAL ERROR: Transaction rolled back due to exception.', [
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('toast', type: 'error', message: 'Activation failed. Please try again.');
        }
    }

    public function confirmActivate(): void
    {
        // Run form validation first before prompting the sweetalert popup
        try {
            $this->validate([
                'payment_method' => ['required', 'in:Cash,GCash,Maya,Bank Transfer'],
                'amount' => ['required', 'numeric', 'in:1900'],
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
            'method' => 'activate'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */
    public function render()
    {
        return view('livewire.member-pages.my-profile.activation-modal');
    }
}
