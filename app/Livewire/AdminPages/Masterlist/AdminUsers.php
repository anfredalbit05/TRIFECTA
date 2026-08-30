<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Models\User;
use App\Models\Module;
use App\Models\Privilege;
use App\Models\Role;
use App\Models\Withdrawal;
use App\Models\Wallet;
use App\Models\Ledger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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

    // Active record for Add/Edit/View/Delete/Access modals
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

    // Access-control matrix: [moduleName => [create, retrieve, update, delete, status, printall]]
    public array $userAccess = [];

    // =====================================================
    // Withdrawal modal state
    //
    // NOTE: the `withdrawals.status` column only supports
    // 'Pending', 'Approved', 'Rejected' — there is no separate
    // "Released" status. Approving a withdrawal IS the release:
    // once a request is Approved, funds are considered released
    // and no further action is needed. So the modal only ever
    // needs two modes: 'create' (new request) and 'approval'
    // (approve a pending request).
    // =====================================================
    public string $withdrawalModalMode = 'create'; // create | approval

    public ?int $selectedWithdrawalUserId = null;
    public ?User $selectedWithdrawalUser = null;
    public ?Withdrawal $selectedWithdrawal = null;
    public float $selectedWithdrawalBalance = 0;

    public string $withdrawAmount = '';
    public string $withdrawalMethod = '';
    public string $accountName = '';
    public string $accountNumber = '';
    public string $paymentReference = '';

    public function mount(): void
    {
        $this->initializeAccessMatrix();
    }

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    */

    protected function hasPermission(string $action): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Super Admin override (RoleID 1)
        if ((int) $user->RoleID === 1) {
            return true;
        }

        $column = 'can_' . $action;

        if ($user->role && method_exists($user->role, 'permissions')) {
            $hasPerm = optional($user->role->permissions)
                ->where('ModuleID', 1)
                ->where($column, 1)
                ->isNotEmpty();

            if ($hasPerm) {
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

    /*
    |--------------------------------------------------------------------------
    | Computed properties
    |--------------------------------------------------------------------------
    */

    public function getSelectedUserProperty()
    {
        return $this->selectedUserId
            ? User::where('UserID', $this->selectedUserId)->first()
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Search / table
    |--------------------------------------------------------------------------
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
    |--------------------------------------------------------------------------
    | View user
    |--------------------------------------------------------------------------
    */

    public function viewUser(User $user): void
    {
        Log::info("Viewing user with ID: {$user->UserID}");

        $this->selectedUserId = $user->UserID;
        $this->isViewModalOpen = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Add / Edit user
    |--------------------------------------------------------------------------
    */

    private function generateUplineCode(): string
    {
        return strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
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
            'password' => $this->password, // hashed via model cast
            'contactno' => $this->contactno ?: null,
            'gender' => $this->gender ?: null,
            'birthdate' => $this->birthdate ?: null,
            'address' => $this->address ?: null,
            'RoleID' => $this->RoleID,
            'position' => $this->position ?: 'client',
            'status' => $this->status,
            'referralCode' => $this->generateUplineCode(),
        ]);

        session()->flash('success', 'User successfully created!');

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
        $this->birthdate = $user->birthdate ? \Carbon\Carbon::parse($user->birthdate)->format('Y-m-d') : '';
        $this->address = $user->address ?? '';
        $this->status = $user->status ?? 'Inactive';
        $this->RoleID = $user->RoleID ?? '';
        $this->position = $user->position ?? '';

        $this->isEditModalOpen = true;
    }

    public function updateUser(): void
    {
        Log::info("Attempting to update user ID: {$this->selectedUserId}");

        $this->validate([
            'firstname' => 'required|string|max:100',
            'middlename' => 'nullable|string|max:100',
            'lastname' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $this->selectedUserId . ',UserID',
            'contactno' => 'nullable|string|max:20',
            'gender' => 'nullable|in:Male,Female,Other',
            'birthdate' => 'nullable|date',
            'address' => 'nullable|string',
            'RoleID' => 'required|exists:roles,RoleID',
            'position' => 'nullable|string|max:255',
            'status' => 'required|in:Active,Inactive,Suspended',
        ]);

        $user = User::find($this->selectedUserId);

        if (!$user) {
            Log::warning("updateUser: no user found for ID {$this->selectedUserId}");
            $this->isEditModalOpen = false;
            return;
        }

        $positionToSave = $this->position !== '' ? $this->position : null;

        $user->update([
            'firstname' => $this->firstname,
            'middlename' => $this->middlename !== '' ? $this->middlename : null,
            'lastname' => $this->lastname,
            'username' => $this->username,
            'contactno' => $this->contactno !== '' ? $this->contactno : null,
            'gender' => $this->gender !== '' ? $this->gender : null,
            'birthdate' => $this->birthdate !== '' ? $this->birthdate : null,
            'address' => $this->address !== '' ? $this->address : null,
            'status' => $this->status,
            'RoleID' => $this->RoleID,
            'position' => $positionToSave,
        ]);

        Log::info("Successfully updated user ID: {$this->selectedUserId}");

        $this->isEditModalOpen = false;
        session()->flash('success', 'User updated successfully.');
    }

    public function closeModal(): void
    {
        $this->resetUserForm();
        $this->isAddModalOpen = false;
        $this->isEditModalOpen = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Delete user
    |--------------------------------------------------------------------------
    */

    public function confirmDelete(User $user): void
    {
        Log::info("Confirm deletion requested for user ID: {$user->UserID}");

        $this->selectedUserId = $user->UserID;
        $this->isDeleteModalOpen = true;
    }

    public function deleteUser(): void
    {
        Log::info("Attempting deletion for user ID: {$this->selectedUserId}");

        if ($this->selectedUser) {
            $userId = $this->selectedUserId;
            $this->selectedUser->delete();
            Log::info("Successfully deleted user ID: {$userId}");
        }

        $this->isDeleteModalOpen = false;
        session()->flash('message', 'User deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Access control matrix
    |--------------------------------------------------------------------------
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
        Log::info("Opening access modal for user ID: {$user->UserID}");

        $this->selectedUserId = $user->UserID;
        $this->initializeAccessMatrix();

        if ($user->RoleID) {
            $privileges = Privilege::where('RoleID', $user->RoleID)->with('module')->get();

            foreach ($privileges as $privilege) {
                if ($privilege->module) {
                    $moduleName = $privilege->module->modulename;

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
        }

        $this->isAccessModalOpen = true;
    }

    public function saveAccess(): void
    {
        Log::info("Saving access permissions matrix for user ID: {$this->selectedUserId}");

        $user = $this->selectedUser;

        if (!$user || !$user->RoleID) {
            Log::warning('saveAccess: User or RoleID not found.');
            return;
        }

        foreach ($this->userAccess as $moduleName => $permissions) {
            $module = Module::where('modulename', $moduleName)->first();

            if ($module) {
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
        }

        $this->isAccessModalOpen = false;
        session()->flash('message', 'User access permissions updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Withdrawals
    |
    | Lifecycle (matches the DB enum: Pending / Approved / Rejected):
    |   1. Admin clicks "Withdraw"  -> creates a Pending request.
    |   2. Admin clicks "Pending"   -> approval modal -> Approve.
    |   3. Status becomes Approved -> this IS the released state.
    |      No further action is available once Approved.
    |--------------------------------------------------------------------------
    */

    private function resetWithdrawalForm(): void
    {
        $this->reset([
            'withdrawAmount',
            'withdrawalMethod',
            'accountName',
            'accountNumber',
            'paymentReference',
        ]);
    }

    /**
     * Cash wallet balance for a specific user (excludes PF-sourced funds).
     * Used for both the modal display and the withdrawal validation cap,
     * so the two always agree.
     */
    private function getWalletBalance(int $userId): float
    {
        return (float) Wallet::where('UserID', $userId)
            ->where('status', 'Active')
            ->where('source', '!=', 'PF')
            ->sum('amount');
    }

    public function openWithdrawalModal(int $userId): void
    {
        if (!$this->canCreate) {
            session()->flash('error', 'You do not have permission to create a withdrawal request.');
            return;
        }

        $user = User::find($userId);

        if (!$user) {
            session()->flash('error', 'User not found.');
            return;
        }

        $this->selectedWithdrawalUserId = $user->UserID;
        $this->selectedWithdrawalUser = $user;
        $this->selectedWithdrawal = null;
        $this->selectedWithdrawalBalance = $this->getWalletBalance($user->UserID);

        $this->resetWithdrawalForm();

        $this->withdrawalModalMode = 'create';
        $this->isWithdrawalModalOpen = true;
    }

    public function openWithdrawalApproval(int $userId): void
    {
        if (!$this->canUpdate) {
            session()->flash('error', 'You do not have permission to approve withdrawals.');
            return;
        }

        $user = User::find($userId);

        if (!$user) {
            session()->flash('error', 'User not found.');
            return;
        }

        $withdrawal = $user->withdrawals()
            ->where('status', 'Pending')
            ->latest()
            ->first();

        if (!$withdrawal) {
            session()->flash('message', 'No pending withdrawal request found.');
            return;
        }

        $this->selectedWithdrawalUserId = $user->UserID;
        $this->selectedWithdrawalUser = $user;
        $this->selectedWithdrawal = $withdrawal;

        $this->withdrawalModalMode = 'approval';
        $this->isWithdrawalModalOpen = true;
    }

    public function closeWithdrawalModal(): void
    {
        $this->isWithdrawalModalOpen = false;
        $this->selectedWithdrawalUserId = null;
        $this->selectedWithdrawalUser = null;
        $this->selectedWithdrawal = null;
        $this->selectedWithdrawalBalance = 0;
        $this->withdrawalModalMode = 'create';
        $this->resetWithdrawalForm();
    }

    public function submitWithdrawal(): void
    {
        if (!$this->selectedWithdrawalUserId) {
            session()->flash('error', 'No withdrawal user selected.');
            return;
        }

        $userID = $this->selectedWithdrawalUserId;

        $availableBalance = $this->getWalletBalance($userID);

        $rules = [
            'withdrawAmount' => [
                'required',
                'numeric',
                'min:500',
                'max:' . max(0, $availableBalance),
            ],
            'withdrawalMethod' => [
                'required',
                'in:Cash,GCash,Maya,Bank Transfer',
            ],
            'paymentReference' => [
                'nullable',
                'string',
                'max:100',
            ],
        ];

        if ($this->withdrawalMethod !== 'Cash') {
            $rules['accountName'] = ['required', 'string', 'max:255'];
            $rules['accountNumber'] = ['required', 'string', 'max:255'];
        } else {
            $rules['accountName'] = ['nullable', 'string', 'max:255'];
            $rules['accountNumber'] = ['nullable', 'string', 'max:255'];
        }

        $this->validate($rules, [
            'withdrawAmount.required' => 'Please enter a withdrawal amount.',
            'withdrawAmount.numeric' => 'Withdrawal amount must be a valid number.',
            'withdrawAmount.min' => 'Minimum withdrawal amount is ₱500.00.',
            'withdrawAmount.max' => 'Withdrawal amount exceeds the user\'s available balance.',
            'withdrawalMethod.required' => 'Please select a withdrawal method.',
            'accountName.required' => 'Please enter the account name.',
            'accountNumber.required' => 'Please enter the account number.',
        ]);

        try {
            DB::transaction(function () use ($userID) {
                $type = $this->withdrawalMethod === 'Cash' ? 'W' : 'O';

                $withdrawal = Withdrawal::create([
                    'UserID' => $userID,
                    'amount' => $this->withdrawAmount,
                    'type' => $type,
                    'withdrawal_method' => $this->withdrawalMethod,
                    'account_name' => $this->withdrawalMethod === 'Cash' ? null : $this->accountName,
                    'account_number' => $this->withdrawalMethod === 'Cash' ? null : $this->accountNumber,
                    'payment_reference' => $this->paymentReference ?: null,
                    'ProcessedBy' => Auth::user()->UserID,
                    'status' => 'Pending',
                ]);

                Ledger::create([
                    'RefID' => $userID,
                    'WithdrawID' => $withdrawal->WithdrawID,
                    'amount' => -$this->withdrawAmount,
                    'Source' => 'WD',
                    'ActivationID' => null,
                    'status' => 'Pending',
                ]);
            });

            $this->closeWithdrawalModal();

            session()->flash('success', 'Withdrawal request submitted successfully.');
        } catch (\Exception $e) {
            report($e);
            session()->flash('error', 'Something went wrong while submitting the withdrawal.');
        }
    }

    /**
     * Approve a pending withdrawal. Approved is the terminal / released
     * state for this workflow — there is no separate "release" step.
     *
     * On approval we:
     *   1. Flip the withdrawal to 'Approved'.
     *   2. Debit the user's cash wallet by inserting a negative-amount
     *      wallet row (the wallet balance is an append-only sum, same
     *      pattern as the existing Ledger entries — so we never mutate
     *      an existing wallet row, we add an offsetting one).
     *   3. Flip the matching Ledger row from 'Pending' to 'Approved' so
     *      the ledger and the withdrawal status stay in sync.
     */
    public function processWithdrawalApproval(): void
    {
        if (!$this->selectedWithdrawal) {
            return;
        }

        try {
            DB::transaction(function () {
                $withdrawal = $this->selectedWithdrawal;

                $withdrawal->update([
                    'status' => 'Approved',
                    'ProcessedBy' => Auth::user()?->UserID,
                ]);

                // Debit the wallet: negative entry so the running sum drops.
                Wallet::create([
                    'UserID' => $withdrawal->UserID,
                    'withdrawID' => $withdrawal->WithdrawID,
                    'amount' => -$withdrawal->amount,
                    'source' => 'WD',
                    'status' => 'Active',
                ]);



                // Keep the ledger entry for this withdrawal in sync.
                Ledger::where('WithdrawID', $withdrawal->WithdrawID)
                    ->update(['status' => 'Completed']);
            });

            session()->flash('message', 'Withdrawal approved, released, and deducted from the wallet successfully.');
        } catch (\Exception $e) {
            report($e);
            session()->flash('error', 'Something went wrong while approving the withdrawal.');
        }

        $this->closeWithdrawalModal();
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $users = User::query()
            ->where('RoleID', '!=', 4)
            ->withExists([
                'activations as has_pending_activation' => function ($query) {
                    $query->where('status', 'Pending');
                },
                'withdrawals as has_pending_withdrawal' => function ($query) {
                    $query->where('status', 'Pending');
                },
                'withdrawals as has_approved_withdrawal' => function ($query) {
                    $query->where('status', 'Approved');
                },
            ])
            ->when(filled($this->search), function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                        ->orWhere('middlename', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('contactno', 'like', "%{$search}%");
                });
            })
            ->latest('UserID')
            ->paginate(10);

        return view('livewire.admin-pages.masterlist.admin-users', [
            'users' => $users,
        ]);
    }
}
