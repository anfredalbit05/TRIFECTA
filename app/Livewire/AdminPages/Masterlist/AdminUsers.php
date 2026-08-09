<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Models\User;
use App\Models\Module;
use App\Models\Privilege;
use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdminUsers extends Component
{
    use WithPagination;

    public string $search = '';
    protected $paginationTheme = 'tailwind';

    // Modal States
    public bool $isViewModalOpen = false;
    public bool $isAddModalOpen = false;
    public bool $isEditModalOpen = false;
    public bool $isAccessModalOpen = false;
    public bool $isDeleteModalOpen = false;

    // Active User ID for Modals
    public ?int $selectedUserId = null;

    // Editable Form Fields
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

    public array $userAccess = [];

    public function mount()
    {
        $this->initializeAccessMatrix();
    }

    private function initializeAccessMatrix()
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

    /*
    |--------------------------------------------------------------------------
    | Permission Checkers (ModuleID 1 assumed for Admin Users)
    |--------------------------------------------------------------------------
    */

    protected function hasPermission($action): bool
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

    public function getSelectedUserProperty()
    {
        return $this->selectedUserId ? User::where('UserID', $this->selectedUserId)->first() : null;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function viewUser(User $user)
    {
        Log::info("Viewing user with ID: {$user->UserID}");

        $this->selectedUserId = $user->UserID;
        $this->isViewModalOpen = true;
    }

    private function generateUplineCode(): string
    {
        return strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
    }

    public function openAddModal()
    {
        Log::info('Opening add user modal');

        // Reset fields for a clean form when adding a new user
        $this->reset([
            'firstname', 'middlename', 'lastname', 'email',
            'username', 'password', 'contactno', 'gender',
            'birthdate', 'address', 'status', 'RoleID', 'position', 'selectedUserId',
        ]);

        $this->status = 'Active';
        $this->isAddModalOpen = true;
    }

    public function storeUser()
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
            'password' => $this->password, // Handled by model hashing cast
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

        $this->reset([
            'firstname', 'middlename', 'lastname', 'email',
            'username', 'password', 'contactno', 'gender',
            'birthdate', 'address', 'status', 'RoleID', 'position',
        ]);

        $this->status = 'Active';
        $this->isAddModalOpen = false;
    }

    public function editUser(User $user)
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

    public function updateUser()
    {
        Log::info("Attempting to update user ID: {$this->selectedUserId}");
        Log::info('updateUser: incoming position value', [
            'selectedUserId' => $this->selectedUserId,
            'position_raw' => $this->position,
            'position_type' => gettype($this->position),
        ]);

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

        Log::info('updateUser: passed validation', [
            'position_after_validation' => $this->position,
        ]);

        $user = User::find($this->selectedUserId);

        if (!$user) {
            Log::warning("updateUser: no user found for ID {$this->selectedUserId}, position not saved.");
        }

        if ($user) {
            $positionToSave = $this->position !== '' ? $this->position : null;

            Log::info('updateUser: position before save', [
                'user_id' => $user->UserID,
                'position_before' => $user->position,
                'position_to_save' => $positionToSave,
                'fillable' => $user->getFillable(),
                'position_is_fillable' => in_array('position', $user->getFillable(), true),
            ]);

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

            $user->refresh();

            Log::info('updateUser: position after save', [
                'user_id' => $user->UserID,
                'position_after' => $user->position,
                'saved_matches_intended' => $user->position === $positionToSave,
            ]);

            Log::info("Successfully updated user ID: {$this->selectedUserId}");
        }

        $this->isEditModalOpen = false;
        session()->flash('success', 'User updated successfully.');
    }

    public function closeModal()
    {
        $this->reset([
            'firstname', 'middlename', 'lastname', 'email',
            'username', 'password', 'contactno', 'gender',
            'birthdate', 'address', 'status', 'RoleID', 'position', 'selectedUserId',
        ]);

        $this->isAddModalOpen = false;
        $this->isEditModalOpen = false;
    }

    public function openAccessModal(User $user)
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

    public function deleteUser()
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

    public function confirmDelete(User $user)
    {
        Log::info("Confirm deletion requested for user ID: {$user->UserID}");

        $this->selectedUserId = $user->UserID;
        $this->isDeleteModalOpen = true;
    }

    public function saveAccess()
    {
        Log::info("Saving access permissions matrix for user ID: {$this->selectedUserId}");

        $user = $this->selectedUser;

        if (!$user || !$user->RoleID) {
            Log::warning('SaveAccess failed: User or RoleID not found.');
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

    public function resetTable(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $users = User::query()
            ->where('RoleID', '!=', 4)
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
