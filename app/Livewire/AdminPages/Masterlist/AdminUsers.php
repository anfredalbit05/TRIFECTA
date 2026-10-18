<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Livewire\Concerns\AuthorizesModules;
use App\Mail\PasswordResetConfirmation;
use App\Models\Ledger;
use App\Models\Module;
use App\Models\Privilege;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Support\Modules;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class AdminUsers extends Component
{
    use AuthorizesModules;
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // =====================================================
    // Table state
    // =====================================================

    public string $search = '';

    // =====================================================
    // Modal visibility flags
    // =====================================================

    public bool $isViewModalOpen = false;

    public bool $isAddModalOpen = false;

    public bool $isEditModalOpen = false;

    public bool $isAccessModalOpen = false;

    public bool $isDeleteModalOpen = false;

    public bool $isWithdrawalModalOpen = false;

    // =====================================================
    // Active record
    // =====================================================

    public ?int $selectedUserId = null;

    // =====================================================
    // Add / Edit form fields
    // =====================================================

    public string $firstname = '';

    public string $middlename = '';

    public string $lastname = '';

    public string $username = '';

    public string $email = '';

    public string $password = '';

    public string $contactno = '';

    public string $gender = '';

    public string $birthdate = '';

    public string $address = '';

    public string $status = 'Inactive';

    public string $RoleID = '';

    public string $position = '';

    public string $referralCode = '';

    // =====================================================
    // Access-control matrix
    // =====================================================

    public array $userAccess = [];

    // =====================================================
    // Withdrawal Approval State
    //
    // The actual withdrawal form is handled by:
    // App\Livewire\Components\WithdrawalModal
    // =====================================================

    public ?int $selectedWithdrawalUserId = null;

    public ?User $selectedWithdrawalUser = null;

    public ?Withdrawal $selectedWithdrawal = null;

    public function mount(): void
    {
        $this->initializeAccessMatrix();
    }

    /*
    |----------------------------------------------------------------------
    | Permissions
    |----------------------------------------------------------------------
    */

    /*
    |----------------------------------------------------------------------
    | Permissions — global module system (Masterlist - Admin Users)
    |----------------------------------------------------------------------
    */

    protected function module(): int
    {
        return Modules::ADMIN_USERS;
    }

    // ==========================================
    // WITHDRAWAL GLOBAL STATUS
    //
    // Mirrors WithdrawalModal::isWithdrawalActive()
    // and Dashboard's own check. Used here to hide
    // the "Withdraw" creation button in the table
    // when withdrawals are turned off system-wide —
    // matching the dashboard's behavior instead of
    // only blocking it deeper inside the modal.
    // ==========================================

    public function isWithdrawalActive(): bool
    {
        return filter_var(
            Setting::where('name', 'activate_withdrawal')
                ->value('value'),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    /*
    |----------------------------------------------------------------------
    | Computed Properties
    |----------------------------------------------------------------------
    */

    public function getSelectedUserProperty(): ?User
    {
        return $this->selectedUserId
            ? User::where('UserID', $this->selectedUserId)->first()
            : null;
    }

    /*
    |----------------------------------------------------------------------
    | Search / Table
    |----------------------------------------------------------------------
    */

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function resetTable(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    /*
    |----------------------------------------------------------------------
    | View User
    |----------------------------------------------------------------------
    */

    public function viewUser(User $user): void
    {
        Log::info("Viewing user with ID: {$user->UserID}");

        $this->selectedUserId = $user->UserID;
        $this->isViewModalOpen = true;
    }

    /*
    |----------------------------------------------------------------------
    | Add / Edit User
    |----------------------------------------------------------------------
    */

    private function generateUplineCode(): string
    {
        return strtoupper(
            substr(
                md5(uniqid(mt_rand(), true)),
                0,
                6
            )
        );
    }

    private function resetUserForm(): void
    {
        $this->reset([
            'firstname',
            'middlename',
            'lastname',
            'email',
            'username',
            'password',
            'contactno',
            'gender',
            'birthdate',
            'address',
            'status',
            'RoleID',
            'position',
            'selectedUserId',
        ]);
    }

    public function openAddModal(): void
    {
        Log::info('Opening add user modal');

        $this->resetUserForm();

        $this->status = 'Active';
        $this->isAddModalOpen = true;
    }

    public function storeUser(): void
    {
        $this->validate([
            'firstname' => 'required|string|max:100',
            'middlename' => 'nullable|string|max:100',
            'lastname' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email|max:255',
            'username' => 'required|string|unique:users,username|max:50',
            'password' => 'required|min:6',
            'contactno' => 'nullable|string|max:20',
            'gender' => 'nullable|in:Male,Female,Other',
            'birthdate' => 'nullable|date',
            'address' => 'nullable|string',
            'RoleID' => 'required|exists:roles,RoleID',
            'position' => 'nullable|string|max:255',
            'status' => 'required|in:Active,Inactive,Suspended',
        ]);

        User::create([
            'firstname' => $this->firstname,
            'middlename' => $this->middlename ?: null,
            'lastname' => $this->lastname,
            'email' => $this->email ?: null,
            'username' => $this->username,
            'password' => $this->password,
            'contactno' => $this->contactno ?: null,
            'gender' => $this->gender ?: null,
            'birthdate' => $this->birthdate ?: null,
            'address' => $this->address ?: null,
            'RoleID' => $this->RoleID,
            'position' => $this->position ?: 'client',
            'status' => $this->status,
            'referralCode' => $this->generateUplineCode(),
        ]);

        session()->flash(
            'success',
            'User successfully created!'
        );

        $this->resetUserForm();

        $this->status = 'Active';
        $this->isAddModalOpen = false;
    }

    public function editUser(User $user): void
    {
        Log::info("Initiating edit for user ID: {$user->UserID}");

        $this->selectedUserId = $user->UserID;
        $this->firstname = $user->firstname ?? '';
        $this->middlename = $user->middlename ?? '';
        $this->lastname = $user->lastname ?? '';
        $this->username = $user->username ?? '';
        $this->email = $user->email ?? '';
        $this->contactno = $user->contactno ?? '';
        $this->gender = $user->gender ?? '';

        $this->birthdate = $user->birthdate
            ? Carbon::parse($user->birthdate)
                ->format('Y-m-d')
            : '';

        $this->address = $user->address ?? '';
        $this->status = $user->status ?? 'Inactive';
        $this->RoleID = $user->RoleID ?? '';
        $this->position = $user->position ?? '';

        $this->isEditModalOpen = true;
    }

    public function updateUser(): void
    {
        Log::info(
            "Attempting to update user ID: {$this->selectedUserId}"
        );

        $this->validate([
            'firstname' => 'required|string|max:100',
            'middlename' => 'nullable|string|max:100',
            'lastname' => 'required|string|max:100',

            'username' => 'required|string|max:50|unique:users,username,'
                .$this->selectedUserId
                .',UserID',

            'contactno' => 'nullable|string|max:20',
            'gender' => 'nullable|in:Male,Female,Other',
            'birthdate' => 'nullable|date',
            'address' => 'nullable|string',

            'RoleID' => 'required|exists:roles,RoleID',

            'position' => 'nullable|string|max:255',

            'status' => 'required|in:Active,Inactive,Suspended',
        ]);

        $user = User::find($this->selectedUserId);

        if (! $user) {
            Log::warning(
                "updateUser: no user found for ID {$this->selectedUserId}"
            );

            $this->isEditModalOpen = false;

            return;
        }

        $user->update([
            'firstname' => $this->firstname,

            'middlename' => $this->middlename !== ''
                    ? $this->middlename
                    : null,

            'lastname' => $this->lastname,

            'username' => $this->username,

            'contactno' => $this->contactno !== ''
                    ? $this->contactno
                    : null,

            'gender' => $this->gender !== ''
                    ? $this->gender
                    : null,

            'birthdate' => $this->birthdate !== ''
                    ? $this->birthdate
                    : null,

            'address' => $this->address !== ''
                    ? $this->address
                    : null,

            'status' => $this->status,

            'RoleID' => $this->RoleID,

            'position' => $this->position !== ''
                    ? $this->position
                    : null,
        ]);

        Log::info(
            "Successfully updated user ID: {$this->selectedUserId}"
        );

        $this->isEditModalOpen = false;

        session()->flash(
            'success',
            'User updated successfully.'
        );
    }

    public function closeModal(): void
    {
        $this->resetUserForm();

        $this->isAddModalOpen = false;
        $this->isEditModalOpen = false;
    }

    /*
    |----------------------------------------------------------------------
    | Set New Password (Admin-Forced Password Reset)
    |
    | Mirrors the Members table flow so staff/admin accounts can have
    | their passwords reset the same way — same policy, same branded
    | confirmation email carrying the new password.
    |----------------------------------------------------------------------
    */

    /**
     * The active password policy, read from the SAME rule that self-service
     * resets use (Password::default()), so the admin dialog always matches
     * the sign-up / reset requirements for the current environment.
     */
    private function passwordPolicy(): array
    {
        $applied = Password::default()->appliedRules();

        return [
            'min' => (int) $applied['min'],
            'mixedCase' => (bool) $applied['mixedCase'],
            'letters' => (bool) $applied['letters'],
            'numbers' => (bool) $applied['numbers'],
            'symbols' => (bool) $applied['symbols'],
            'uncompromised' => (bool) $applied['uncompromised'],
        ];
    }

    private function passwordPolicyHint(array $policy): string
    {
        $parts = ['at least '.$policy['min'].' characters'];

        if ($policy['mixedCase']) {
            $parts[] = 'uppercase and lowercase letters';
        } elseif ($policy['letters']) {
            $parts[] = 'letters';
        }

        if ($policy['numbers']) {
            $parts[] = 'numbers';
        }

        if ($policy['symbols']) {
            $parts[] = 'symbols';
        }

        return 'Must contain '.implode(', ', $parts).'.';
    }

    public function promptUserPasswordReset($id): void
    {
        if (! $this->canUpdate) {
            $this->dispatch(
                'user-password-error',
                message: 'You do not have permission to reset user passwords.'
            );

            return;
        }

        $user = User::find($id);

        if (! $user) {
            $this->dispatch(
                'user-password-error',
                message: 'User not found.'
            );

            return;
        }

        $policy = $this->passwordPolicy();

        $this->dispatch(
            'set-user-password',
            id: $user->UserID,
            name: trim(
                ($user->firstname ?? '').' '.($user->lastname ?? '')
            ),
            policy: $policy,
            hint: $this->passwordPolicyHint($policy),
        );
    }

    /**
     * Persist the new password (called from the SweetAlert confirm dialog).
     * Guarded server-side: permissions, existence, and password rules are
     * re-validated here so this can never be triggered without authorization.
     */
    public function resetUserPassword($id, $password): void
    {
        if (! $this->canUpdate) {
            $this->dispatch(
                'user-password-error',
                message: 'You do not have permission to reset user passwords.'
            );

            return;
        }

        $user = User::find($id);

        if (! $user) {
            $this->dispatch(
                'user-password-error',
                message: 'User not found.'
            );

            return;
        }

        $password = (string) $password;

        // Validate against the exact same rule used by self-service resets
        // (Password::default()) instead of a hardcoded minimum.
        $validator = Validator::make(
            ['password' => $password],
            ['password' => ['required', 'string', Password::default()]]
        );

        if ($validator->fails()) {
            $this->dispatch(
                'user-password-error',
                message: $validator->errors()->first('password')
            );

            return;
        }

        try {
            // The User model casts 'password' as 'hashed', so it is
            // automatically bcrypt-hashed before it hits the database.
            $user->update([
                'password' => $password,
            ]);

            Log::info('Admin reset user password', [
                'user_id' => $user->UserID,
                'reset_by' => Auth::id(),
            ]);

            // Notify the user by email when one is on file, using the same
            // branded confirmation template as the member / self-service flow.
            $emailSent = false;

            if (filled($user->email)) {
                try {
                    Mail::to($user->email)->send(
                        new PasswordResetConfirmation($user, $password)
                    );

                    $emailSent = true;
                } catch (\Throwable $e) {
                    Log::error('Failed to email user about password reset', [
                        'user_id' => $user->UserID,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $name = trim(
                ($user->firstname ?? '').' '.($user->lastname ?? '')
            );

            $text = $emailSent
                ? "{$name}'s password has been changed successfully. "
                    .'A confirmation email containing the new password was '
                    ."sent to {$user->email}."
                : "{$name}'s password has been changed successfully to: "
                    ."{$password}. No email is on file, so please share the "
                    .'new password with the user.';

            $this->dispatch('swal:success', [
                'title' => 'Password Updated!',
                'text' => $text,
            ]);
        } catch (\Throwable $e) {
            Log::error('User password reset failed', [
                'user_id' => $user->UserID,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch(
                'user-password-error',
                message: 'Something went wrong while updating the password.'
            );
        }
    }

    /*
    |----------------------------------------------------------------------
    | Delete User
    |----------------------------------------------------------------------
    */

    public function confirmDelete(User $user): void
    {
        Log::info(
            "Confirm deletion requested for user ID: {$user->UserID}"
        );

        $this->selectedUserId = $user->UserID;
        $this->isDeleteModalOpen = true;
    }

    public function deleteUser(): void
    {
        Log::info(
            "Attempting deletion for user ID: {$this->selectedUserId}"
        );

        if ($this->selectedUser) {

            $userId = $this->selectedUserId;

            $this->selectedUser->delete();

            Log::info(
                "Successfully deleted user ID: {$userId}"
            );
        }

        $this->isDeleteModalOpen = false;

        session()->flash(
            'message',
            'User deleted successfully.'
        );
    }

    /*
    |----------------------------------------------------------------------
    | Access Control Matrix
    |----------------------------------------------------------------------
    */

    private function initializeAccessMatrix(): void
    {
        /* Ordered by ModuleID (the seeded 1-15 order), not alphabetical,
           so the matrix rows match the modules table exactly. */
        $modules = Module::orderBy('ModuleID')
            ->pluck('modulename', 'ModuleID')
            ->toArray();

        if (empty($modules)) {
            $modules = [
                1 => 'Masterlist - Admin Users',
                2 => 'Masterlist - Members',
                3 => 'Masterlist - Satellite Members',
                4 => 'Masterlist - Branches',
                5 => 'Masterlist - Benefits',
                6 => 'Masterlist - FAQs',
                7 => 'Masterlist - Inquiries',
                8 => 'Transaction List - Withdrawal',
                9 => 'Transaction List - Activations',
                10 => 'Post - News & Update',
                11 => 'Post - Testimonials',
                12 => 'Report & Settings - General',
                13 => 'Report & Settings - Users Reports',
                14 => 'Report & Settings - Ledgers',
                15 => 'Report & Settings - Pangkabuhayan',
            ];
        }

        foreach ($modules as $module) {

            $this->userAccess[$module] = [
                'create' => false,
                'retrieve' => false,
                'update' => false,
                'delete' => false,
                'status' => false,
                'printall' => false,
            ];
        }
    }

    public function openAccessModal(User $user): void
    {
        Log::info(
            "Opening access modal for user ID: {$user->UserID}"
        );

        $this->selectedUserId = $user->UserID;

        $this->initializeAccessMatrix();

        if ($user->RoleID) {

            $privileges = Privilege::where(
                'RoleID',
                $user->RoleID
            )
                ->with('module')
                ->get();

            foreach ($privileges as $privilege) {

                if (! $privilege->module) {
                    continue;
                }

                $moduleName =
                    $privilege->module->modulename;

                if (isset($this->userAccess[$moduleName])) {

                    $this->userAccess[$moduleName] = [

                        'create' => (bool) $privilege->can_create,

                        'retrieve' => (bool) $privilege->can_read,

                        'update' => (bool) $privilege->can_update,

                        'delete' => (bool) $privilege->can_delete,

                        'status' => (bool) $privilege->can_change_status,

                        'printall' => (bool) $privilege->can_print,
                    ];
                }
            }
        }

        $this->isAccessModalOpen = true;
    }

    public function saveAccess(): void
    {
        Log::info(
            "Saving access permissions matrix for user ID: {$this->selectedUserId}"
        );

        $user = $this->selectedUser;

        if (! $user || ! $user->RoleID) {

            Log::warning(
                'saveAccess: User or RoleID not found.'
            );

            return;
        }

        foreach (
            $this->userAccess as $moduleName => $permissions
        ) {

            $module = Module::where(
                'modulename',
                $moduleName
            )->first();

            if (! $module) {
                continue;
            }

            Privilege::updateOrCreate(

                [
                    'RoleID' => $user->RoleID,

                    'ModuleID' => $module->ModuleID,
                ],

                [
                    'can_create' => $permissions['create'],

                    'can_read' => $permissions['retrieve'],

                    'can_update' => $permissions['update'],

                    'can_delete' => $permissions['delete'],

                    'can_print' => $permissions['printall'],

                    'can_change_status' => $permissions['status'],
                ]
            );
        }

        $this->isAccessModalOpen = false;

        session()->flash(
            'message',
            'User access permissions updated successfully.'
        );
    }

    /*
    |----------------------------------------------------------------------
    | Withdrawals
    |
    | Withdrawal creation:
    | Handled by the reusable WithdrawalModal component.
    |
    | Withdrawal approval/release:
    | Handled here by AdminUsers.
    |----------------------------------------------------------------------
    */

    public function openWithdrawalModal(int $userId): void
    {
        if (! $this->canCreate) {

            session()->flash(
                'error',
                'You do not have permission to create a withdrawal request.'
            );

            return;
        }

        $user = User::find($userId);

        if (! $user) {

            session()->flash(
                'error',
                'User not found.'
            );

            return;
        }

        // Tell the reusable WithdrawalModal
        // to use this selected user's wallet.
        $this->dispatch(
            'open-admin-withdrawal-modal',
            userID: $user->UserID
        );
    }

    /*
    |----------------------------------------------------------------------
    | Refresh table after a withdrawal is created
    |
    | WithdrawalModal is a separate Livewire component
    | (used here in admin mode, and on the member
    | dashboard on its own). When it dispatches
    | 'withdrawal-submitted', this listener re-renders
    | AdminUsers so the Pending/Withdraw badge for the
    | affected row updates without a manual refresh.
    |----------------------------------------------------------------------
    */

    #[On('withdrawal-submitted')]
    public function onWithdrawalSubmittedElsewhere(): void
    {
        Log::info('AdminUsers: withdrawal-submitted event received, refreshing table.');

        // No state to update here — render() re-queries
        // has_pending_withdrawal on every request, so
        // simply being triggered by this event is enough
        // to reflect the new badge on next paint.
    }

    /*
    |----------------------------------------------------------------------
    | Open Pending Withdrawal Approval
    |----------------------------------------------------------------------
    */

    public function openWithdrawalApproval(int $userId): void
    {
        if (! $this->canUpdate) {

            session()->flash(
                'error',
                'You do not have permission to approve withdrawals.'
            );

            return;
        }

        $user = User::find($userId);

        if (! $user) {

            session()->flash(
                'error',
                'User not found.'
            );

            return;
        }

        $withdrawal = $user->withdrawals()
            ->where('status', 'Pending')
            ->latest('WithdrawID')
            ->first();

        if (! $withdrawal) {

            session()->flash(
                'message',
                'No pending withdrawal request found.'
            );

            return;
        }

        $this->selectedWithdrawalUserId =
            $user->UserID;

        $this->selectedWithdrawalUser =
            $user;

        $this->selectedWithdrawal =
            $withdrawal;

        $this->isWithdrawalModalOpen =
            true;
    }

    /*
    |----------------------------------------------------------------------
    | Close Approval Modal
    |----------------------------------------------------------------------
    */

    public function closeWithdrawalModal(): void
    {
        $this->isWithdrawalModalOpen = false;

        $this->selectedWithdrawalUserId = null;
        $this->selectedWithdrawalUser = null;
        $this->selectedWithdrawal = null;
    }

    /*
    |--------------------------------------------------------------------------
    | Confirm Withdrawal Rejection
    |--------------------------------------------------------------------------
    */

    public function confirmWithdrawalRejection(): void
    {
        if (! $this->selectedWithdrawal) {

            session()->flash(
                'error',
                'No withdrawal request selected.'
            );

            return;
        }

        $this->dispatch(
            'confirm-withdrawal-rejection',
            amount: number_format(
                $this->selectedWithdrawal->amount,
                2
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Reject Withdrawal
    |--------------------------------------------------------------------------
    */

    public function processWithdrawalRejection(): void
    {
        // ==========================================
        // PERMISSION CHECK
        // ==========================================

        if (! $this->canUpdate) {

            $this->dispatch(
                'withdrawal-rejected-error',
                message: 'You do not have permission to reject withdrawals.'
            );

            return;
        }

        if (! $this->selectedWithdrawal) {

            $this->dispatch(
                'withdrawal-rejected-error',
                message: 'No withdrawal request selected.'
            );

            return;
        }

        // ==========================================
        // GET AUTHENTICATED USER
        // ==========================================

        $confirmedBy = Auth::user()?->UserID;

        if (! $confirmedBy) {

            Log::error(
                'Withdrawal rejection failed: No authenticated user found.'
            );

            $this->dispatch(
                'withdrawal-rejected-error',
                message: 'Authenticated user not found.'
            );

            return;
        }

        try {

            DB::transaction(function () use ($confirmedBy) {

                $withdrawal = Withdrawal::where(
                    'WithdrawID',
                    $this->selectedWithdrawal->WithdrawID
                )
                    ->lockForUpdate()
                    ->first();

                // ==========================================
                // MAKE SURE STILL PENDING
                // ==========================================

                if (
                    ! $withdrawal ||
                    $withdrawal->status !== 'Pending'
                ) {

                    throw new \Exception(
                        'Withdrawal is no longer pending.'
                    );
                }

                // ==========================================
                // REJECT WITHDRAWAL
                // ==========================================

                $withdrawal->update([
                    'status' => 'Rejected',

                    'confirmed_by' => $confirmedBy,
                ]);
            });

            // ==========================================
            // CLOSE MODAL
            // ==========================================

            $this->closeWithdrawalModal();

            // ==========================================
            // SUCCESS ALERT
            // ==========================================

            $this->dispatch(
                'withdrawal-rejected-success'
            );

        } catch (\Exception $e) {

            report($e);

            Log::error(
                'Withdrawal rejection failed: '.$e->getMessage()
            );

            $this->dispatch(
                'withdrawal-rejected-error',
                message: 'Something went wrong while rejecting the withdrawal.'
            );
        }
    }

    /*
    |----------------------------------------------------------------------
    | Withdrawal Approval / Rejection Event Listeners
    |----------------------------------------------------------------------
    */

    #[On('process-withdrawal-approval')]
    public function handleWithdrawalApproval(): void
    {
        $this->processWithdrawalApproval();
    }

    #[On('process-withdrawal-rejection')]
    public function handleWithdrawalRejection(): void
    {
        $this->processWithdrawalRejection();
    }

    /*
    |--------------------------------------------------------------------------
    | Confirm Withdrawal Approval
    |--------------------------------------------------------------------------
    */

    public function confirmWithdrawalApproval(): void
    {
        if (! $this->selectedWithdrawal) {
            session()->flash(
                'error',
                'No withdrawal request selected.'
            );

            return;
        }

        $this->dispatch(
            'confirm-withdrawal-approval',
            amount: number_format(
                $this->selectedWithdrawal->amount,
                2
            )
        );
    }

    /*
    |----------------------------------------------------------------------
    | Approve / Release Withdrawal
    |----------------------------------------------------------------------
    */

    public function processWithdrawalApproval(): void
    {
        // ==========================================
        // PERMISSION CHECK
        // ==========================================

        if (! $this->canUpdate) {

            $this->dispatch(
                'withdrawal-approved-error',
                message: 'You do not have permission to approve withdrawals.'
            );

            return;
        }

        if (! $this->selectedWithdrawal) {

            $this->dispatch(
                'withdrawal-approved-error',
                message: 'No withdrawal request selected.'
            );

            return;
        }

        // ==========================================
        // GET CURRENT AUTHENTICATED USER
        // ==========================================

        $confirmedBy = Auth::user()?->UserID;

        if (! $confirmedBy) {

            Log::error('Withdrawal approval failed: No authenticated user found.', [
                'auth_user' => Auth::user(),
                'auth_id' => Auth::id(),
            ]);

            $this->dispatch(
                'withdrawal-approved-error',
                message: 'Authenticated user not found.'
            );

            return;
        }

        try {

            DB::transaction(function () use ($confirmedBy) {

                $withdrawal = Withdrawal::where(
                    'WithdrawID',
                    $this->selectedWithdrawal->WithdrawID
                )
                    ->lockForUpdate()
                    ->first();

                if (
                    ! $withdrawal ||
                    $withdrawal->status !== 'Pending'
                ) {
                    throw new \Exception(
                        'Withdrawal is no longer pending.'
                    );
                }

                $withdrawalAmount = (float) $withdrawal->amount;
                $taxAmount = (float) $withdrawal->tax_amount;
                $requestedAmount = (float) $withdrawal->requested_amount;

                // ==========================================
                // APPROVE WITHDRAWAL
                // ==========================================

                $withdrawal->update([
                    'status' => 'Approved',

                    'confirmed_by' => $confirmedBy,
                ]);

                Wallet::create([
                    'UserID' => $withdrawal->UserID,

                    'withdrawID' => $withdrawal->WithdrawID,

                    'amount' => -abs($requestedAmount),

                    'source' => 'WD',

                    'status' => 'Active',
                ]);

                // ==========================================
                // CREATE WITHDRAWAL LEDGER
                // ==========================================

                Ledger::create([
                    'RefID' => $withdrawal->UserID,

                    'WithdrawID' => $withdrawal->WithdrawID,

                    'amount' => -abs($withdrawalAmount),

                    'Source' => 'WD',

                    'ActivationID' => null,

                    'status' => 'Completed',
                ]);

                // ==========================================
                // CREATE TRANSACTION FEE LEDGER
                // ==========================================

                Ledger::create([
                    'RefID' => $withdrawal->UserID,

                    'WithdrawID' => $withdrawal->WithdrawID,

                    'amount' => $taxAmount,

                    'Source' => 'TX',

                    'ActivationID' => null,

                    'status' => 'Completed',
                ]);
            });

            $this->closeWithdrawalModal();

            $this->dispatch(
                'withdrawal-approved-success'
            );

        } catch (\Exception $e) {

            report($e);

            Log::error(
                'Withdrawal approval failed: '.$e->getMessage()
            );

            $this->dispatch(
                'withdrawal-approved-error',
                message: 'Something went wrong while approving the withdrawal.'
            );
        }
    }

    /*
    |----------------------------------------------------------------------
    | Render
    |----------------------------------------------------------------------
    */

    public function render()
    {
        $users = User::query()

            ->where('RoleID', '!=', 4)

            ->withExists([

                'activations as has_pending_activation' => function ($query) {

                    $query->where(
                        'status',
                        'Pending'
                    );
                },

                'withdrawals as has_pending_withdrawal' => function ($query) {

                    $query->where(
                        'status',
                        'Pending'
                    );
                },

                'withdrawals as has_approved_withdrawal' => function ($query) {

                    $query->where(
                        'status',
                        'Approved'
                    );
                },
            ])

            ->when(
                filled($this->search),
                function ($query) {

                    $search =
                        trim($this->search);

                    $query->where(
                        function ($q) use ($search) {

                            $q->where(
                                'firstname',
                                'like',
                                "%{$search}%"
                            )
                                ->orWhere(
                                    'middlename',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'lastname',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'username',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'contactno',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )

            ->latest('UserID')

            ->paginate(10);
        return view(
            'livewire.admin-pages.masterlist.admin-users',
            [
                'users' => $users,

                'isWithdrawalActive' => $this->isWithdrawalActive(),
            ]
        );
    }
}
