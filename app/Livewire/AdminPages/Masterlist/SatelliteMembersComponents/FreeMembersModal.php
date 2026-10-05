<?php

namespace App\Livewire\AdminPages\Masterlist\SatelliteMembersComponents;

use App\Livewire\Concerns\AuthorizesModules;
use App\Models\Activation;
use App\Models\Satellite;
use App\Models\User;
use App\Services\ActivationApprovalService;
use App\Support\Modules;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

/*
|---------------------------------------------------------------------------
| FREE MEMBERS MODAL
|---------------------------------------------------------------------------
| Opened from the Satellite Members actions column: lists every free member
| (RoleID 4, isFree = true) provisioned under one satellite member and lets
| an admin activate them one by one — or all at once.
|
| Approval runs through ActivationApprovalService::approve(TYPE_FREE), the
| same shared approval used by the review modals: it completes the member's
| Pending membership-fee ledger and pays the satellite owner DR / TX / OV /
| PF. No policy or receipt numbers are involved (one-click approval).
*/

/**
 * @property-read bool $canUpdate
 */
class FreeMembersModal extends Component
{
    use AuthorizesModules;

    protected function module(): int
    {
        return Modules::SATELLITE_MEMBERS;
    }

    public bool $open = false;

    public ?int $selectedUserID = null;

    public ?User $member = null;

    public ?Satellite $satellite = null;

    /**
     * Rows shown in the table — plain scalars so Livewire can hydrate them
     * without touching the models on every request.
     *
     * @var array<int, array{ActivationID: int, UserID: int, username: string, name: string, status: string, user_status: string, amount: float, created_at: string}>
     */
    public array $freeMembers = [];

    public int $pendingCount = 0;

    public int $approvedCount = 0;

    /** Activation waiting for the typed confirmation dialog. */
    public ?int $pendingActivationID = null;


    /*
    |------------------------------------------------------------------------
    | Open the modal for one satellite member
    |------------------------------------------------------------------------
    */

    #[On('open-free-members')]
    public function openModal(int $UserID): void
    {
        if (! $this->canUpdate) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to manage free members.',
            ]);

            return;
        }

        $member = User::where('UserID', $UserID)
            ->where('RoleID', 8)
            ->first();

        if (! $member) {
            Log::warning('Free members modal blocked: invalid satellite member.', [
                'UserID' => $UserID,
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Invalid Member',
                'text' => 'This user is not a satellite member.',
            ]);

            return;
        }

        $satellite = Satellite::where('UserID', $member->UserID)
            ->latest('satelliteID')
            ->first();

        if (! $satellite) {
            $this->dispatch('swal:error', [
                'title' => 'No Satellite',
                'text' => 'This satellite member has no satellite record yet.',
            ]);

            return;
        }

        $this->selectedUserID = (int) $member->UserID;
        $this->member = $member;
        $this->satellite = $satellite;
        $this->pendingActivationID = null;

        $this->loadFreeMembers();

        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
        $this->pendingActivationID = null;
        $this->freeMembers = [];
        $this->pendingCount = 0;
        $this->approvedCount = 0;
        $this->reset(['selectedUserID', 'member', 'satellite']);
    }

    /*
    |------------------------------------------------------------------------
    | APPROVE ONE FREE MEMBER
    |------------------------------------------------------------------------
    */

    public function confirmApproveFreeMember(int $activationID): void
    {
        $row = $this->findRow($activationID);

        if (! $row) {
            return;
        }

        if ($row['status'] !== 'Pending') {
            $this->dispatch('swal:error', [
                'title' => 'Already Processed',
                'text' => 'This free member has already been processed.',
            ]);

            return;
        }

        $this->pendingActivationID = $activationID;

        $this->dispatch('swal:confirm', [
            'title' => 'Activate Free Member?',
            'text' => 'Activate '.($row['username'] ?: 'this free member')
                .'? Their membership-fee ledger is completed and the satellite'
                .' owner earns DR and PF for it.',
            'icon' => 'question',
            'method' => 'free-member-approve-confirmed',
            'confirmText' => 'Yes, activate!',
            'cancelText' => 'Cancel',
            'loadingTitle' => 'Activating free member...',
            'loadingText' => 'Updating records.',
        ]);
    }

    #[On('free-member-approve-confirmed')]
    public function approvePendingFreeMember(): void
    {
        $activation = $this->guardedActivation($this->pendingActivationID);

        if (! $activation) {
            return;
        }

        $approved = app(ActivationApprovalService::class)->approve(
            $activation,
            (int) $activation->UserID,
            ActivationApprovalService::TYPE_FREE
        );

        if (! $approved) {
            Log::warning('Free member approval failed.', [
                'ActivationID' => $activation->ActivationID,
                'processed_by' => Auth::id(),
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Not Activated',
                'text' => 'This free member could not be activated. Please try again.',
            ]);

            return;
        }

        Log::info('Free member approved from Free Members modal.', [
            'ActivationID' => $activation->ActivationID,
            'UserID' => $activation->UserID,
            'satelliteID' => $this->satellite?->satelliteID,
            'processed_by' => Auth::id(),
        ]);

        $this->pendingActivationID = null;

        $this->afterApproval();
    }

    /*
    |------------------------------------------------------------------------
    | APPROVE ALL PENDING FREE MEMBERS
    |------------------------------------------------------------------------
    */

    public function confirmApproveAllFreeMembers(): void
    {
        if ($this->pendingCount < 1) {
            $this->dispatch('swal:error', [
                'title' => 'Nothing To Activate',
                'text' => 'There are no pending free members under this satellite.',
            ]);

            return;
        }

        $this->dispatch('swal:confirm', [
            'title' => 'Activate All Free Members?',
            'text' => 'Activate all '.$this->pendingCount.' pending free members under '
                .($this->satellite?->satellite_name ?: 'this satellite')
                .'. Each one completes its own membership-fee ledger and pays the satellite owner DR and PF.',
            'icon' => 'question',
            'method' => 'free-member-approve-all-confirmed',
            'confirmText' => 'Yes, activate all',
            'cancelText' => 'Cancel',
            'loadingTitle' => 'Activating free members...',
            'loadingText' => 'This may take a moment.',
        ]);
    }

    #[On('free-member-approve-all-confirmed')]
    public function approveAllFreeMembers(): void
    {
        if (! $this->guardedReady()) {
            return;
        }

        $pendingIDs = collect($this->freeMembers)
            ->where('status', 'Pending')
            ->pluck('ActivationID')
            ->all();

        $activated = 0;

        foreach ($pendingIDs as $activationID) {
            $activation = $this->guardedActivation($activationID, silent: true);

            if (! $activation) {
                continue;
            }

            $approved = app(ActivationApprovalService::class)->approve(
                $activation,
                (int) $activation->UserID,
                ActivationApprovalService::TYPE_FREE
            );

            if ($approved) {
                $activated++;
            }
        }

        Log::info('Free members batch approved from Free Members modal.', [
            'satelliteID' => $this->satellite?->satelliteID,
            'requested' => count($pendingIDs),
            'activated' => $activated,
            'processed_by' => Auth::id(),
        ]);

        $this->pendingActivationID = null;

        $this->afterApproval($activated > 0);
    }

    /*
    |------------------------------------------------------------------------
    | HELPERS
    |------------------------------------------------------------------------
    */

    /** Reload the table rows + counters after any status change. */
    private function afterApproval(bool $success = true): void
    {
        $this->loadFreeMembers();

        $this->dispatch('members-refresh');

        if ($success) {
            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Free member activated successfully.',
            ]);
        }
    }

    private function loadFreeMembers(): void
    {
        if (! $this->satellite) {
            $this->freeMembers = [];
            $this->pendingCount = 0;
            $this->approvedCount = 0;

            return;
        }

        $rows = Activation::query()
            ->where('satelliteID', $this->satellite->satelliteID)
            ->where('isFree', true)
            ->with('user')
            ->latest('ActivationID')
            ->get();

        $this->freeMembers = $rows
            ->map(function (Activation $activation) {
                // The member behind this free activation (eager loaded above).
                $user = $activation->user;

                return [
                    'ActivationID' => (int) $activation->ActivationID,
                    'UserID' => (int) $activation->UserID,
                    'username' => (string) data_get($user, 'username', ''),
                    'name' => trim(
                        data_get($user, 'firstname', '')
                        .' '
                        .data_get($user, 'lastname', '')
                    ),
                    'status' => (string) $activation->status,
                    'user_status' => (string) data_get($user, 'status', ''),
                    'amount' => (float) $activation->amount,
                    'created_at' => (string) ($activation->created_at ?? ''),
                ];
            })
            ->values()
            ->toArray();

        $this->pendingCount = count(array_filter(
            $this->freeMembers,
            fn (array $row) => $row['status'] === 'Pending'
        ));

        $this->approvedCount = count($this->freeMembers) - $this->pendingCount;
    }

    /**
     * The row to act on, keyed the same way $freeMembers is — or null when
     * it does not belong to this satellite / the caller may not act.
     *
     * @return array{ActivationID: int, UserID: int, username: string, name: string, status: string, user_status: string, amount: float, created_at: string}|null
     */
    private function findRow(int $activationID): ?array
    {
        if (! $this->canUpdate) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to activate free members.',
            ]);

            return null;
        }

        $row = collect($this->freeMembers)
            ->firstWhere('ActivationID', $activationID);

        if (! $row) {
            $this->dispatch('swal:error', [
                'title' => 'Not Found',
                'text' => 'This free member does not belong to the selected satellite.',
            ]);

            return null;
        }

        return $row;
    }

    /**
     * Re-read the activation and verify it is still a Pending free member of
     * THIS satellite (defends against a stale modal and against approving
     * somebody else's activation by ID).
     */
    private function guardedActivation(int $activationID, bool $silent = false): ?Activation
    {
        if (! $this->guardedReady(silent: $silent)) {
            return null;
        }

        $activation = Activation::find($activationID);

        $belongsHere = $activation
            && $activation->isFree
            && (int) $activation->satelliteID === (int) $this->satellite->satelliteID;

        if (! $belongsHere) {
            if (! $silent) {
                $this->dispatch('swal:error', [
                    'title' => 'Not Found',
                    'text' => 'This free member does not belong to the selected satellite.',
                ]);
            }

            return null;
        }

        if ($activation->status !== 'Pending') {
            if (! $silent) {
                $this->dispatch('swal:error', [
                    'title' => 'Already Processed',
                    'text' => 'This free member has already been processed.',
                ]);
            }

            return null;
        }

        return $activation;
    }

    /** Permission + satellite guards shared by every approval entry point. */
    private function guardedReady(bool $silent = false): bool
    {
        if (! $this->canUpdate || ! $this->satellite) {
            if (! $silent) {
                $this->dispatch('swal:error', [
                    'title' => 'Not Allowed',
                    'text' => 'You do not have permission to activate free members.',
                ]);
            }

            return false;
        }

        return true;
    }

    public function render(): View
    {
        return view('livewire.admin-pages.masterlist.satellite-members-components.free-members-modal');
    }
}
