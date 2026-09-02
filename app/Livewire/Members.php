<?php

namespace App\Livewire;

use App\Models\Ledger;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Members extends Component
{
    use WithPagination;

    public string $search = '';
    public string $dateRange = '';   // e.g. "2026-08-01 to 2026-08-16"

    public string $summaryFilter = 'all';

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

    public function mount()
    {
        // Sets the default filter display to the current year
        $this->dateRange = '';
    }

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
    | Render Members
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        [$dateFrom, $dateTo] = $this->parsedDateRange();

        $members = User::query()
            ->where('RoleID', 4)
            ->withExists([
                'activations as has_pending_activation' => function ($query) {
                    $query->where('status', 'Pending');
                },
                'withdrawals as has_pending_withdrawal' => function ($query) {
                    $query->where('status', 'Pending');
                }
            ])
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
            ->when($dateFrom && $dateTo, function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [
                    Carbon::parse($dateFrom)->startOfDay(),
                    Carbon::parse($dateTo)->endOfDay(),
                ]);
            })
            ->latest()
            ->paginate(10);

        return view('components.admin-pages.masterlist.members', [
            'members' => $members,

            'isWithdrawalActive' => $this->isWithdrawalActive(),
        ]);
    }

    protected function parsedDateRange(): array
    {
        if (blank($this->dateRange) || !str_contains($this->dateRange, ' to ')) {
            return [null, null];
        }

        [$from, $to] = explode(' to ', $this->dateRange, 2);

        return [trim($from) ?: null, trim($to) ?: null];
    }

    public function updatedDateRange()
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

        $isActivating = strtolower($member->status) !== 'active';

        $member->status = strtolower($member->status) === 'active' ? 'Inactive' : 'Active';
        $member->save();

        Log::info('Member status updated successfully', ['UserID' => $member->UserID, 'new_status' => $member->status]);

        $this->dispatch('swal:success', [
            'title' => 'Success!',
            'text' => $isActivating
                ? 'Member has been activated successfully.'
                : 'Member has been deactivated successfully.',
        ]);

        $this->dispatch('members-refresh');
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
        if (!$this->canCreate) {

            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to create a withdrawal request.',
            ]);

            return;
        }

        $member = User::find($userId);

        if (!$member) {

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
        if (!$this->canUpdate) {
            return;
        }

        Log::info('Withdrawal approval clicked', ['UserID' => $id, 'reviewed_by' => Auth::id()]);

        $member = User::find($id);

        if (!$member) {
            Log::warning('Withdrawal approval failed: member not found.', ['UserID' => $id]);
            return;
        }

        $withdrawal = $member->withdrawals()
            ->where('status', 'Pending')
            ->latest('WithdrawID')
            ->first();

        if (!$withdrawal) {

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
        if (!$this->canUpdate) {

            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to reject withdrawals.',
            ]);

            return;
        }

        if (!$this->selectedWithdrawal) {

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
        // ==========================================
        // PERMISSION CHECK
        // ==========================================

        if (!$this->canUpdate) {

            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to reject withdrawals.',
            ]);

            return;
        }

        if (!$this->selectedWithdrawal) {
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
                    !$withdrawal ||
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
                'Withdrawal rejection failed: ' . $e->getMessage()
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
        // ==========================================
        // PERMISSION CHECK
        // ==========================================

        if (!$this->canUpdate) {

            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to approve withdrawals.',
            ]);

            return;
        }

        if (!$this->selectedWithdrawal) {
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
                    !$withdrawal ||
                    $withdrawal->status !== 'Pending'
                ) {
                    throw new \Exception(
                        'Withdrawal is no longer pending.'
                    );
                }

                // ==========================================
                // WITHDRAWAL AMOUNT
                // ==========================================

                $withdrawalAmount = (float) $withdrawal->amount;

                // ==========================================
                // CALCULATE 10% TRANSACTION
                //
                // Example:
                // Withdrawal = ₱500
                // TX = ₱50
                // ==========================================

                $transactionAmount = $withdrawalAmount * 0.10;

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

                    'amount' => -abs($withdrawalAmount),

                    'source' => 'WD',

                    'status' => 'Active',
                ]);

                // ==========================================
                // LEDGER: WITHDRAWAL
                //
                // Example:
                // Withdrawal ₱500
                // Ledger = -₱500
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
                // LEDGER: 10% TRANSACTION
                //
                // Example:
                // ₱500 × 10% = ₱50
                // Ledger = +₱50
                // Source = TX
                // ==========================================

                Ledger::create([
                    'RefID' => $withdrawal->UserID,

                    'WithdrawID' => $withdrawal->WithdrawID,

                    'amount' => $transactionAmount,

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
                'Withdrawal approval failed: ' . $e->getMessage()
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
        $this->dateRange = '';           // Clear month filter
        $this->exportYear = '';
        $this->exportMonth = '';
        $this->exportStatus = '';
        $this->resetPage();
        $this->skipRender = false;
        $this->dispatch('ranges-reset');
    }
}
