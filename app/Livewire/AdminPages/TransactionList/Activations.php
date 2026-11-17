<?php

namespace App\Livewire\AdminPages\TransactionList;

use App\Livewire\Concerns\AuthorizesModules;
use App\Models\Activation;
use App\Models\Withdrawal;
use App\Services\ActivationApprovalService;
use App\Support\Modules;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;
use Livewire\WithPagination;

class Activations extends Component
{
    use HasTabSkeleton;
    use AuthorizesModules;
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    /*
    | Date-range filter, e.g. "2026-08-01 to 2026-08-16" — same flatpickr
    | pattern as the Inquiries tab (Masterlist). Empty means ALL TIME.
    */
    public string $dateRange = '';

    /*
    | Payment-method dropdown filter (payment_method). Empty shows all.
    | Print honors EVERY active filter — date range, dropdown, status, search.
    */
    public string $paymentFilter = '';

    /*
    | When true, the printable (unpaginated) report table is rendered into
    | the DOM for printing. Kept false at all other times (same approach as
    | the Report & Settings Ledgers print).
    */
    public bool $printing = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
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
        $this->statusFilter = '';
        $this->dateRange = '';
        $this->paymentFilter = '';
        $this->resetPage();
        $this->dispatch('ranges-reset');
    }

    /*
    |--------------------------------------------------------------------------
    | PRINT — honors ALL active filters (search + status + payment + dates)
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
                'text' => 'You do not have permission to print activations.',
            ]);

            return;
        }

        $this->printing = true;

        $this->dispatch('print-activations');
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
    | PERMISSIONS — global module system (Transaction List - Activations)
    |--------------------------------------------------------------------------
    | Super Admin (RoleID 1) or a role holding the ModuleID 9 privilege with
    | can_change_status (or can_delete) may approve/reject activations.
    | Everyone else sees only the row data (no action buttons in the blade).
    |--------------------------------------------------------------------------
    */

    protected function module(): int
    {
        return Modules::TXN_ACTIVATIONS;
    }

    public function getCanProcessProperty(): bool
    {
        return $this->can('change_status') || $this->can('delete');
    }

    /*
    |--------------------------------------------------------------------------
    | PENDING COUNTS — badges on the page-shell tab nav
    |--------------------------------------------------------------------------
    */

    public function getPendingActivationsCountProperty(): int
    {
        return Activation::query()->where('status', 'Pending')->count();
    }

    public function getPendingWithdrawalsCountProperty(): int
    {
        return Withdrawal::query()->where('status', 'Pending')->count();
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
        // Approve/reject/resubmit fired from the review modals or the
        // activation form modal — the method call itself round-trips and
        // re-renders rows, and badge counts may have changed (e.g. a
        // rejected activation resubmitted back to Pending), so re-broadcast
        // them for the page-shell tab nav.
        $this->broadcastBadgeRefresh();
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE ACTIVATION
    |--------------------------------------------------------------------------
    */

    public function approveActivation(int $activationId): void
    {
        if (! $this->canProcess) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to approve activations.',
            ]);

            return;
        }

        $activation = Activation::find($activationId);

        if (! $activation || $activation->status !== 'Pending') {
            $this->dispatch('swal:error', [
                'title' => 'Already Processed',
                'text' => 'This activation request has already been processed.',
            ]);

            return;
        }

        // Satellite activations keep their DR=100% commission flavor; all
        // other activations use the regular DR=90% + 10% TX split. Free
        // members (provisioned by a satellite) carry no payment at all and
        // run through the free flavor instead.
        $type = ActivationApprovalService::TYPE_REGULAR;

        if ($activation->isFree) {
            $type = ActivationApprovalService::TYPE_FREE;
        } elseif (filled($activation->satelliteID)) {
            $type = ActivationApprovalService::TYPE_SATELLITE;
        }

        $approved = app(ActivationApprovalService::class)->approve(
            $activation,
            (int) $activation->UserID,
            $type
        );

        if (! $approved) {
            Log::warning('Transaction-list activation approval aborted: no longer pending.', [
                'ActivationID' => $activationId,
                'reviewed_by' => Auth::id(),
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Already Processed',
                'text' => 'This activation request has already been processed.',
            ]);

            return;
        }

        Log::info('Activation approved from Transaction List.', [
            'ActivationID' => $activationId,
            'UserID' => $activation->UserID,
            'type' => $type,
            'reviewed_by' => Auth::id(),
        ]);

        $this->broadcastBadgeRefresh();

        $this->dispatch('swal:success', title: 'Success!', message: 'Activation approved successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | ROW ACTIONS — review / approve / reject via the shared review modals
    |--------------------------------------------------------------------------
    | Every action opens the full review modal first (details before
    | decision). TRIFECTA members get the regular modal, satellite members
    | the satellite one. Quick approve still uses the typed SwalFlow dialog
    | from the page shell; reject always goes through the modal so the
    | reviewer sees the request details before writing remarks.
    |--------------------------------------------------------------------------
    */

    public function reviewActivation(int $activationId): void
    {
        if (! $this->canProcess) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to review activations.',
            ]);

            return;
        }

        $activation = Activation::with('user')->find($activationId);

        if (! $activation) {
            $this->dispatch('swal:error', [
                'title' => 'Not Found',
                'text' => 'This activation could not be found.',
            ]);

            return;
        }

        // Satellite member (RoleID 8) → satellite review modal;
        // everyone else → the regular TRIFECTA review modal.
        // The exact activation row clicked is passed so the modal reviews
        // that request (important when a member has several requests).
        if ((int) ($activation->user?->RoleID ?? 0) === 8) {
            $this->dispatch('open-satellite-activation-review',
                UserID: (int) $activation->UserID,
                activationId: $activation->ActivationID,
            );
        } else {
            $this->dispatch('open-activation-review',
                UserID: (int) $activation->UserID,
                activationId: $activation->ActivationID,
            );
        }
    }

    /*
    | RESUBMIT a REJECTED activation — opens the activation FORM modal
    | (ActivationModal for TRIFECTA members, the satellite reapply wizard for
    | satellite members) pre-filled from the rejected request. The typed
    | confirmation happens on submit inside that modal; no separate
    | confirmation dialog is shown on click.
    */
    public function resubmitActivationRow(int $activationId): void
    {
        if (! $this->canProcess) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to resubmit activations.',
            ]);

            return;
        }

        $activation = Activation::with('user')->find($activationId);

        if (! $activation) {
            $this->dispatch('swal:error', [
                'title' => 'Not Found',
                'text' => 'This activation could not be found.',
            ]);

            return;
        }

        if ($activation->status !== 'Rejected') {
            $this->dispatch('swal:error', [
                'title' => 'Not Available',
                'text' => 'Resubmit is only available for rejected activations.',
            ]);

            return;
        }

        // Satellite member (RoleID 8) → the satellite reapply wizard;
        // everyone else → the regular activation modal in reactivate mode.
        if ((int) ($activation->user?->RoleID ?? 0) === 8) {
            $this->dispatch('open-satellite-reapply',
                UserID: (int) $activation->UserID,
            );
        } else {
            $this->dispatch('open-rejected-activation',
                UserID: (int) $activation->UserID,
            );
        }
    }

    public function confirmApproveActivation(int $activationId): void
    {
        if (! $this->canProcess) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to approve activations.',
            ]);

            return;
        }

        $activation = Activation::find($activationId);

        if (! $activation || $activation->status !== 'Pending') {
            $this->dispatch('swal:error', [
                'title' => 'Already Processed',
                'text' => 'This activation request has already been processed.',
            ]);

            return;
        }

        $this->dispatch('txn-approve-activation-prompt',
            activationId: $activationId,
            memberName: trim(($activation->user?->lastname ?? '').' '.($activation->user?->firstname ?? '')) ?: 'this member',
            amount: number_format((float) $activation->amount, 2),
        );
    }

    public function confirmRejectActivation(int $activationId): void
    {
        if (! $this->canProcess) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to reject activations.',
            ]);

            return;
        }

        $activation = Activation::with('user')->find($activationId);

        if (! $activation) {
            $this->dispatch('swal:error', [
                'title' => 'Not Found',
                'text' => 'This activation could not be found.',
            ]);

            return;
        }

        // Reject always shows the full details first — route through the
        // same review modal, pinned to the exact activation row.
        if ((int) ($activation->user?->RoleID ?? 0) === 8) {
            $this->dispatch('open-satellite-activation-review',
                UserID: (int) $activation->UserID,
                activationId: $activation->ActivationID,
            );
        } else {
            $this->dispatch('open-activation-review',
                UserID: (int) $activation->UserID,
                activationId: $activation->ActivationID,
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SHARED QUERY — paginated table AND print export use the SAME filters
    |--------------------------------------------------------------------------
    | Search, status, payment-method dropdown, and date range all apply to
    | both, so the printed report always matches what is on screen.
    |--------------------------------------------------------------------------
    */
    protected function baseQuery()
    {
        [$dateFrom, $dateTo] = $this->parsedDateRange();
        $search = trim($this->search);
        $status = trim($this->statusFilter);
        $method = trim($this->paymentFilter);

        return Activation::query()
            ->with('user')
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($method !== '', fn ($query) => $query->where('payment_method', $method))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ActivationID', 'like', "%{$search}%")
                        ->orWhere('receipt_number', 'like', "%{$search}%")
                        ->orWhere('payment_method', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
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

        $activations = (clone $this->baseQuery())
            ->latest('ActivationID')
            ->paginate(10);

        // Unpaginated set for the printable report, matching the same filters —
        // only queried when the user actually prints (keeps the page light).
        $printActivations = $this->printing
            ? (clone $this->baseQuery())->latest('ActivationID')->get()
            : collect();

        return view('livewire.admin-pages.transaction-list.activations', [
            'activations' => $activations,
            'printActivations' => $printActivations,
            'dateRangeLabel' => $dateFrom && $dateTo
                ? Carbon::parse($dateFrom)->format('M d, Y').' — '.Carbon::parse($dateTo)->format('M d, Y')
                : 'All Time',
        ]);
    }
}
