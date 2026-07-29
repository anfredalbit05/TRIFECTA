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
    public function activate(): void
    {
        Log::info('--- ACTIVATION PROCESS STARTED ---', [
            'authenticated' => Auth::check(),
            'current_user_id' => Auth::id(),
            'selected_user_id' => $this->selectedUserID,
            'payment_method' => $this->payment_method,
            'upline_code' => $this->upline_code,
            'reference_code' => $this->reference_code,
        ]);

        // 1. Form Validation (Added Maya to allowed payment methods)
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
            Log::info('Step 1: Form validation passed successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Step 1 FAILED: Form validation errors encountered.', [
                'errors' => $e->errors()
            ]);
            throw $e;
        }

        /** @var User|null $currentUser */
        $currentUser = Auth::user();

        if (!$currentUser) {
            Log::error('Step 2 FAILED: No authenticated user found.');
            $this->dispatch('toast', type: 'error', message: 'You must be logged in to activate an account.');
            return;
        }

        $selectedUser = User::find($this->selectedUserID);

        if (!$selectedUser) {
            Log::error('Step 2 FAILED: Selected user record not found.', ['selectedUserID' => $this->selectedUserID]);
            $this->dispatch('toast', type: 'error', message: 'The selected member could not be found.');
            return;
        }
        Log::info('Step 2: Current and target users verified.', [
            'current_user_id' => $currentUser->UserID,
            'target_user_id' => $selectedUser->UserID
        ]);

        // 2. Find Upline/Referrer User from the entered upline code matching referral_code
       // 2. Find Upline/Referrer User from the entered upline code matching referralCode
        $cleanUplineCode = trim($this->upline_code);
        $referralUser = User::where('referralCode', $cleanUplineCode)->first();

        if (!$referralUser) {
            Log::warning('Step 3 FAILED: Referral/upline code does not exist in users table.', [
                'upline_code' => $cleanUplineCode
            ]);

            $this->addError('upline_code', 'The referral/upline code is invalid.');
            return;
        }

        // Prevent self-referral
        if ((int) $referralUser->UserID === (int) $selectedUser->UserID) {
            Log::warning('Step 3 FAILED: Attempted self-referral.', ['UserID' => $selectedUser->UserID]);
            $this->addError('upline_code', 'A user cannot use their own referral code.');
            return;
        }
        Log::info('Step 3: Referral user successfully found.', ['referral_user_id' => $referralUser->UserID]);

        // 3. Prevent Duplicate Activation Requests for the Target User
        $existingActivation = Activation::where('UserID', $selectedUser->UserID)
            ->whereIn('status', ['Pending', 'Approved'])
            ->first();

        if ($existingActivation) {
            Log::warning('Step 4 FAILED: Duplicate active/pending activation found.', [
                'UserID' => $selectedUser->UserID,
                'ActivationID' => $existingActivation->ActivationID,
                'status' => $existingActivation->status
            ]);

            $this->dispatch('toast', type: 'error', message: 'This account already has an active or pending activation request.');
            return;
        }
        Log::info('Step 4: No duplicate activations detected.');

        $activationType = ($this->payment_method === 'Cash') ? 'W' : 'O';
        $paymentReference = ($this->payment_method === 'Cash') ? null : trim($this->reference_code);

        DB::beginTransaction();
        Log::info('Step 5: Database transaction started.');

        Log::info('Activation data', [
    'data' => [
        'UserID' => $selectedUser->UserID,
        'amount' => 1900,
        'type' => $activationType,
        'payment_method' => $this->payment_method,
        'upline_code' => $cleanUplineCode,
        'payment_reference' => $paymentReference,
        'ProcessedBy' => $currentUser->UserID,
        'status' => 'Pending',
    ]
]);

        try {
            // 4. Create Activation Record assigned to the Target User
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

            Log::info('Step 6: Activation record created.', ['ActivationID' => $activation->ActivationID]);

            // Auto-activate selected user if processed by Admin (RoleID = 1)
            if ((int) $currentUser->RoleID === 1) {
                $selectedUser->update([
                    'status' => 'Active',
                ]);
                Log::info('Step 6.1: Target user status auto-updated to Active by Admin.');
            }

            // 5. Create Wallet Commission (20%) for the Upline/Referral User
            $walletAmount = $activation->amount * 0.20;

            $wallet = Wallet::create([
                'UserID' => $referralUser->UserID,
                'ActivationID' => $activation->ActivationID,
                'amount' => $walletAmount,
                'status' => 'Active',
            ]);
            Log::info('Step 7: Wallet commission created.', ['WalletID' => $wallet->WalletID ?? null, 'amount' => $walletAmount]);

            // 6. Save Beneficiaries to the Target Selected User
            foreach ($this->beneficiaries as $index => $beneficiary) {
                $fullname = trim($beneficiary['fullname'] ?? '');
                $birthdate = trim($beneficiary['birthdate'] ?? '');
                $relationship = trim($beneficiary['relationship'] ?? '');

                $hasData = ($fullname !== '' || $birthdate !== '' || $relationship !== '');

                if (!$hasData) {
                    continue;
                }

                if ($fullname === '' || $birthdate === '' || $relationship === '') {
                    Log::warning("Step 8 FAILED: Incomplete beneficiary row at index {$index}");
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
            Log::info('Step 8: Beneficiaries processed successfully.');

            DB::commit();
            Log::info('--- TRANSACTION COMMITTED SUCCESSFULLY ---', [
                'ActivationID' => $activation->ActivationID
            ]);

            $this->dispatch('toast', type: 'success', message: 'Account activated successfully.');
            $this->closeModal();

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::warning('Transaction rolled back due to ValidationException.', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('CRITICAL ERROR: Transaction rolled back due to exception.', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('toast', type: 'error', message: 'Activation failed. Please try again.');
        }
    }

    public function confirmActivate(): void
    {
        // $this->dispatch('swal:confirm', [
        //     'title' => 'Submit Activation?',
        //     'text' => 'Please review your details before submitting your activation.',
        //     'icon' => 'question',
        //     'method' => 'activate'
        // ]);

        $this->activate();
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
