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
| FREE + REGULAR MEMBERS MODAL
|---------------------------------------------------------------------------
| Opened from the Satellite Members actions column: lists every member
| (RoleID 4) whose uplineCode is this satellite member's referralCode —
| BOTH the provisioned free members (isFree = true) and the regular / paid
| members invited with that code (isFree = false).
|
| Two actions:
|
|   • FREE row     → one-click "Activate" with a typed confirmation.
|     Runs ActivationApprovalService::approve(TYPE_FREE): completes the
|     member's Pending membership-fee ledger and pays the satellite owner
|     DR / TX / OV / PF. No policy or receipt numbers.
|   • REGULAR row  → "Approve" button that opens the existing
|     ActivationReviewModal (open-activation-review with the exact
|     ActivationID) — the same review process used for normal members.
|
| "Activate All" only ever touches Pending FREE rows.
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
     * @var array<int, array{ActivationID: int, UserID: int, username: string, name: string, status: string, user_status: string, amount: float, created_at: string, isFree: bool, member_type: string}>
     */
    public array $freeMembers = [];

    public int $pendingCount = 0;

    /** Pending rows that are FREE members — the only ones Activate All may touch. */
    public int $pendingFreeCount = 0;

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
        $this->pendingFreeCount = 0;
        $this->approvedCount = 0;
        $this->reset(['selectedUserID', 'member', 'satellite']);
    }

    /*
    |------------------------------------------------------------------------
    | REFRESH (approval happened elsewhere — e.g. the ActivationReviewModal
    | dispatched members-refresh after approving a regular member)
    |------------------------------------------------------------------------
    */

    #[On('members-refresh')]
    public function refreshRows(): void
    {
        if ($this->open && $this->satellite && $this->member) {
            $this->loadFreeMembers();
        }
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

        if (! $row['isFree']) {
            $this->dispatch('swal:error', [
                'title' => 'Regular Member',
                'text' => 'This is a regular member. Use the Approve button to open the review modal.',
            ]);

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

        // Preview + typed confirmation: the dialog shows WHO is being
        // activated (username, name, amount, ledger effect) and the admin
        // must type the username exactly before the button unlocks.
        $this->dispatch('swal:confirm', [
            'title' => 'Activate Free Member?',
            'html' => 'You are about to activate the free member below.'
                .'<div style="margin:14px auto;max-width:420px;text-align:left;'
                .'border:1px solid #E2E8F0;border-radius:12px;overflow:hidden;">'
                .'<table style="width:100%;font-size:13px;border-collapse:collapse;">'
                .'<tr><td style="padding:8px 12px;color:#64748B;">Username</td>'
                .'<td style="padding:8px 12px;text-align:right;font-weight:700;color:var(--color-primary);">'
                .e($row['username']).'</td></tr>'
                .'<tr style="background:#F8FAFC;"><td style="padding:8px 12px;color:#64748B;">Name</td>'
                .'<td style="padding:8px 12px;text-align:right;font-weight:700;color:var(--color-primary);">'
                .e($row['name'] ?: '—').'</td></tr>'
                .'<tr><td style="padding:8px 12px;color:#64748B;">Type</td>'
                .'<td style="padding:8px 12px;text-align:right;font-weight:700;color:var(--color-primary);">Free Member</td></tr>'
                .'<tr style="background:#F8FAFC;"><td style="padding:8px 12px;color:#64748B;">Ledger Amount</td>'
                .'<td style="padding:8px 12px;text-align:right;font-weight:700;color:var(--color-primary);">₱'
                .number_format($row['amount'], 2).'</td></tr>'
                .'</table></div>'
                .'<p style="font-size:13px;color:#64748B;">Their membership-fee ledger is completed and the '
                .'satellite owner earns DR / PF (plus TX / OV up the line) for it.</p>'
                .'<br>Type <strong>'.e($row['username']).'</strong> to confirm.',
            'icon' => 'question',
            'method' => 'free-member-approve-confirmed',
            'requireConfirmWord' => $row['username'],
            'inputPlaceholder' => $row['username'],
            'confirmText' => 'Yes, activate!',
            'cancelText' => 'Cancel',
            'loadingTitle' => 'Activating free member...',
            'loadingText' => 'Updating records.',
        ]);
    }

    /*
    |------------------------------------------------------------------------
    | APPROVE A REGULAR (NON-FREE) MEMBER — opens the shared review modal
    |------------------------------------------------------------------------
    | A regular member invited under this satellite member's referralCode
    | goes through the exact same review process as any normal member:
    | ActivationReviewModal (policy / receipt numbers, then the shared
    | ActivationApprovalService). We only open it with the exact ActivationID.
    */

    public function openRegularMemberReview(int $activationID): void
    {
        $row = $this->findRow($activationID);

        if (! $row) {
            return;
        }

        if ($row['isFree']) {
            $this->dispatch('swal:error', [
                'title' => 'Free Member',
                'text' => 'Free members are activated directly with the Activate button.',
            ]);

            return;
        }

        if ($row['status'] !== 'Pending') {
            $this->dispatch('swal:error', [
                'title' => 'Already Processed',
                'text' => 'This activation has already been processed.',
            ]);

            return;
        }

        // Re-verify server-side that the activation still belongs to this
        // satellite member (defends against a stale modal).
        $activation = Activation::with('user')->find($activationID);

        $belongsHere = $activation
            && $activation->user
            && (int) $activation->user->RoleID === 4
            && (string) $activation->user->uplineCode === (string) $this->member?->referralCode;

        if (! $belongsHere) {
            $this->dispatch('swal:error', [
                'title' => 'Not Found',
                'text' => 'This member does not belong to the selected satellite.',
            ]);

            return;
        }

        Log::info('Regular member review opened from Free Members modal.', [
            'ActivationID' => $activation->ActivationID,
            'UserID' => $activation->UserID,
            'satelliteID' => $this->satellite?->satelliteID,
            'opened_by' => Auth::id(),
        ]);

        // Same event ActivationReviewModal listens to (mounted once on the
        // masterlist page); passing the ActivationID reviews exactly this row.
        $this->dispatch(
            'open-activation-review',
            UserID: (int) $activation->UserID,
            activationId: (int) $activation->ActivationID,
        );
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
        if ($this->pendingFreeCount < 1) {
            $this->dispatch('swal:error', [
                'title' => 'Nothing To Activate',
                'text' => 'There are no pending free members under this satellite.',
            ]);

            return;
        }

        // Preview + typed confirmation. The list below is built ONLY from
        // Pending FREE rows — regular members are never included.
        $freeUsernames = collect($this->freeMembers)
            ->filter(fn (array $row) => $row['status'] === 'Pending' && $row['isFree'])
            ->pluck('username')
            ->values();

        $previewList = $freeUsernames
            ->take(10)
            ->map(fn ($username) => '<li style="font-weight:700;color:var(--color-primary);">'.e($username).'</li>')
            ->implode('');

        $overflow = $freeUsernames->count() > 10
            ? '<li style="color:#64748B;">… and '.($freeUsernames->count() - 10).' more</li>'
            : '';

        $this->dispatch('swal:confirm', [
            'title' => 'Activate All Free Members?',
            'html' => 'You are about to activate <strong style="color:var(--color-primary);">'
                .$this->pendingFreeCount.'</strong> pending free member(s) under '
                .'<strong style="color:var(--color-primary);">'.e($this->satellite?->satellite_name ?: 'this satellite').'</strong>.'
                .'<div style="margin:14px auto;max-width:420px;text-align:left;'
                .'border:1px solid #E2E8F0;border-radius:12px;max-height:220px;'
                .'overflow:auto;padding:10px 14px;">'
                .'<p style="margin:0 0 6px;font-size:11px;font-weight:700;'
                .'text-transform:uppercase;letter-spacing:.05em;color:#64748B;">'
                .'Free members only</p>'
                .'<ul style="margin:0;padding-left:18px;font-size:13px;">'
                .$previewList.$overflow.'</ul></div>'
                .'<p style="font-size:13px;color:#64748B;">Regular members are NOT included — '
                .'each one is approved separately through its own review. Each free member\'s '
                .'membership-fee ledger is completed and the satellite owner is paid DR and PF.</p>'
                .'<br>Type <strong>ACTIVATE ALL</strong> to confirm.',
            'icon' => 'question',
            'method' => 'free-member-approve-all-confirmed',
            'requireConfirmWord' => 'ACTIVATE ALL',
            'inputPlaceholder' => 'ACTIVATE ALL',
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

        // FREE PENDING ONLY — regular members must never be batch-approved
        // here; they go through the ActivationReviewModal instead.
        $pendingIDs = collect($this->freeMembers)
            ->filter(fn (array $row) => $row['status'] === 'Pending' && $row['isFree'])
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
        if (! $this->satellite || ! $this->member) {
            $this->freeMembers = [];
            $this->pendingCount = 0;
            $this->pendingFreeCount = 0;
            $this->approvedCount = 0;

            return;
        }

        /*
         * IMPORTANT:
         *
         * The satellite member's referralCode is the actual relationship
         * that determines which members belong under this satellite member.
         *
         * Do NOT restrict this query to isFree = true.
         * We want BOTH:
         *
         *   isFree = true  -> FREE member  (one-click Activate)
         *   isFree = false -> REGULAR member (Approve opens review modal)
         */
        $rows = Activation::query()
            ->whereHas('user', function ($query) {
                $query
                    ->where('uplineCode', $this->member->referralCode)
                    ->where('RoleID', 4);
            })
            ->with('user')
            ->latest('ActivationID')
            ->get();

        $this->freeMembers = $rows
            ->map(function (Activation $activation) {
                $user = $activation->user;

                return [
                    'ActivationID' => (int) $activation->ActivationID,
                    'UserID' => (int) $activation->UserID,

                    'username' => (string) data_get($user, 'username', ''),

                    'name' => trim(
                        data_get($user, 'firstname', '')
                        .' '.
                        data_get($user, 'lastname', '')
                    ),

                    'status' => (string) $activation->status,

                    'user_status' => (string) data_get($user, 'status', ''),

                    'amount' => (float) $activation->amount,

                    'created_at' => (string) ($activation->created_at ?? ''),

                    /*
                     * IMPORTANT:
                     * Keep this as a boolean.
                     */
                    'isFree' => (bool) $activation->isFree,

                    /*
                     * Easier for the Blade.
                     */
                    'member_type' => $activation->isFree
                        ? 'Free Member'
                        : 'Paid Member',
                ];
            })
            ->values()
            ->toArray();

        $this->pendingCount = count(array_filter(
            $this->freeMembers,
            fn (array $row) => $row['status'] === 'Pending'
        ));

        // Only Pending FREE rows feed the Activate All button / counter.
        $this->pendingFreeCount = count(array_filter(
            $this->freeMembers,
            fn (array $row) => $row['status'] === 'Pending' && $row['isFree']
        ));

        $this->approvedCount = count(array_filter(
            $this->freeMembers,
            fn (array $row) => $row['status'] === 'Approved'
        ));
    }

    /**
     * The row to act on, keyed the same way $freeMembers is — or null when
     * it does not belong to this satellite / the caller may not act.
     *
     * @return array{ActivationID: int, UserID: int, username: string, name: string, status: string, user_status: string, amount: float, created_at: string, isFree: bool, member_type: string}|null
     */
    private function findRow(int $activationID): ?array
    {
        if (! $this->canUpdate) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to manage members.',
            ]);

            return null;
        }

        $row = collect($this->freeMembers)
            ->firstWhere('ActivationID', $activationID);

        if (! $row) {
            $this->dispatch('swal:error', [
                'title' => 'Not Found',
                'text' => 'This member does not belong to the selected satellite.',
            ]);

            return null;
        }

        return $row;
    }

    /**
     * Re-read the activation and verify it is still a Pending FREE member of
     * THIS satellite (defends against a stale modal, against approving
     * somebody else's activation by ID, and against routing a regular
     * member through the one-click free path).
     */
    private function guardedActivation(int $activationID, bool $silent = false): ?Activation
    {
        if (! $this->guardedReady(silent: $silent)) {
            return null;
        }

        $activation = Activation::with('user')->find($activationID);

        $belongsHere = $activation
            && (int) $activation->UserID > 0
            && $activation->user
            && (int) $activation->user->RoleID === 4
            && (string) $activation->user->uplineCode === (string) $this->member->referralCode
            && (bool) $activation->isFree;

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
