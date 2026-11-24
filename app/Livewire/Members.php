<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesModules;
use App\Mail\PasswordResetConfirmation;
use App\Models\Activation;
use App\Models\Ledger;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Services\WithdrawalWindowService;
use App\Support\Modules;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\On;
use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;
use Livewire\WithPagination;

class Members extends Component
{
    use HasTabSkeleton;
    use AuthorizesModules;
    use WithPagination;

    public string $search = '';

    public string $dateRange = '';   // e.g. "2026-08-01 to 2026-08-16"

    public string $summaryFilter = 'all';

    public string $statusFilter = 'all';

    // Excel Export Filters
    public $exportYear = '';

    public $exportMonth = '';

    public $exportStatus = '';

    public bool $skipRender = true;

    protected $paginationTheme = 'tailwind';

    // =====================================================
    // Withdrawal Approval State
    //
    // Withdrawal creation is handled by the reusable
    // App\Livewire\Components\WithdrawalModal, the same
    // way AdminUsers uses it — see openWithdrawalModal()
    // below. Approval/release stays local to this
    // component, same pattern as AdminUsers.
    // =====================================================

    public bool $isWithdrawalModalOpen = false;

    public ?int $selectedWithdrawalUserId = null;

    public ?User $selectedWithdrawalUser = null;

    public ?Withdrawal $selectedWithdrawal = null;

    public string $branchFilter = 'all';

    public function mount()
    {
        // Sets the default filter display to the current year
        $this->dateRange = '';
    }

    /*
    |--------------------------------------------------------------------------
    | Permission Checkers — global module system (Masterlist - Members)
    |--------------------------------------------------------------------------
    */

    protected function module(): int
    {
        return Modules::MEMBERS;
    }


    /**
     * Base query for every member lookup in this component.
     * This is the security boundary — it does NOT depend on the dropdown.
     */
    private function scopedMembers()
    {
        return User::query()
            ->where('RoleID', 8);
    }

    /**
     * Find one member.
     */
    private function findScopedMember($id): ?User
    {
        return $this->scopedMembers()
            ->where('UserID', $id)
            ->first();
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
    | Refresh table after a withdrawal is created
    |
    | WithdrawalModal is a separate Livewire component,
    | reused here in admin mode. When it dispatches
    | 'withdrawal-submitted', this listener re-renders
    | Members so the Pending/Withdraw state for the
    | affected row updates without a manual refresh.
    |--------------------------------------------------------------------------
    */

    #[On('withdrawal-submitted')]
    public function onWithdrawalSubmittedElsewhere(): void
    {
        Log::info('Members: withdrawal-submitted event received, refreshing table.');

        $this->skipRender = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Withdrawal Global Status
    |
    | Mirrors WithdrawalModal::isWithdrawalActive() and
    | AdminUsers::isWithdrawalActive(). Used by the view
    | to hide/disable the "Withdraw" creation action when
    | withdrawals are turned off system-wide.
    |--------------------------------------------------------------------------
    */

    public function isWithdrawalActive(): bool
    {
        return filter_var(
            Setting::where('name', 'activate_withdrawal')
                ->value('value'),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Withdrawal Request Window
    |
    | Requests are only accepted Friday 5:00 PM to Sunday 11:59 PM.
    | See App\Services\WithdrawalWindowService.
    |--------------------------------------------------------------------------
    */

    public function withdrawalWindow(): array
    {
        return app(WithdrawalWindowService::class)->status();
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVAL WINDOW — approve/reject only every Monday, 12:00 AM - 11:59 PM
    |--------------------------------------------------------------------------
    */

    public function approvalWindow(): array
    {
        return app(WithdrawalWindowService::class)->approvalStatus();
    }

    private function approvalWindowBlocked(): bool
    {
        $window = $this->approvalWindow();

        if ($window['open']) {
            return false;
        }

        Log::warning('Member withdrawal approval blocked: outside the Monday approval window.', [
            'caller' => Auth::id(),
        ]);

        $this->dispatch('swal:error', [
            'title' => 'Approvals Closed',
            'text' => $window['message'],
        ]);

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Render Members
    |--------------------------------------------------------------------------
    */


    public function render()
    {
        // Mark any approval that has passed its validity period as
        // 'Expired' BEFORE reading the table, so the action buttons
        // and status badges reflect reality on every load.
        $this->expireLapsedActivations();

        // Total members across all branches
        $totalMembers = $this->scopedMembers()->count();

        // Total members per branch (including branches with zero members)
        $branchMemberCounts = DB::table('branches')
            ->leftJoin('users', function ($join) {
                $join->on('branches.BranchID', '=', 'users.BranchID')
                    ->where('users.RoleID', 8);
            })
            ->select(
                'branches.BranchID',
                'branches.branchName',
                DB::raw('COUNT(users.UserID) as total_members')
            )
            ->groupBy('branches.BranchID', 'branches.branchName')
            ->orderBy('branches.branchName')
            ->get();

        [$dateFrom, $dateTo] = $this->parsedDateRange();


        $members = $this->scopedMembers()
                ->leftJoin('branches', 'users.BranchID', '=', 'branches.BranchID')
                ->select('users.*', 'branches.branchName as branch_name')
                ->when($this->branchFilter !== 'all', function ($query) {
                    $query->where('users.BranchID', $this->branchFilter);
                })
            ->withExists([
                'activations as has_pending_activation' => function ($query) {
                    $query->where('status', 'Pending');
                },
                'withdrawals as has_pending_withdrawal' => function ($query) {
                    $query->where('status', 'Pending');
                },
                'activations as has_expired_activation' => function ($query) {
                    $query->where('status', 'Expired');
                },
                'activations as has_rejected_activation' => function ($query) {
                    $query->where('status', 'Rejected');
                },
            ])
            ->withCount([
                'withdrawals as pending_withdrawals_count' => function ($query) {
                    $query->where('status', 'Pending');
                },
            ])
            ->when(filled($this->search), function ($query) {
                $words = array_filter(
                    explode(' ', trim(preg_replace('/[\s,]+/', ' ', $this->search)))
                );

                foreach ($words as $word) {
                    $safe   = addcslashes($word, '%_\\');
                    $like   = "%{$safe}%";
                    $prefix = "{$safe}%";

                    $query->where(function ($q) use ($like, $prefix) {
                        $q->where('firstname', 'like', $like)
                            ->orWhere('middlename', 'like', $like)
                            ->orWhere('lastname', 'like', $like)
                            ->orWhere('username', 'like', $like)
                            ->orWhere('referralCode', 'like', $prefix)
                            ->orWhere('policy_number', 'like', $like)
                            ->orWhereHas('activations', function ($activation) use ($like) {
                                $activation->where('policy_number', 'like', $like)
                                    ->orWhere('receipt_number', 'like', $like)
                                    ->orWhere('cocogen_policy_number', 'like', $like);
                            });
                    });
                }
            })
            ->when($dateFrom && $dateTo, function ($query) use ($dateFrom, $dateTo) {
                $query->whereHas('activations.histories', function ($h) use ($dateFrom, $dateTo) {
                    $h->where('status', 'Approved')
                    ->whereBetween('created_at', [
                        Carbon::parse($dateFrom)->startOfDay(),
                        Carbon::parse($dateTo)->endOfDay(),
                    ]);
                });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderByDesc('users.created_at')
            ->paginate(10);

        $isSatelliteApplicationActive = Setting::where(
            'name',
            'activate_satellite_application'
        )->value('value');

        $isSatelliteApplicationActive = filter_var(
            $isSatelliteApplicationActive,
            FILTER_VALIDATE_BOOLEAN
        );

        return view('livewire.admin-pages.masterlist.members', [
            'members' => $members,
            'totalMembers' => $totalMembers,
            'branchMemberCounts' => $branchMemberCounts,
            'isWithdrawalActive' => $this->isWithdrawalActive(),
            'withdrawalWindow' => $this->withdrawalWindow(),
            'isSatelliteApplicationActive' => $isSatelliteApplicationActive,
            'branches' => DB::table('branches')
                ->orderBy('branchName')
                ->get(),
        ]);
    }

    public function updatedBranchFilter(): void
    {
        $this->resetPage();
    }

    protected function parsedDateRange(): array
    {
        if (blank($this->dateRange) || ! str_contains($this->dateRange, ' to ')) {
            return [null, null];
        }

        [$from, $to] = explode(' to ', $this->dateRange, 2);

        return [trim($from) ?: null, trim($to) ?: null];
    }

    public function updatedDateRange()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function clearDateFilter(): void
    {
        $this->dateRange = '';
        $this->resetPage();
        $this->dispatch('ranges-reset');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Auto-Expire Lapsed Memberships
    |
    | An Approved activation older than the configured validity period
    | (membership_validity_months, SettingsID 5 — default 12) is marked
    | 'Expired'. The member stays 'Inactive' in users, and the Members
    | table then offers "Renew" instead of "Deactivate".
    |
    | Idempotent and cheap — a single UPDATE, safe to call on every render.
    |--------------------------------------------------------------------------
    */
    public function expireLapsedActivations(): void
    {
        $validityMonths = max(1, (int) (
            Setting::where('SettingsID', 5)->value('value') ?: 12
        ));

        Activation::query()
            ->where('status', 'Approved')
            ->where('created_at', '<', now()->subMonths($validityMonths))
            ->update(['status' => 'Expired']);
    }

    /*
    |--------------------------------------------------------------------------
    | Open Activation Review Modal
    |--------------------------------------------------------------------------
    */

    public function openReview($id): void
    {
        Log::info('Activation review clicked', ['UserID' => $id, 'reviewed_by' => Auth::id()]);

        $member = $this->findScopedMember($id);

        if (! $member) {
            Log::warning('Review failed: member not found.', ['UserID' => $id]);

            return;
        }

        $this->dispatch('open-activation-review', UserID: $member->UserID);
    }

    /*
    |--------------------------------------------------------------------------
    | Satellite Subscription Modal
    |--------------------------------------------------------------------------
    */

    public function openSatellite($id): void
    {
        $member = $this->findScopedMember($id);

        if (! $member) {
            Log::warning('Satellite modal failed: member not found.', ['UserID' => $id]);

            $this->dispatch('swal:error', [
                'title' => 'Not Found',
                'text' => 'Member not found.',
            ]);

            return;
        }

        $this->dispatch('open-satellite-modal', UserID: $member->UserID);
    }

    /*
    |--------------------------------------------------------------------------
    | Activate / Deactivate Confirmation
    |--------------------------------------------------------------------------
    */

    public function confirmStatus($id): void
    {
        $member = $this->findScopedMember($id);

        if (! $member) {
            Log::warning('Status update failed: member not found.', ['UserID' => $id]);

            return;
        }

        $action = match (strtolower($member->status)) {
            // Active → Suspend (replaces the old Deactivate)
            'active' => 'suspend',

            // Suspended → allowed to come back via the same dialog
            'suspended' => 'activate',

            default => 'activate',
        };

        $this->dispatch(
            'members-confirm-status',
            id: $member->UserID,
            action: $action,
            name: collect([$member->firstname, $member->middlename, $member->lastname])
                ->filter()
                ->implode(' '),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Member Status
    |--------------------------------------------------------------------------
    */

    public function updateStatus($id): void
    {

        Log::info('Updating member status', ['UserID' => $id, 'updated_by' => Auth::id()]);

        $member = $this->findScopedMember($id);

        if (! $member) {
            Log::warning('Status update failed: member not found.', ['UserID' => $id]);

            return;
        }

        $current = strtolower($member->status);

        // Active → Suspended (admin suspension).
        // Suspended → back to Active (unsuspend via the same dialog).
        // Anything else (Inactive) → Active.
        $member->status = $current === 'active' ? 'Suspended' : 'Active';
        $member->save();

        $fullName = collect([$member->firstname, $member->middlename, $member->lastname])
            ->filter()
            ->implode(' ');

        Log::info('Member status updated successfully', ['UserID' => $member->UserID, 'new_status' => $member->status]);

        $text = match ($member->status) {
            'Suspended' => "{$fullName} has been suspended successfully.",
            'Active' => $current === 'suspended'
                ? "{$fullName}'s account has been reactivated successfully."
                : "{$fullName} has been activated successfully.",
            default => "{$fullName}'s status has been updated successfully.",
        };

        $this->dispatch('swal:success', [
            'title' => 'Success!',
            'text' => $text,
        ]);

        $this->dispatch('members-refresh');
    }

    /*
    |--------------------------------------------------------------------------
    | Open Renewal Modal
    |
    | A member whose activation has EXPIRED renews from here: the
    | ActivationModal opens pre-filled and files a brand-new
    | activation (Pending) once confirmed — the expired row is
    | kept as history, never overwritten.
    |--------------------------------------------------------------------------
    */
    public function openRenewal($id): void
    {

        Log::info('Admin renewal clicked', ['target_UserID' => $id, 'opened_by' => Auth::id()]);

        $member = $this->findScopedMember($id);

        if (! $member) {
            Log::warning('Renewal failed. Member not found.', ['target_UserID' => $id]);

            return;
        }

        $this->dispatch('open-activation-modal', UserID: $member->UserID, renew: true);
    }

    /*
    |--------------------------------------------------------------------------
    | Open Reactivate Modal
    |
    | A member whose latest activation was REJECTED re-enters here: the
    | ActivationModal opens in reactivate mode (pre-filled from the rejected
    | request, submit button "ReActivate Account") and filing it flips the
    | SAME activation row back to Pending — no duplicate request is created.
    |--------------------------------------------------------------------------
    */
    public function openReactivate($id): void
    {
        if (! $this->canUpdate) {
            return;
        }

        Log::info('Admin reactivate clicked', ['target_UserID' => $id, 'opened_by' => Auth::id()]);

        $member = $this->findScopedMember($id);

        if (! $member) {
            Log::warning('Reactivate failed. Member not found.', ['target_UserID' => $id]);

            return;
        }

        $this->dispatch('open-rejected-activation', UserID: $member->UserID);
    }

    /*
    |--------------------------------------------------------------------------
    | Open Activation Modal
    |--------------------------------------------------------------------------
    */

    public function openActivation($id): void
    {

        Log::info('Admin activation clicked', ['target_UserID' => $id, 'processed_by' => Auth::id()]);

        $member = $this->findScopedMember($id);

        if (! $member) {
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

        $member = $this->findScopedMember($id);

        if (! $member) {
            Log::warning('Edit member not found.', ['UserID' => $id]);

            return;
        }

        $this->dispatch('open-update-member', UserID: $member->UserID);
    }

    public function viewMember($id): void
    {

        $member = $this->findScopedMember($id);

        if (! $member) {
            return;
        }

        $this->dispatch('open-view-member', UserID: $member->UserID);
    }

    /*
    |--------------------------------------------------------------------------
    | Set New Password (Admin-Forced Password Reset)
    |--------------------------------------------------------------------------
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

    public function promptMemberPasswordReset($id): void
    {

        $member = $this->findScopedMember($id);

        if (! $member) {
            $this->dispatch('swal:error', [
                'title' => 'Not Found',
                'text' => 'Member not found.',
            ]);

            return;
        }

        $policy = $this->passwordPolicy();

        $this->dispatch(
            'set-member-password',
            id: $member->UserID,
            name: trim(($member->firstname ?? '').' '.($member->lastname ?? '')),
            policy: $policy,
            hint: $this->passwordPolicyHint($policy),
        );
    }

    /**
     * Persist the new password (called from the SweetAlert confirm dialog).
     * Guarded server-side: permissions, existence, branch scope, and
     * password rules are re-validated here so this can never be triggered
     * without authorization.
     */
    public function resetMemberPassword($id, $password): void
    {

        $member = $this->findScopedMember($id);

        if (! $member) {
            $this->dispatch('swal:error', [
                'title' => 'Not Found',
                'text' => 'Member not found.',
            ]);

            return;
        }

        $password = (string) $password;

        // Validate against the exact same rule used by self-service
        // resets (Password::default()) instead of a hardcoded minimum.
        $validator = Validator::make(
            ['password' => $password],
            ['password' => ['required', 'string', Password::default()]]
        );

        if ($validator->fails()) {
            $this->dispatch('swal:error', [
                'title' => 'Password Requirements Not Met',
                'text' => $validator->errors()->first('password'),
            ]);

            return;
        }

        try {
            // The User model casts 'password' as 'hashed', so it is
            // automatically bcrypt-hashed before it hits the database.
            $member->update([
                'password' => $password,
            ]);

            Log::info('Admin reset member password', [
                'member_user_id' => $member->UserID,
                'reset_by' => Auth::id(),
            ]);

            // Notify the member by email when one is on file, using the same
            // branded confirmation template as the self-service reset flow.
            // The email itself carries the new password.
            $emailSent = false;

            if (filled($member->email)) {
                try {
                    Mail::to($member->email)->send(
                        new PasswordResetConfirmation($member, $password)
                    );

                    $emailSent = true;
                } catch (\Throwable $e) {
                    Log::error('Failed to email member about password reset', [
                        'member_user_id' => $member->UserID,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $name = trim(
                ($member->firstname ?? '').' '.($member->lastname ?? '')
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
            Log::error('Member password reset failed', [
                'member_user_id' => $member->UserID,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Error',
                'text' => 'Something went wrong while updating the password.',
            ]);
        }
    }

    public function handleActivationBroadcast($event): void
    {
        // This method will automatically trigger in real-time
        // whenever an activation event is broadcasted on the channel.

        // You can optionally target or just refresh the component:
        $this->skipRender = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Withdrawals
    |
    | Withdrawal creation:
    | Handled by the reusable WithdrawalModal component,
    | the same way AdminUsers uses it.
    |
    | Withdrawal approval/release:
    | Handled here directly, same pattern as AdminUsers.
    |--------------------------------------------------------------------------
    */

    public function openWithdrawalModal(int $userId): void
    {

        if (! $this->withdrawalWindow()['open']) {

            $this->dispatch('swal:error', [
                'title' => 'Withdrawal Window Closed',
                'text' => $this->withdrawalWindow()['message'],
            ]);

            return;
        }

        $member = $this->findScopedMember($userId);

        if (! $member) {

            $this->dispatch('swal:error', [
                'title' => 'Not Found',
                'text' => 'Member not found.',
            ]);

            return;
        }

        // Tell the reusable WithdrawalModal
        // to use this selected member's wallet.
        $this->dispatch(
            'open-admin-withdrawal-modal',
            userID: $member->UserID
        );
    }

    public function openWithdrawalApproval($id): void
    {

        $member = $this->findScopedMember($id);

        if (! $member) {
            Log::warning('Withdrawal approval failed: member not found.', ['UserID' => $id]);

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

        $this->selectedWithdrawalUserId = $member->UserID;
        $this->selectedWithdrawalUser = $member;
        $this->selectedWithdrawal = $withdrawal;

        $this->isWithdrawalModalOpen = true;
    }

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

        if ($this->approvalWindowBlocked()) {
            return;
        }

        if (! $this->selectedWithdrawal) {

            $this->dispatch('swal:error', [
                'title' => 'No Withdrawal Selected',
                'text' => 'Please select a withdrawal request first.',
            ]);

            return;
        }

        $this->dispatch('confirm-withdrawal-rejection');
    }

    /*
    |--------------------------------------------------------------------------
    | Reject Withdrawal
    |--------------------------------------------------------------------------
    */

    public function rejectWithdrawal(): void
    {

        if ($this->approvalWindowBlocked()) {
            return;
        }

        if (! $this->selectedWithdrawal) {
            return;
        }

        try {

            DB::transaction(function () {

                // ==========================================
                // LOCK WITHDRAWAL
                // ==========================================

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

                // ==========================================
                // REJECT WITHDRAWAL
                //
                // No wallet deduction
                // No ledger transaction
                // ==========================================

                $withdrawal->update([
                    'status' => 'Rejected',

                    'confirmed_by' => Auth::user()?->UserID,
                ]);

            });

            // ==========================================
            // CLOSE MODAL
            // ==========================================

            $this->closeWithdrawalModal();

            // ==========================================
            // SUCCESS MESSAGE
            // ==========================================

            $this->dispatch('swal:success', [
                'title' => 'Withdrawal Rejected',
                'text' => 'The withdrawal request has been rejected successfully.',
            ]);

            // ==========================================
            // REFRESH TABLE
            // ==========================================

            $this->skipRender = false;

        } catch (\Exception $e) {

            report($e);

            Log::error(
                'Withdrawal rejection failed: '.$e->getMessage()
            );

            $this->dispatch('swal:error', [
                'title' => 'Error',
                'text' => 'Something went wrong while rejecting the withdrawal.',
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Approve & Release Withdrawal
    |--------------------------------------------------------------------------
    */

    public function processWithdrawalApproval(): void
    {

        if ($this->approvalWindowBlocked()) {
            return;
        }

        if (! $this->selectedWithdrawal) {
            return;
        }

        try {

            DB::transaction(function () {

                // ==========================================
                // LOCK WITHDRAWAL
                // ==========================================

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

                // ==========================================
                // WITHDRAWAL VALUES
                //
                // Example:
                //
                // requested_amount = 1000
                // tax_amount       = 100
                // amount           = 900
                // ==========================================

                $requestedAmount = (float) $withdrawal->requested_amount;
                $taxAmount = (float) $withdrawal->tax_amount;
                $withdrawalAmount = (float) $withdrawal->amount;

                // ==========================================
                // APPROVE WITHDRAWAL
                // ==========================================

                $withdrawal->update([
                    'status' => 'Approved',

                    'confirmed_by' => Auth::user()?->UserID,
                ]);

                // ==========================================
                // WALLET ENTRY
                //
                // Example:
                // Withdrawal ₱500
                // Wallet = -₱500
                // Source = WD
                // ==========================================

                Wallet::create([
                    'UserID' => $withdrawal->UserID,

                    'withdrawID' => $withdrawal->WithdrawID,

                    'amount' => -abs($requestedAmount),

                    'source' => 'WD',

                    'status' => 'Active',
                ]);

                // ==========================================
                // LEDGER: WITHDRAWAL
                //
                // Record the NET withdrawal amount.
                //
                // Example:
                // amount = 900
                // Ledger WD = -900
                // Source = WD
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
                // LEDGER: TAX
                //
                // Use the tax_amount already stored
                // in the withdrawal record.
                //
                // Example:
                // tax_amount = 100
                // Ledger TX = +100
                // Source = TX
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

            // ==========================================
            // CLOSE MODAL
            // ==========================================

            $this->closeWithdrawalModal();

            // ==========================================
            // SUCCESS MESSAGE
            // ==========================================

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Withdrawal approved and released successfully.',
            ]);

            // Refresh table
            $this->skipRender = false;

        } catch (\Exception $e) {

            report($e);

            Log::error(
                'Withdrawal approval failed: '.$e->getMessage()
            );

            $this->dispatch('swal:error', [
                'title' => 'Error',
                'text' => 'Something went wrong while approving the withdrawal.',
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Reset / Refresh Table Action
    |--------------------------------------------------------------------------
    */
    public function resetTable(): void
    {
        $this->search = '';
        $this->summaryFilter = 'all';
        $this->statusFilter = 'all';
        $this->dateRange = '';           // Clear month filter
        $this->exportYear = '';
        $this->exportMonth = '';
        $this->exportStatus = '';
        $this->resetPage();
        $this->skipRender = false;
        $this->dispatch('ranges-reset');
    }
}
