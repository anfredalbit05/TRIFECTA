<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Models\User;
use App\Models\Module;
use App\Models\Privilege;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class AdminUsers extends Component
{
    use WithPagination;

    public string $search = '';
    protected $paginationTheme = 'tailwind';

    // Modal States
    public bool $isViewModalOpen = false;
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
    public string $contactno = '';
    public string $gender = '';
    public string $birthdate = '';
    public string $address = ''; // Added missing property
    public string $status = '';
    public string $RoleID = '';

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
                'Report & Settings - General'
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

    public function editUser(User $user)
    {
        Log::info("Initiating edit for user ID: {$user->UserID}");

        $this->selectedUserId = $user->UserID;

        $this->firstname = $user->firstname ?? '';
        $this->middlename = $user->middlename ?? '';
        $this->lastname = $user->lastname ?? '';
        $this->username = $user->username ?? '';
        $this->contactno = $user->contactno ?? '';
        $this->gender = $user->gender ?? '';
        $this->birthdate = $user->birthdate ? $user->birthdate->format('Y-m-d') : '';
        $this->address = $user->address ?? ''; // Added loading address
        $this->status = $user->status ?? 'Active';
        $this->RoleID = $user->RoleID ?? '';

        $this->isEditModalOpen = true;
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
                            'create'   => (bool) $privilege->can_create,
                            'retrieve' => (bool) $privilege->can_read,
                            'update'   => (bool) $privilege->can_update,
                            'delete'   => (bool) $privilege->can_delete,
                            'status'   => (bool) $privilege->can_change_status,
                            'printall' => (bool) $privilege->can_print,
                        ];
                    }
                }
            }
        }

        $this->isAccessModalOpen = true;
    }

    public function confirmDelete(User $user)
    {
        Log::info("Confirm deletion requested for user ID: {$user->UserID}");

        $this->selectedUserId = $user->UserID;
        $this->isDeleteModalOpen = true;
    }

    public function updateUser()
    {
        Log::info("Attempting to update user ID: {$this->selectedUserId}");

        $this->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'status' => 'required|string',
        ]);

        if ($this->selectedUser) {
            $this->selectedUser->update([
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
            ]);

            Log::info("Successfully updated user ID: {$this->selectedUserId}");
        }

        $this->isEditModalOpen = false;
        session()->flash('message', 'User updated successfully.');
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

    public function saveAccess()
    {
        Log::info("Saving access permissions matrix for user ID: {$this->selectedUserId}");

        $user = $this->selectedUser;

        if (!$user || !$user->RoleID) {
            Log::warning("SaveAccess failed: User or RoleID not found.");
            return;
        }

        foreach ($this->userAccess as $moduleName => $permissions) {
            $module = Module::where('modulename', $moduleName)->first();

            if ($module) {
                Privilege::updateOrCreate(
                    [
                        'RoleID'   => $user->RoleID,
                        'ModuleID' => $module->ModuleID,
                    ],
                    [
                        'can_create'        => $permissions['create'],
                        'can_read'          => $permissions['retrieve'],
                        'can_update'        => $permissions['update'],
                        'can_delete'        => $permissions['delete'],
                        'can_print'         => $permissions['printall'],
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

        return view('livewire.admin-pages.dashboard.all-users', [
            'users' => $users,
        ]);
    }
}
