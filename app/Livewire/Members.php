<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Members extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $skipRender = true;

    protected $paginationTheme = 'tailwind';

    /*
    |--------------------------------------------------------------------------
    | Permission Checkers
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

        // Safely check role permissions or fallback to direct query
        if ($user->role && method_exists($user->role, 'permissions')) {
            return optional($user->role->permissions)
                ->where('ModuleID', 2)
                ->where($column, 1)
                ->isNotEmpty();
        }

        // Fallback direct check against privileges table
        return \App\Models\Privilege::where('RoleID', $user->RoleID)
            ->where('ModuleID', 2)
            ->where($column, 1)
            ->exists();
    }

    public function getCanCreateProperty(): bool
    {
        return $this->hasPermission('create'); // checks can_create
    }

    public function getCanReadProperty(): bool
    {
        return $this->hasPermission('read'); // checks can_read
    }

    public function getCanUpdateProperty(): bool
    {
        return $this->hasPermission('update'); // checks can_update
    }

    public function getCanDeleteProperty(): bool
    {
        return $this->hasPermission('delete'); // checks can_delete
    }

    /*
    |--------------------------------------------------------------------------
    | Refresh Members Table
    |--------------------------------------------------------------------------
    */
    #[On('members-refresh')]
    public function refreshMembers(): void
    {
        $this->skipRender = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Render Members
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $members = User::query()
            ->where('RoleID', '!=', 1)
            ->withExists([
                'activations as has_pending_activation' => function ($query) {
                    $query->where('status', 'Pending');
                }
            ])
            ->when(filled($this->search), function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                      ->orWhere('middlename', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('components.admin-pages.masterlist.members', [
            'members' => $members,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Open Activation Review Modal
    |--------------------------------------------------------------------------
    */

    public function openReview($id): void
    {
        if (!$this->canUpdate) {
            return;
        }

        Log::info('Activation review clicked', ['UserID' => $id, 'reviewed_by' => Auth::id()]);

        $member = User::find($id);

        if (!$member) {
            Log::warning('Review failed: member not found.', ['UserID' => $id]);
            return;
        }

        $this->dispatch('open-activation-review', UserID: $member->UserID);
    }

    /*
    |--------------------------------------------------------------------------
    | Activate / Deactivate Confirmation
    |--------------------------------------------------------------------------
    */

    public function confirmStatus($id): void
    {
        if (!$this->canUpdate) {
            return;
        }

        Log::info('Status button clicked', ['UserID' => $id]);

        $member = User::find($id);

        if (!$member) {
            Log::warning('Status update failed: member not found.', ['UserID' => $id]);
            return;
        }

        $action = strtolower($member->status) === 'active' ? 'deactivate' : 'activate';

        $this->dispatch('confirm-status', id: $member->UserID, action: $action);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Member Status
    |--------------------------------------------------------------------------
    */

    public function updateStatus($id): void
    {
        if (!$this->canUpdate) {
            return;
        }

        Log::info('Updating member status', ['UserID' => $id, 'updated_by' => Auth::id()]);

        $member = User::find($id);

        if (!$member) {
            Log::warning('Status update failed: member not found.', ['UserID' => $id]);
            return;
        }

        $member->status = strtolower($member->status) === 'active' ? 'Inactive' : 'Active';
        $member->save();

        Log::info('Member status updated successfully', ['UserID' => $member->UserID, 'new_status' => $member->status]);

        $this->dispatch('members-refresh');
        $this->dispatch('status-updated');
    }

    /*
    |--------------------------------------------------------------------------
    | Open Activation Modal
    |--------------------------------------------------------------------------
    */

    public function openActivation($id): void
    {
        if (!$this->canUpdate) {
            return;
        }

        Log::info('Admin activation clicked', ['target_UserID' => $id, 'processed_by' => Auth::id()]);

        $member = User::find($id);

        if (!$member) {
            Log::warning('Activation failed. Member not found.', ['target_UserID' => $id]);
            return;
        }

        $this->dispatch('open-activation-modal', UserID: $member->UserID);
    }

    /*
    |--------------------------------------------------------------------------
    | Open Update Member Modal
    |--------------------------------------------------------------------------
    */

    public function editMember($id): void
    {
        if (!$this->canUpdate) {
            return;
        }

        Log::info('Edit member clicked', ['UserID' => $id]);

        $member = User::find($id);

        if (!$member) {
            Log::warning('Edit member not found.', ['UserID' => $id]);
            return;
        }

        $this->dispatch('open-update-member', UserID: $member->UserID);
    }

    public function viewMember($id): void
    {
        if (!$this->canRead) {
            return;
        }

        $member = User::find($id);

        if (! $member) {
            return;
        }

        $this->dispatch('open-view-member', UserID: $member->UserID);
    }
}
