<?php

namespace App\Livewire\AdminPages\TransactionList;

use App\Livewire\Concerns\AuthorizesModules;
use App\Models\Activation;
use App\Models\Ledger;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Services\WithdrawalWindowService;
use App\Support\Modules;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;
use Livewire\WithPagination;

class Withdrawals extends Component
{
    use HasTabSkeleton;
    use AuthorizesModules;
    use WithPagination;

    public string $search = '';

    /*
    | Date-range filter, e.g. "2026-08-01 to 2026-08-16" — same flatpickr
    | pattern as the Inquiries tab (Masterlist). Empty means ALL TIME.
    */
    public string $dateRange = '';

    /*
    | Payment-method dropdown filter (withdrawal_method). Empty shows all.
    | Print honors EVERY active filter — date range, dropdown, and search.
    */
    public string $paymentFilter = '';

    /*
    | When true, the printable (unpaginated) report table is rendered into
    | the DOM for printing. Kept false at all other times — rendering every
    | row on every page load would be wasteful (same approach as the Report
    | & Settings Ledgers print).
    */
    public bool $printing = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPaymentFilter()
    {
        $this->resetPage();
    }

    public function resetTable(): void
    {
        $this->search = '';
        $this->dateRange = '';
        $this->paymentFilter = '';
        $this->resetPage();
        $this->dispatch('ranges-reset');
    }

    /*
    |--------------------------------------------------------------------------
    | PRINT — honors ALL active filters (search + payment dropdown + dates)
    |--------------------------------------------------------------------------
    | "Whatever is on screen is what prints": erase a filter and the print
    | goes back to printing everything again.
    |--------------------------------------------------------------------------
    */

    public function preparePrint(): void
    {
        if (! $this->can('print')) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to print withdrawals.',
            ]);

            return;
        }

        $this->printing = true;

        $this->dispatch('print-withdrawals');
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

    public function clearDateFilter(): void
    {
        $this->dateRange = '';
        $this->resetPage();
        $this->dispatch('ranges-reset');
    }

    /*
    |--------------------------------------------------------------------------
    | PERMISSIONS — global module system (Transaction List - Withdrawal)
    |--------------------------------------------------------------------------
    | Super Admin (RoleID 1) or a role holding the ModuleID 8 privilege with
    | can_change_status (or can_delete) may approve/reject withdrawals.
    | Everyone else sees only the row data (no action buttons in the blade).
    |--------------------------------------------------------------------------
    */

    protected function module(): int
    {
        return Modules::TXN_WITHDRAWALS;
    }

    public function getCanProcessProperty(): bool
    {
        return $this->can('change_status') || $this->can('delete');
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVAL WINDOW — approve/reject only every Monday, 12:00 AM - 11:59 PM
    |--------------------------------------------------------------------------
    | Requests may be filed Fri 5:00 PM - Sun 11:59 PM, but a pending request
    | is only acted on during Monday. Every approve/reject entry point checks
    | this guard, so the rule cannot be bypassed from the browser.
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

        Log::warning('Withdrawal approval blocked: outside the Monday approval window.', [
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
    | PENDING COUNTS — badges on the page-shell tab nav
    |--------------------------------------------------------------------------
    */

    public function getPendingWithdrawalsCountProperty(): int
    {
        return Withdrawal::query()->where('status', 'Pending')->count();
    }

    public function getPendingActivationsCountProperty(): int
    {
        return Activation::query()->where('status', 'Pending')->count();
    }

    /**
     * Keep the page-shell badges in sync after any processable change
     * (approve / reject) made from either tab.
     */
    private function broadcastBadgeRefresh(): void
    {
        $this->dispatch('txn-badges-updated',
            activations: $this->pendingActivationsCount,
            withdrawals: $this->pendingWithdrawalsCount,
        );
    }

    #[On('members-refresh')]
    public function refreshAfterModalAction(): void
    {
        // Approve/reject fired from the Members page review modals — the
        // method call itself round-trips and re-renders rows + badges.
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE & RELEASE WITHDRAWAL
    | Same DB effects as the SatelliteMembers approval modal: status +
    | confirmed_by, WD wallet debit on the requested amount, WD ledger on the
    | net amount, and the 10% tax back onto the ledger as a TX row.
    |--------------------------------------------------------------------------
    */

    public function approveWithdrawal(int $withdrawalId): void
    {
        if ($this->approvalWindowBlocked()) {
            return;
        }

        if (! $this->canProcess) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to approve withdrawals.',
            ]);

            return;
        }

        try {
            DB::transaction(function () use ($withdrawalId) {
                $withdrawal = Withdrawal::where('WithdrawID', $withdrawalId)
                    ->lockForUpdate()
                    ->first();

                if (! $withdrawal || $withdrawal->status !== 'Pending') {
                    throw new \RuntimeException('Withdrawal is no longer pending.');
                }

                $requestedAmount = (float) $withdrawal->requested_amount;
                $taxAmount = (float) $withdrawal->tax_amount;
                $withdrawalAmount = (float) $withdrawal->amount;

                $withdrawal->update([
                    'status' => 'Approved',
                    'confirmed_by' => Auth::user()?->UserID,
                ]);

                // Wallet entry: the requested amount leaves the wallet.
                Wallet::create([
                    'UserID' => $withdrawal->UserID,
                    'withdrawID' => $withdrawal->WithdrawID,
                    'amount' => -abs($requestedAmount),
                    'source' => 'WD',
                    'status' => 'Active',
                ]);

                // Ledger - withdrawal (net amount).
                Ledger::create([
                    'RefID' => $withdrawal->UserID,
                    'WithdrawID' => $withdrawal->WithdrawID,
                    'amount' => -abs($withdrawalAmount),
                    'Source' => 'WD',
                    'ActivationID' => null,
                    'status' => 'Completed',
                ]);

                // Ledger - tax (TX) is not recorded for RoleID 5, 6, or 7.
                $memberRoleID = (int) $withdrawal->user()->value('RoleID');
                // Ledger - tax (returned to the ledger as a TX row).

                if (! in_array($memberRoleID, [5, 6, 7, 9], true)) {
                Ledger::create([
                    'RefID' => $withdrawal->UserID,
                    'WithdrawID' => $withdrawal->WithdrawID,
                    'amount' => $taxAmount,
                    'Source' => 'TX',
                    'ActivationID' => null,
                    'status' => 'Completed',
                ]);

                }
            });

        } catch (\RuntimeException $e) {
            Log::warning('Transaction-list withdrawal approval aborted.', [
                'WithdrawID' => $withdrawalId,
                'reason' => $e->getMessage(),
                'reviewed_by' => Auth::id(),
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Cannot Approve',
                'text' => 'This withdrawal request has already been processed.',
            ]);

            return;
        } catch (\Exception $e) {
            report($e);

            Log::error('Transaction-list withdrawal approval failed: '.$e->getMessage());

            $this->dispatch('swal:error', [
                'title' => 'Error',
                'text' => 'Something went wrong while approving the withdrawal.',
            ]);

            return;
        }

        Log::info('Withdrawal approved from Transaction List.', [
            'WithdrawID' => $withdrawalId,
            'reviewed_by' => Auth::id(),
        ]);

        $this->broadcastBadgeRefresh();

        // Closes the review modal when the approval was fired from it
        // (a no-op broadcast when the modal is not open).
        $this->dispatch('withdrawal-processed');

        $this->dispatch('swal:success', title: 'Success!', message: 'Withdrawal approved and released successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | ROW ACTION — review first (details before decision)
    |--------------------------------------------------------------------------
    | The only row action: open the withdrawal review modal pinned to the
    | exact row. Approve/reject happen from inside that modal, so the
    | reviewer always sees the full request details before deciding.
    |--------------------------------------------------------------------------
    */

    public function reviewWithdrawal(int $withdrawalId): void
    {
        if (! $this->canProcess) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to review withdrawals.',
            ]);

            return;
        }

        $withdrawal = Withdrawal::find($withdrawalId);

        if (! $withdrawal) {
            $this->dispatch('swal:error', [
                'title' => 'Not Found',
                'text' => 'This withdrawal could not be found.',
            ]);

            return;
        }

        $this->dispatch('open-withdrawal-review', withdrawalId: $withdrawalId);
    }

    /*
    |--------------------------------------------------------------------------
    | CONFIRM GATES (typed dialogs are driven by the page shell)
    |--------------------------------------------------------------------------
    */

    public function confirmApproveWithdrawal(int $withdrawalId): void
    {
        if ($this->approvalWindowBlocked()) {
            return;
        }

        if (! $this->canProcess) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to approve withdrawals.',
            ]);

            return;
        }

        $withdrawal = Withdrawal::find($withdrawalId);

        if (! $withdrawal || $withdrawal->status !== 'Pending') {
            $this->dispatch('swal:error', [
                'title' => 'Already Processed',
                'text' => 'This withdrawal request has already been processed.',
            ]);

            return;
        }

        $this->dispatch('txn-approve-withdrawal-prompt',
            withdrawalId: $withdrawalId,
            memberName: trim(($withdrawal->user?->lastname ?? '').' '.($withdrawal->user?->firstname ?? '')) ?: 'this member',
            amount: number_format((float) $withdrawal->amount, 2),
        );
    }

    public function confirmRejectWithdrawal(int $withdrawalId): void
    {
        if ($this->approvalWindowBlocked()) {
            return;
        }

        if (! $this->canProcess) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to reject withdrawals.',
            ]);

            return;
        }

        $withdrawal = Withdrawal::find($withdrawalId);

        if (! $withdrawal || $withdrawal->status !== 'Pending') {
            $this->dispatch('swal:error', [
                'title' => 'Already Processed',
                'text' => 'This withdrawal request has already been processed.',
            ]);

            return;
        }

        $this->dispatch('txn-reject-withdrawal-prompt', withdrawalId: $withdrawalId);
    }

    #[On('txn-reject-withdrawal')]
    public function rejectWithdrawal($withdrawalId): void
    {
        if ($this->approvalWindowBlocked()) {
            return;
        }

        if (! $this->canProcess) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to reject withdrawals.',
            ]);

            return;
        }

        try {
            DB::transaction(function () use ($withdrawalId) {
                $withdrawal = Withdrawal::where('WithdrawID', (int) $withdrawalId)
                    ->lockForUpdate()
                    ->first();

                if (! $withdrawal || $withdrawal->status !== 'Pending') {
                    throw new \RuntimeException('Withdrawal is no longer pending.');
                }

                $withdrawal->update([
                    'status' => 'Rejected',
                    'confirmed_by' => Auth::user()?->UserID,
                ]);
            });
        } catch (\RuntimeException $e) {
            Log::warning('Transaction-list withdrawal rejection aborted.', [
                'WithdrawID' => $withdrawalId,
                'reason' => $e->getMessage(),
                'reviewed_by' => Auth::id(),
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Already Processed',
                'text' => 'This withdrawal request has already been processed.',
            ]);

            return;
        } catch (\Exception $e) {
            report($e);

            Log::error('Transaction-list withdrawal rejection failed: '.$e->getMessage());

            $this->dispatch('swal:error', [
                'title' => 'Error',
                'text' => 'Something went wrong while rejecting the withdrawal.',
            ]);

            return;
        }

        Log::info('Withdrawal rejected from Transaction List.', [
            'WithdrawID' => $withdrawalId,
            'reviewed_by' => Auth::id(),
        ]);

        $this->broadcastBadgeRefresh();

        // Closes the review modal when the rejection was fired from it
        // (a no-op broadcast when the modal is not open).
        $this->dispatch('withdrawal-processed');

        $this->dispatch('swal:success', title: 'Withdrawal Rejected', message: 'The withdrawal request has been rejected successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHARED QUERY — paginated table AND print export use the SAME filters
    |--------------------------------------------------------------------------
    | Search, payment-method dropdown, and date range all apply to both, so
    | the printed report always matches what is on screen.
    |--------------------------------------------------------------------------
    */
    protected function baseQuery()
    {
        [$dateFrom, $dateTo] = $this->parsedDateRange();
        $search = trim($this->search);
        $method = trim($this->paymentFilter);

        return Withdrawal::query()
            ->with('user')
            ->when($method !== '', fn ($query) => $query->where('withdrawal_method', $method))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('WithdrawID', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                        ->orWhere('withdrawal_method', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('payment_reference', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($u) use ($search) {
                            $u->where('firstname', 'like', "%{$search}%")
                                ->orWhere('lastname', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%");
                        });
                });
            })
            ->when($dateFrom && $dateTo, function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [
                    Carbon::parse($dateFrom)->startOfDay(),
                    Carbon::parse($dateTo)->endOfDay(),
                ]);
            });
    }


    public function render()
    {
        [$dateFrom, $dateTo] = $this->parsedDateRange();

        $withdrawals = (clone $this->baseQuery())
            ->latest('WithdrawID')
            ->paginate(10);

        // Unpaginated set for the printable report, matching the same filters —
        // only queried when the user actually prints (keeps the page light).
        $printWithdrawals = $this->printing
            ? (clone $this->baseQuery())->latest('WithdrawID')->get()
            : collect();

        return view('livewire.admin-pages.transaction-list.withdrawals', [
            'withdrawals' => $withdrawals,
            'printWithdrawals' => $printWithdrawals,
            'dateRangeLabel' => $dateFrom && $dateTo
                ? Carbon::parse($dateFrom)->format('M d, Y').' — '.Carbon::parse($dateTo)->format('M d, Y')
                : 'All Time',
        ]);
    }
}
