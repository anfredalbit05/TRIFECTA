<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Models\User;
use App\Models\Module;
use App\Models\Privilege;
use App\Models\Withdrawal;
use App\Models\Wallet;
use App\Models\Ledger;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class AdminUsers extends Component
{
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

    protected function hasPermission(string $action): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Super Admin override
        if ((int) $user->RoleID === 1) {
            return true;
        }

        $column = 'can_' . $action;

        if ($user->role && method_exists($user->role, 'permissions')) {
            $hasPermission = optional($user->role->permissions)
                ->where('ModuleID', 1)
                ->where($column, 1)
                ->isNotEmpty();

            if ($hasPermission) {
                return true;
            }
        }

        return Privilege::where('RoleID', $user->RoleID)
            ->where('ModuleID', 1)
            ->where($column, 1)
            ->exists();
    }

    public function getCanCreateProperty(): bool
    {
        return $this->hasPermission('create');
    }

    public function getCanReadProperty(): bool
    {
        return $this->hasPermission('read');
    }

    public function getCanUpdateProperty(): bool
    {
        return $this->hasPermission('update');
    }

    public function getCanDeleteProperty(): bool
    {
        return $this->hasPermission('delete');
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
            ? \Carbon\Carbon::parse($user->birthdate)
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

            'username' =>
                'required|string|max:50|unique:users,username,'
                . $this->selectedUserId
                . ',UserID',

            'contactno' => 'nullable|string|max:20',
            'gender' => 'nullable|in:Male,Female,Other',
            'birthdate' => 'nullable|date',
            'address' => 'nullable|string',

            'RoleID' => 'required|exists:roles,RoleID',

            'position' => 'nullable|string|max:255',

            'status' =>
                'required|in:Active,Inactive,Suspended',
        ]);

        $user = User::find($this->selectedUserId);

        if (!$user) {
            Log::warning(
                "updateUser: no user found for ID {$this->selectedUserId}"
            );

            $this->isEditModalOpen = false;

            return;
        }

        $user->update([
            'firstname' => $this->firstname,

            'middlename' =>
                $this->middlename !== ''
                    ? $this->middlename
                    : null,

            'lastname' => $this->lastname,

            'username' => $this->username,

            'contactno' =>
                $this->contactno !== ''
                    ? $this->contactno
                    : null,

            'gender' =>
                $this->gender !== ''
                    ? $this->gender
                    : null,

            'birthdate' =>
                $this->birthdate !== ''
                    ? $this->birthdate
                    : null,

            'address' =>
                $this->address !== ''
                    ? $this->address
                    : null,

            'status' => $this->status,

            'RoleID' => $this->RoleID,

            'position' =>
                $this->position !== ''
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
        $modules = Module::pluck('modulename')->toArray();

        if (empty($modules)) {
            $modules = [
                'Masterlist - Admin Users',
                'Masterlist - Members',
                'Masterlist - Branches',
                'Masterlist - Benefits',
                'Masterlist - Inquiries',
                'Masterlist - FAQs',
                'Post - News & Updates',
                'Post - Testimonials',
                'Report & Settings - General',
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

                if (!$privilege->module) {
                    continue;
                }

                $moduleName =
                    $privilege->module->modulename;

                if (isset($this->userAccess[$moduleName])) {

                    $this->userAccess[$moduleName] = [

                        'create' =>
                            (bool) $privilege->can_create,

                        'retrieve' =>
                            (bool) $privilege->can_read,

                        'update' =>
                            (bool) $privilege->can_update,

                        'delete' =>
                            (bool) $privilege->can_delete,

                        'status' =>
                            (bool) $privilege->can_change_status,

                        'printall' =>
                            (bool) $privilege->can_print,
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

        if (!$user || !$user->RoleID) {

            Log::warning(
                'saveAccess: User or RoleID not found.'
            );

            return;
        }

        foreach (
            $this->userAccess
            as $moduleName => $permissions
        ) {

            $module = Module::where(
                'modulename',
                $moduleName
            )->first();

            if (!$module) {
                continue;
            }

            Privilege::updateOrCreate(

                [
                    'RoleID' => $user->RoleID,

                    'ModuleID' => $module->ModuleID,
                ],

                [
                    'can_create' =>
                        $permissions['create'],

                    'can_read' =>
                        $permissions['retrieve'],

                    'can_update' =>
                        $permissions['update'],

                    'can_delete' =>
                        $permissions['delete'],

                    'can_print' =>
                        $permissions['printall'],

                    'can_change_status' =>
                        $permissions['status'],
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
        if (!$this->canCreate) {

            session()->flash(
                'error',
                'You do not have permission to create a withdrawal request.'
            );

            return;
        }

        $user = User::find($userId);

        if (!$user) {

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
        if (!$this->canUpdate) {

            session()->flash(
                'error',
                'You do not have permission to approve withdrawals.'
            );

            return;
        }

        $user = User::find($userId);

        if (!$user) {

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

        if (!$withdrawal) {

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
    |----------------------------------------------------------------------
    | Approve / Release Withdrawal
    |----------------------------------------------------------------------
    */

    public function processWithdrawalApproval(): void
    {
        if (!$this->selectedWithdrawal) {
            return;
        }

        try {

            DB::transaction(function () {

                $withdrawal =
                    Withdrawal::where(
                        'WithdrawID',
                        $this->selectedWithdrawal->WithdrawID
                    )
                        ->lockForUpdate()
                        ->first();

                if (
                    !$withdrawal
                    || $withdrawal->status !== 'Pending'
                ) {
                    throw new \Exception(
                        'Withdrawal is no longer pending.'
                    );
                }

                // ==========================================
                // APPROVE / RELEASE WITHDRAWAL
                // ==========================================

                $withdrawal->update([
                    'status' => 'Approved',

                    'ProcessedBy' =>
                        Auth::user()?->UserID,
                ]);

                // ==========================================
                // DEDUCT FROM WALLET
                //
                // Negative wallet entry keeps the wallet
                // history intact.
                // ==========================================

                Wallet::create([
                    'UserID' =>
                        $withdrawal->UserID,

                    'withdrawID' =>
                        $withdrawal->WithdrawID,

                    'amount' =>
                        -abs((float) $withdrawal->amount),

                    'source' =>
                        'WD',

                    'status' =>
                        'Active',
                ]);

                // ==========================================
                // COMPLETE LEDGER ENTRY
                // ==========================================

                Ledger::where(
                    'WithdrawID',
                    $withdrawal->WithdrawID
                )->update([
                    'status' => 'Completed',
                ]);
            });

            session()->flash(
                'success',
                'Withdrawal approved, released, and deducted from the wallet successfully.'
            );

        } catch (\Exception $e) {

            report($e);

            session()->flash(
                'error',
                'Something went wrong while approving the withdrawal.'
            );
        }

        $this->closeWithdrawalModal();
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

                'activations as has_pending_activation'
                    => function ($query) {

                    $query->where(
                        'status',
                        'Pending'
                    );
                },

                'withdrawals as has_pending_withdrawal'
                    => function ($query) {

                    $query->where(
                        'status',
                        'Pending'
                    );
                },

                'withdrawals as has_approved_withdrawal'
                    => function ($query) {

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
