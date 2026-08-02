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

    protected $paginationTheme = 'tailwind';

    /*
    |--------------------------------------------------------------------------
    | Refresh Members Table
    |--------------------------------------------------------------------------
    |
    | This listener receives the event from:
    | - Activation review modal
    | - Activation modal
    | - Status update action
    |
    */

    #[On('members-refresh')]
    public function refreshMembers(): void
    {
        Log::info('Members table refresh event received.');

        // Livewire automatically calls render() again.
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
        'processedActivations as has_pending_activation' => function ($query) {
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
        Log::info(
            'Activation review clicked',
            [
                'UserID' => $id,
                'reviewed_by' => Auth::id(),
            ]
        );


        $member = User::find($id);


        if (!$member) {

            Log::warning(
                'Review failed: member not found.',
                [
                    'UserID' => $id,
                ]
            );

            return;
        }


        $this->dispatch(
            'open-activation-review',
            UserID: $member->UserID
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Activate / Deactivate Confirmation
    |--------------------------------------------------------------------------
    */

    public function confirmStatus($id): void
    {
        Log::info(
            'Status button clicked',
            [
                'UserID' => $id,
            ]
        );


        $member = User::find($id);


        if (!$member) {

            Log::warning(
                'Status update failed: member not found.',
                [
                    'UserID' => $id,
                ]
            );

            return;
        }


        $action = strtolower(
            $member->status
        ) === 'active'
            ? 'deactivate'
            : 'activate';


        $this->dispatch(
            'confirm-status',
            id: $member->UserID,
            action: $action
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update Member Status
    |--------------------------------------------------------------------------
    */

    public function updateStatus($id): void
    {
        Log::info(
            'Updating member status',
            [
                'UserID' => $id,
                'updated_by' => Auth::id(),
            ]
        );


        $member = User::find($id);


        if (!$member) {

            Log::warning(
                'Status update failed: member not found.',
                [
                    'UserID' => $id,
                ]
            );

            return;
        }


        $member->status = strtolower(
            $member->status
        ) === 'active'
            ? 'Inactive'
            : 'Active';


        $member->save();


        Log::info(
            'Member status updated successfully',
            [
                'UserID' => $member->UserID,
                'new_status' => $member->status,
            ]
        );


        /*
        |--------------------------------------------------------------
        | Refresh this Livewire component
        |--------------------------------------------------------------
        */

        $this->dispatch(
            'members-refresh'
        );


        /*
        |--------------------------------------------------------------
        | Show SweetAlert success message
        |--------------------------------------------------------------
        */

        $this->dispatch(
            'status-updated'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Open Activation Modal
    |--------------------------------------------------------------------------
    */

    public function openActivation($id): void
    {
        Log::info(
            'Admin activation clicked',
            [
                'target_UserID' => $id,
                'processed_by' => Auth::id(),
            ]
        );


        $member = User::find($id);


        if (!$member) {

            Log::warning(
                'Activation failed. Member not found.',
                [
                    'target_UserID' => $id,
                ]
            );

            return;
        }


        Log::info(
            'Sending open activation modal event',
            [
                'target_UserID' => $member->UserID,

                'member_name' =>
                    $member->firstname
                    . ' '
                    . $member->lastname,

                'ProcessedBy' => Auth::id(),
            ]
        );


        $this->dispatch(
            'open-activation-modal',
            UserID: $member->UserID
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Open Update Member Modal
    |--------------------------------------------------------------------------
    */

    public function editMember($id): void
    {
        Log::info(
            'Edit member clicked',
            [
                'UserID' => $id,
            ]
        );


        $member = User::find($id);


        if (!$member) {

            Log::warning(
                'Edit member not found.',
                [
                    'UserID' => $id,
                ]
            );

            return;
        }


        Log::info(
            'Opening update member modal',
            [
                'UserID' => $member->UserID,
            ]
        );


        $this->dispatch(
            'open-update-member',
            UserID: $member->UserID
        );
    }

    public function viewMember($id): void
{
    $member = User::find($id);

    if (! $member) {
        return;
    }

    $this->dispatch(
        'open-view-member',
        UserID: $member->UserID
    );
}

}
