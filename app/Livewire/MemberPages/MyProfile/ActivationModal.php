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
    public string $payment_method = ''; // Cash, GCash, Bank Transfer
    public string $amount = '1900';    // Fixed activation amount
    public string $referral_code = ''; // User-entered referral/upline code
    public string $reference_code = '';// GCash or Bank transaction reference

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
    public function openModal($UserID): void
    {
        Log::info('Activation modal opened', [
            'selected_UserID' => $UserID,
            'opened_by' => Auth::id(),
        ]);

        $this->selectedUserID = $UserID;
        $this->resetValidation();

        $this->payment_method = '';
        $this->amount = '1900';
        $this->referral_code = '';
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
    public function viewActivation($UserID): void
    {
        $this->selectedUserID = $UserID;

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
            'referral_code',
            'reference_code',
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
    public function activate(): void
    {
        Log::info('Activation button clicked.', [
            'authenticated' => Auth::check(),
            'current_user_id' => Auth::id(),
            'payment_method' => $this->payment_method,
            'referral_code' => $this->referral_code,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Form Validation
        |--------------------------------------------------------------------------
        */
        $this->validate([
            'payment_method' => ['required', 'in:Cash,GCash,Bank Transfer'],
            'amount' => ['required', 'numeric', 'in:1900'],
            'referral_code' => ['required', 'string', 'max:100'],
            'reference_code' => ['nullable', 'string', 'max:100', 'required_unless:payment_method,Cash'],
            'beneficiaries' => ['required', 'array', 'max:3'],
            'beneficiaries.*.fullname' => ['nullable', 'string', 'max:150'],
            'beneficiaries.*.birthdate' => ['nullable', 'date'],
            'beneficiaries.*.relationship' => ['nullable', 'string', 'max:50'],
        ]);

        Log::info('Activation validation passed.');

        /*
        |--------------------------------------------------------------------------
        | Check Current Logged-In User & Target User
        |--------------------------------------------------------------------------
        */
        /** @var User|null $currentUser */
        $currentUser = Auth::user();

        if (!$currentUser) {
            Log::warning('Activation failed: no authenticated user.');
            $this->dispatch('toast', type: 'error', message: 'You must be logged in to activate your account.');
            return;
        }

        $selectedUser = User::find($this->selectedUserID);

        if (!$selectedUser) {
            $this->dispatch('toast', type: 'error', message: 'The selected member could not be found.');
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Find User From Referral Code
        |--------------------------------------------------------------------------
        */
        $cleanReferralCode = trim($this->referral_code);

        $referralUser = User::where('uplineCode', $cleanReferralCode)->first();

        if (!$referralUser) {
            Log::warning('Invalid referral code.', [
                'current_user_id' => $currentUser->UserID,
                'referral_code' => $cleanReferralCode,
            ]);

            $this->addError('referral_code', 'The referral code is invalid.');
            return;
        }

        Log::info('Referral user found.', [
            'referral_user_id' => $referralUser->UserID,
            'current_user_id' => $currentUser->UserID,
        ]);

        // Prevent self-referral
        if ((int) $referralUser->UserID === (int) $currentUser->UserID) {
            $this->addError('referral_code', 'You cannot use your own referral code.');
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Activation Requests
        |--------------------------------------------------------------------------
        */
        $existingActivation = Activation::where('ProcessedBy', $currentUser->UserID)
            ->whereIn('status', ['Pending', 'Approved'])
            ->first();

        if ($existingActivation) {
            Log::warning('Duplicate activation prevented.', [
                'ProcessedBy' => $currentUser->UserID,
                'ActivationID' => $existingActivation->ActivationID,
                'status' => $existingActivation->status,
            ]);

            $this->dispatch('toast', type: 'error', message: 'You already have an existing activation request.');
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Helper Parameters
        |--------------------------------------------------------------------------
        */
        $activationType = ($this->payment_method === 'Cash') ? 'W' : 'O';
        $paymentReference = ($this->payment_method === 'Cash') ? null : trim($this->reference_code);

        DB::beginTransaction();

        try {
            // Create Activation Record
            Log::info('Creating activation record.', [
                'UserID' => $referralUser->UserID,
                'ProcessedBy' => $currentUser->UserID,
            ]);

            $activation = Activation::create([
                'UserID' => $referralUser->UserID,
                'amount' => 1900.00,
                'type' => $activationType,
                'payment_method' => $this->payment_method,
                'referral_code' => $cleanReferralCode,
                'payment_reference' => $paymentReference,
                'ProcessedBy' => $currentUser->UserID,
                'status' => 'Pending',
            ]);

            Log::info('Activation record created.', [
                'ActivationID' => $activation->ActivationID,
            ]);

            // Auto-activate selected user if processed by Admin (RoleID = 1)
            if ((int) $currentUser->RoleID === 1) {
                User::where('UserID', $this->selectedUserID)->update([
                    'status' => 'Active',
                ]);

                Log::info('Selected user status updated by admin.', [
                    'activated_user_id' => $this->selectedUserID,
                    'processed_by_admin' => $currentUser->UserID,
                ]);
            }

            // Create Wallet Commission (20%)
            $walletAmount = $activation->amount * 0.20;

            Wallet::create([
                'UserID' => $activation->UserID,
                'ActivationID' => $activation->ActivationID,
                'amount' => $walletAmount,
                'status' => 'Active',
            ]);

            // Save Beneficiaries
            foreach ($this->beneficiaries as $index => $beneficiary) {
                $fullname = trim($beneficiary['fullname'] ?? '');
                $birthdate = trim($beneficiary['birthdate'] ?? '');
                $relationship = trim($beneficiary['relationship'] ?? '');

                $hasData = ($fullname !== '' || $birthdate !== '' || $relationship !== '');

                if (!$hasData) {
                    continue;
                }

                if ($fullname === '' || $birthdate === '' || $relationship === '') {
                    throw ValidationException::withMessages([
                        "beneficiaries.$index.fullname" => 'Full name is required.',
                        "beneficiaries.$index.birthdate" => 'Birthdate is required.',
                        "beneficiaries.$index.relationship" => 'Relationship is required.',
                    ]);
                }

                $existingBeneficiary = Beneficiary::where('UserID', $currentUser->UserID)
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
                        'UserID' => $currentUser->UserID,
                        'fullname' => $fullname,
                        'birthdate' => $birthdate,
                        'relationship' => $relationship,
                        'status' => true,
                    ]);
                }
            }

            DB::commit();

            Log::info('Activation completed successfully.', [
                'UserID' => $referralUser->UserID,
                'ProcessedBy' => $currentUser->UserID,
                'ActivationID' => $activation->ActivationID,
                'payment_method' => $this->payment_method,
                'type' => $activationType,
            ]);

            $this->dispatch('toast', type: 'success', message: 'Account activated successfully.');
            $this->closeModal();

        } catch (ValidationException $e) {
            DB::rollBack();

            Log::warning('Activation validation failed.', [
                'current_user_id' => $currentUser->UserID,
                'errors' => $e->errors(),
            ]);

            throw $e;

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Activation failed.', [
                'current_user_id' => $currentUser->UserID,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $this->dispatch('toast', type: 'error', message: 'Activation failed. Please try again.');
        }
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
