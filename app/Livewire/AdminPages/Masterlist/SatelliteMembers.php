<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Livewire\Concerns\AuthorizesModules;
use App\Mail\PasswordResetConfirmation;
use App\Models\Ledger;
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

class SatelliteMembers extends Component
{
    use AuthorizesModules;
    use WithPagination;

    public string $search = '';

    public string $dateRange = '';

    public string $summaryFilter = 'all';

    // Excel Export Filters
    public $exportYear = '';

    public $exportMonth = '';

    public $exportStatus = '';

    public bool $skipRender = true;

    protected $paginationTheme = 'tailwind';

    // =====================================================
    // WITHDRAWAL APPROVAL STATE
    // =====================================================

    public bool $isWithdrawalModalOpen = false;

    public ?int $selectedWithdrawalUserId = null;

    public ?User $selectedWithdrawalUser = null;

    public ?Withdrawal $selectedWithdrawal = null;

    // =====================================================
    // MOUNT
    // =====================================================

    public function mount(): void
    {
        $this->dateRange = '';
    }

    // =====================================================
    // PERMISSION CHECKERS
    // =====================================================

    /*
    |--------------------------------------------------------------------------
    | Permission Checkers — global module system (Masterlist - Satellite)
    |--------------------------------------------------------------------------
    */

    protected function module(): int
    {
        return Modules::SATELLITE_MEMBERS;
    }

    // =====================================================
    // REFRESH MEMBERS TABLE
    // =====================================================

    #[On('members-refresh')]
    public function refreshMembers(): void
    {
        $this->skipRender = false;
    }

    // =====================================================
    // REFRESH AFTER WITHDRAWAL CREATION
    // =====================================================

    #[On('withdrawal-submitted')]
    public function onWithdrawalSubmittedElsewhere(): void
    {
        Log::info(
            'SatelliteMembers: withdrawal-submitted event received, refreshing table.'
        );

        $this->skipRender = false;
    }

    // =====================================================
    // WITHDRAWAL GLOBAL STATUS
    // =====================================================

    public function isWithdrawalActive(): bool
    {
        return filter_var(
            Setting::where('name', 'activate_withdrawal')
                ->value('value'),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    // =====================================================
    // RENDER SATELLITE MEMBERS
    // =====================================================

    public function render()
    {
        [$dateFrom, $dateTo] = $this->parsedDateRange();

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        | This page ONLY displays users with RoleID = 8.
        |--------------------------------------------------------------------------
        */

        $members = User::query()

            // ONLY SATELLITE MEMBERS
            ->where('RoleID', 8)

            // Pending activation / withdrawal status
            ->withExists([
                'activations as has_pending_activation' => function ($query) {
                    $query->where('status', 'Pending');
                },

                // Satellite user whose latest application was REJECTED — drives
                // the "Reapply" button label in the table.
                'activations as has_rejected_activation' => function ($query) {
                    $query->where('status', 'Rejected');
                },

                'withdrawals as has_pending_withdrawal' => function ($query) {
                    $query->where('status', 'Pending');
                },
            ])

            // SEARCH
            ->when(filled($this->search), function ($query) {

                $search = trim($this->search);

                $query->where(function ($q) use ($search) {

                    $q->where('firstname', 'like', "%{$search}%")
                        ->orWhere('middlename', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");

                });

            })

            // DATE RANGE
            ->when($dateFrom && $dateTo, function ($query) use ($dateFrom, $dateTo) {

                $query->whereBetween('created_at', [

                    Carbon::parse($dateFrom)->startOfDay(),

                    Carbon::parse($dateTo)->endOfDay(),

                ]);

            })

            // NEWEST FIRST
            ->latest()

            // PAGINATION
            ->paginate(10);

        return view(
            'livewire.admin-pages.masterlist.satellite-members',
            [
                'members' => $members,

                'isWithdrawalActive' => $this->isWithdrawalActive(),
            ]
        );
    }

    // =====================================================
    // PARSE DATE RANGE
    // =====================================================

    protected function parsedDateRange(): array
    {
        if (
            blank($this->dateRange) ||
            ! str_contains($this->dateRange, ' to ')
        ) {
            return [null, null];
        }

        [$from, $to] = explode(
            ' to ',
            $this->dateRange,
            2
        );

        return [
            trim($from) ?: null,
            trim($to) ?: null,
        ];
    }

    // =====================================================
    // DATE RANGE UPDATED
    // =====================================================

    public function updatedDateRange(): void
    {
        $this->resetPage();
    }

    // =====================================================
    // CLEAR DATE FILTER
    // =====================================================

    public function clearDateFilter(): void
    {
        $this->dateRange = '';

        $this->resetPage();

        $this->dispatch('ranges-reset');
    }

    // =====================================================
    // SEARCH UPDATED
    // =====================================================

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // =====================================================
    // OPEN SATELLITE ACTIVATION REVIEW
    // =====================================================

    public function openReview($id): void
    {
        if (! $this->canUpdate) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to review activations.',
            ]);

            return;
        }

        Log::info(
            'Satellite activation review clicked',
            [
                'UserID' => $id,
                'reviewed_by' => Auth::id(),
            ]
        );

        // IMPORTANT:
        // Only RoleID 8 can be reviewed from SatelliteMembers.
        $member = User::where('UserID', $id)
            ->where('RoleID', 8)
            ->first();

        if (! $member) {
            Log::warning(
                'Satellite activation review failed: invalid satellite member.',
                [
                    'UserID' => $id,
                ]
            );

            $this->dispatch('swal:error', [
                'title' => 'Invalid Member',
                'text' => 'The selected user is not a satellite member.',
            ]);

            return;
        }

        // IMPORTANT:
        // This event name MUST match SatelliteMembersActivationReview.
        $this->dispatch(
            'open-satellite-activation-review',
            UserID: (int) $member->UserID
        );
    }

    // =====================================================
    // SATELLITE SUBSCRIPTION MODAL
    // =====================================================

    public function openSatellite($id): void
    {
        if (! $this->canUpdate) {

            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to manage satellite subscriptions.',
            ]);

            return;
        }

        Log::info(
            'Satellite modal clicked',
            [
                'UserID' => $id,
                'opened_by' => Auth::id(),
            ]
        );

        $member = User::find($id);

        if (! $member) {

            Log::warning(
                'Satellite modal failed: member not found.',
                [
                    'UserID' => $id,
                ]
            );

            $this->dispatch('swal:error', [
                'title' => 'Not Found',
                'text' => 'Member not found.',
            ]);

            return;
        }

        // Extra protection
        if ((int) $member->RoleID !== 8) {

            $this->dispatch('swal:error', [
                'title' => 'Invalid Member',
                'text' => 'This user is not a satellite member.',
            ]);

            return;
        }

        $this->dispatch(
            'open-satellite-modal',
            UserID: $member->UserID
        );
    }

    // =====================================================
    // CONFIRM STATUS
    // =====================================================

    public function confirmStatus($id): void
    {
        if (! $this->canUpdate) {
            return;
        }

        Log::info(
            'Satellite member status button clicked',
            [
                'UserID' => $id,
            ]
        );

        $member = User::find($id);

        if (! $member) {

            Log::warning(
                'Satellite status update failed: member not found.',
                [
                    'UserID' => $id,
                ]
            );

            return;
        }

        // Only RoleID 8
        if ((int) $member->RoleID !== 8) {
            return;
        }

        $action =
            strtolower($member->status) === 'active'
                ? 'deactivate'
                : 'activate';

        $this->dispatch(
            'satellite-confirm-status',
            id: $member->UserID,
            action: $action,
            name: collect([$member->firstname, $member->middlename, $member->lastname])
                ->filter()
                ->implode(' '),
        );
    }

    // =====================================================
    // UPDATE MEMBER STATUS
    // =====================================================

    public function updateStatus($id): void
    {
        if (! $this->canUpdate) {
            return;
        }

        Log::info(
            'Updating satellite member status',
            [
                'UserID' => $id,
                'updated_by' => Auth::id(),
            ]
        );

        $member = User::find($id);

        if (! $member) {

            Log::warning(
                'Satellite status update failed: member not found.',
                [
                    'UserID' => $id,
                ]
            );

            return;
        }

        // Security protection:
        // this component may only update RoleID 8.
        if ((int) $member->RoleID !== 8) {

            Log::warning(
                'Satellite status update blocked: invalid role.',
                [
                    'UserID' => $id,
                    'RoleID' => $member->RoleID,
                ]
            );

            return;
        }

        $isActivating =
            strtolower($member->status) !== 'active';

        $member->status =
            strtolower($member->status) === 'active'
                ? 'Inactive'
                : 'Active';

        $member->save();

        $fullName = collect([$member->firstname, $member->middlename, $member->lastname])
            ->filter()
            ->implode(' ');

        Log::info(
            'Satellite member status updated successfully',
            [
                'UserID' => $member->UserID,
                'new_status' => $member->status,
            ]
        );

        $this->dispatch('swal:success', [
            'title' => 'Success!',
            'text' => ($isActivating ? "{$fullName} has been activated" : "{$fullName} has been deactivated").' successfully.',
        ]);

        $this->dispatch('members-refresh');
    }

    // =====================================================
    // OPEN ACTIVATION MODAL
    // =====================================================

    public function openActivation($id): void
    {
        if (! $this->canUpdate) {
            return;
        }

        Log::info(
            'Satellite admin activation clicked',
            [
                'target_UserID' => $id,
                'processed_by' => Auth::id(),
            ]
        );

        $member = User::find($id);

        if (! $member) {

            Log::warning(
                'Satellite activation failed. Member not found.',
                [
                    'target_UserID' => $id,
                ]
            );

            return;
        }

        // Only RoleID 8
        if ((int) $member->RoleID !== 8) {
            return;
        }

        // Has a Pending application → open the review modal (approve / reject).
        $hasPending = $member->activations()->where('status', 'Pending')->exists();

        if ($hasPending) {
            $this->dispatch(
                'open-satellite-activation-review',
                UserID: $member->UserID
            );

            return;
        }

        // Has a REJECTED activation → REAPPLY mode: reopen the wizard pre-filled
        // with this satellite user's own info + last application, so the admin can
        // correct it and resubmit WITHOUT creating a new user ("RESUBMIT APPLICATION").
        $hasRejected = $member->activations()->where('status', 'Rejected')->exists();

        if ($hasRejected) {
            Log::info('Satellite admin activation → reapply', [
                'target_UserID' => $member->UserID,
                'mode' => 'reapply',
                'processed_by' => Auth::id(),
            ]);

            $this->dispatch(
                'open-satellite-reapply',
                UserID: $member->UserID
            );

            return;
        }

        // No application history at all → open the plain NEW-application wizard
        // (defensive fallback; shouldn't normally occur in this table).
        Log::info('Satellite admin activation → new application (no history)', [
            'target_UserID' => $member->UserID,
            'mode' => 'new-application',
            'processed_by' => Auth::id(),
        ]);

        $this->dispatch(
            'open-satellite-modal',
            UserID: $member->UserID
        );
    }

    // =====================================================
    // OPEN FREE MEMBERS MODAL
    // =====================================================

    /**
     * Lists the free members (RoleID 4, isFree) provisioned under this
     * satellite member so an admin can activate them from the table.
     */
    public function openFreeMembers(int|string $id): void
    {
        if (! $this->canUpdate) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to manage free members.',
            ]);

            return;
        }

        Log::info(
            'Satellite admin free members clicked',
            [
                'target_UserID' => $id,
                'processed_by' => Auth::id(),
            ]
        );

        $member = User::where('UserID', $id)
            ->where('RoleID', 8)
            ->first();

        if (! $member) {
            Log::warning(
                'Free members modal blocked: member not found or invalid role.',
                [
                    'target_UserID' => $id,
                ]
            );

            $this->dispatch('swal:error', [
                'title' => 'Invalid Member',
                'text' => 'This user is not a satellite member.',
            ]);

            return;
        }

        $this->dispatch(
            'open-free-members',
            UserID: $member->UserID
        );
    }

    // =====================================================
    // EDIT SATELLITE MEMBER
    // =====================================================

    public function editMember($id): void
    {
        if (! $this->canUpdate) {
            return;
        }

        Log::info(
            'Edit satellite member clicked',
            [
                'UserID' => $id,
                'updated_by' => Auth::id(),
            ]
        );

        // IMPORTANT:
        // Only allow RoleID 8 members to be edited here.
        $member = User::where('UserID', $id)
            ->where('RoleID', 8)
            ->first();

        if (! $member) {
            Log::warning(
                'Edit satellite member blocked: member not found or invalid role.',
                [
                    'UserID' => $id,
                ]
            );

            $this->dispatch('swal:error', [
                'title' => 'Invalid Member',
                'text' => 'This user is not a satellite member.',
            ]);

            return;
        }

        // Open SatelliteMembersEdit
        $this->dispatch(
            'open-satellite-member-edit',
            UserID: $member->UserID
        );
    }

    // =====================================================
    // VIEW MEMBER
    // =====================================================

    public function viewMember($id): void
    {
        if (! $this->canRead) {
            return;
        }

        $member = User::where('UserID', $id)
            ->where('RoleID', 8)
            ->first();

        if (! $member) {
            $this->dispatch('swal:error', [
                'title' => 'Invalid Member',
                'text' => 'The selected user is not a satellite member.',
            ]);

            return;
        }

        Log::info('Opening satellite member view', [
            'UserID' => $member->UserID,
            'opened_by' => Auth::id(),
        ]);

        $this->dispatch(
            'open-satellite-member-view',
            UserID: (int) $member->UserID
        );
    }

    // =====================================================
    // PASSWORD POLICY
    // =====================================================

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

    // =====================================================
    // PASSWORD POLICY HINT
    // =====================================================

    private function passwordPolicyHint(array $policy): string
    {
        $parts = [
            'at least '.$policy['min'].' characters',
        ];

        if ($policy['mixedCase']) {

            $parts[] =
                'uppercase and lowercase letters';

        } elseif ($policy['letters']) {

            $parts[] = 'letters';
        }

        if ($policy['numbers']) {
            $parts[] = 'numbers';
        }

        if ($policy['symbols']) {
            $parts[] = 'symbols';
        }

        return 'Must contain '.implode(
            ', ',
            $parts
        ).'.';
    }

    // =====================================================
    // PROMPT MEMBER PASSWORD RESET
    // =====================================================

    public function promptMemberPasswordReset($id): void
    {
        if (! $this->canUpdate) {

            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to reset member passwords.',
            ]);

            return;
        }

        $member = User::find($id);

        if (! $member) {

            $this->dispatch('swal:error', [
                'title' => 'Not Found',
                'text' => 'Member not found.',
            ]);

            return;
        }

        // Only RoleID 8
        if ((int) $member->RoleID !== 8) {

            $this->dispatch('swal:error', [
                'title' => 'Invalid Member',
                'text' => 'This user is not a satellite member.',
            ]);

            return;
        }

        $policy = $this->passwordPolicy();

        $this->dispatch(
            'set-member-password',

            id: $member->UserID,

            name: trim(
                ($member->firstname ?? '').
                ' '.
                ($member->lastname ?? '')
            ),

            policy: $policy,

            hint: $this->passwordPolicyHint($policy)
        );
    }

    // =====================================================
    // RESET MEMBER PASSWORD
    // =====================================================

    public function resetMemberPassword($id, $password): void
    {
        if (! $this->canUpdate) {

            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to reset member passwords.',
            ]);

            return;
        }

        $member = User::find($id);

        if (! $member) {

            $this->dispatch('swal:error', [
                'title' => 'Not Found',
                'text' => 'Member not found.',
            ]);

            return;
        }

        // Only RoleID 8
        if ((int) $member->RoleID !== 8) {

            $this->dispatch('swal:error', [
                'title' => 'Invalid Member',
                'text' => 'This user is not a satellite member.',
            ]);

            return;
        }

        $password = (string) $password;

        $validator = Validator::make(
            ['password' => $password],
            [
                'password' => [
                    'required',
                    'string',
                    Password::default(),
                ],
            ]
        );

        if ($validator->fails()) {

            $this->dispatch('swal:error', [
                'title' => 'Password Requirements Not Met',
                'text' => $validator
                    ->errors()
                    ->first('password'),
            ]);

            return;
        }

        try {

            $member->update([
                'password' => $password,
            ]);

            Log::info(
                'Admin reset satellite member password',
                [
                    'member_user_id' => $member->UserID,
                    'reset_by' => Auth::id(),
                ]
            );

            $emailSent = false;

            if (filled($member->email)) {

                try {

                    Mail::to($member->email)->send(
                        new PasswordResetConfirmation(
                            $member,
                            $password
                        )
                    );

                    $emailSent = true;

                } catch (\Throwable $e) {

                    Log::error(
                        'Failed to email satellite member about password reset',
                        [
                            'member_user_id' => $member->UserID,
                            'error' => $e->getMessage(),
                        ]
                    );
                }
            }

            $name = trim(
                ($member->firstname ?? '').
                ' '.
                ($member->lastname ?? '')
            );

            $text = $emailSent

                ? "{$name}'s password has been changed successfully. "
                    .'A confirmation email containing the new password was '
                    ."sent to {$member->email}."

                : "{$name}'s password has been changed successfully to: "
                    ."{$password}. No email is on file, so please share the "
                    .'new password with the member.';

            $this->dispatch('swal:success', [
                'title' => 'Password Updated!',
                'text' => $text,
            ]);

        } catch (\Throwable $e) {

            Log::error(
                'Satellite member password reset failed',
                [
                    'member_user_id' => $member->UserID,
                    'error' => $e->getMessage(),
                ]
            );

            $this->dispatch('swal:error', [
                'title' => 'Error',
                'text' => 'Something went wrong while updating the password.',
            ]);
        }
    }

    // =====================================================
    // ACTIVATION BROADCAST
    // =====================================================

    public function handleActivationBroadcast($event): void
    {
        $this->skipRender = false;
    }

    // =====================================================
    // OPEN WITHDRAWAL MODAL
    // =====================================================

    public function openWithdrawalModal(int $userId): void
    {
        if (! $this->canCreate) {

            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to create a withdrawal request.',
            ]);

            return;
        }

        $member = User::find($userId);

        if (! $member) {

            $this->dispatch('swal:error', [
                'title' => 'Not Found',
                'text' => 'Member not found.',
            ]);

            return;
        }

        // Only RoleID 8
        if ((int) $member->RoleID !== 8) {

            $this->dispatch('swal:error', [
                'title' => 'Invalid Member',
                'text' => 'This user is not a satellite member.',
            ]);

            return;
        }

        $this->dispatch(
            'open-admin-withdrawal-modal',
            userID: $member->UserID
        );
    }

    // =====================================================
    // OPEN WITHDRAWAL APPROVAL
    // =====================================================

    public function openWithdrawalApproval($id): void
    {
        if (! $this->canUpdate) {
            return;
        }

        Log::info(
            'Satellite withdrawal approval clicked',
            [
                'UserID' => $id,
                'reviewed_by' => Auth::id(),
            ]
        );

        $member = User::find($id);

        if (! $member) {

            Log::warning(
                'Satellite withdrawal approval failed: member not found.',
                [
                    'UserID' => $id,
                ]
            );

            return;
        }

        // Only RoleID 8
        if ((int) $member->RoleID !== 8) {
            return;
        }

        $withdrawal = $member->withdrawals()
            ->where('status', 'Pending')
            ->latest('WithdrawID')
            ->first();

        if (! $withdrawal) {

            $this->dispatch('swal:error', [
                'title' => 'No Pending Request',
                'text' => 'No pending withdrawal request found for this member.',
            ]);

            return;
        }

        $this->selectedWithdrawalUserId =
            $member->UserID;

        $this->selectedWithdrawalUser =
            $member;

        $this->selectedWithdrawal =
            $withdrawal;

        $this->isWithdrawalModalOpen = true;
    }

    // =====================================================
    // CLOSE WITHDRAWAL MODAL
    // =====================================================

    public function closeWithdrawalModal(): void
    {
        $this->isWithdrawalModalOpen = false;

        $this->selectedWithdrawalUserId = null;

        $this->selectedWithdrawalUser = null;

        $this->selectedWithdrawal = null;
    }

    // =====================================================
    // CONFIRM WITHDRAWAL REJECTION
    // =====================================================

    public function confirmWithdrawalRejection(): void
    {
        if (! $this->canUpdate) {

            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to reject withdrawals.',
            ]);

            return;
        }

        if (! $this->selectedWithdrawal) {

            $this->dispatch('swal:error', [
                'title' => 'No Withdrawal Selected',
                'text' => 'Please select a withdrawal request first.',
            ]);

            return;
        }

        $this->dispatch(
            'confirm-withdrawal-rejection'
        );
    }

    // =====================================================
    // REJECT WITHDRAWAL
    // =====================================================

    public function rejectWithdrawal(): void
    {
        if (! $this->canUpdate) {

            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to reject withdrawals.',
            ]);

            return;
        }

        if (! $this->selectedWithdrawal) {
            return;
        }

        try {

            DB::transaction(function () {

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

                $withdrawal->update([
                    'status' => 'Rejected',
                    'confirmed_by' => Auth::user()?->UserID,
                ]);
            });

            $this->closeWithdrawalModal();

            $this->dispatch('swal:success', [
                'title' => 'Withdrawal Rejected',
                'text' => 'The withdrawal request has been rejected successfully.',
            ]);

            $this->skipRender = false;

        } catch (\Exception $e) {

            report($e);

            Log::error(
                'Satellite withdrawal rejection failed: '.
                $e->getMessage()
            );

            $this->dispatch('swal:error', [
                'title' => 'Error',
                'text' => 'Something went wrong while rejecting the withdrawal.',
            ]);
        }
    }

    // =====================================================
    // APPROVE & RELEASE WITHDRAWAL
    // =====================================================

    public function processWithdrawalApproval(): void
    {
        if (! $this->canUpdate) {

            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to approve withdrawals.',
            ]);

            return;
        }

        if (! $this->selectedWithdrawal) {
            return;
        }

        try {

            DB::transaction(function () {

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

                // =================================================
                // ENSURE WITHDRAWAL BELONGS TO ROLE ID 8 USER
                // =================================================

                $member = User::find(
                    $withdrawal->UserID
                );

                if (
                    ! $member ||
                    (int) $member->RoleID !== 8
                ) {

                    throw new \Exception(
                        'Withdrawal does not belong to a satellite member.'
                    );
                }

                // =================================================
                // WITHDRAWAL VALUES
                // =================================================

                $requestedAmount =
                    (float) $withdrawal->requested_amount;

                $taxAmount =
                    (float) $withdrawal->tax_amount;

                $withdrawalAmount =
                    (float) $withdrawal->amount;

                // =================================================
                // APPROVE WITHDRAWAL
                // =================================================

                $withdrawal->update([
                    'status' => 'Approved',
                    'confirmed_by' => Auth::user()?->UserID,
                ]);

                // =================================================
                // WALLET ENTRY
                // =================================================

                Wallet::create([
                    'UserID' => $withdrawal->UserID,

                    'withdrawID' => $withdrawal->WithdrawID,

                    'amount' => -abs($requestedAmount),

                    'source' => 'WD',

                    'status' => 'Active',
                ]);

                // =================================================
                // LEDGER - WITHDRAWAL
                // =================================================

                Ledger::create([
                    'RefID' => $withdrawal->UserID,

                    'WithdrawID' => $withdrawal->WithdrawID,

                    'amount' => -abs($withdrawalAmount),

                    'Source' => 'WD',

                    'ActivationID' => null,

                    'status' => 'Completed',
                ]);

                // =================================================
                // LEDGER - TAX
                // =================================================

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

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Withdrawal approved and released successfully.',
            ]);

            $this->skipRender = false;

        } catch (\Exception $e) {

            report($e);

            Log::error(
                'Satellite withdrawal approval failed: '.
                $e->getMessage()
            );

            $this->dispatch('swal:error', [
                'title' => 'Error',
                'text' => 'Something went wrong while approving the withdrawal.',
            ]);
        }
    }

    // =====================================================
    // RESET / REFRESH TABLE
    // =====================================================

    public function resetTable(): void
    {
        $this->search = '';

        $this->summaryFilter = 'all';

        $this->dateRange = '';

        $this->exportYear = '';

        $this->exportMonth = '';

        $this->exportStatus = '';

        $this->resetPage();

        $this->skipRender = false;

        $this->dispatch('ranges-reset');
    }
}
