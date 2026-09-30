<?php

namespace App\Livewire\AdminPages\TransactionList;

use App\Livewire\Concerns\AuthorizesModules;
use App\Models\Activation;
use App\Models\Withdrawal;
use App\Services\ActivationApprovalService;
use App\Support\Modules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Activations extends Component
{
    use AuthorizesModules;
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function resetTable(): void
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->resetPage();
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
        // Approve/reject fired from the Members page review modals — the
        // method call itself round-trips and re-renders rows + badges.
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
        // other activations use the regular DR=90% + 10% TX split.
        $type = filled($activation->satelliteID)
            ? ActivationApprovalService::TYPE_SATELLITE
            : ActivationApprovalService::TYPE_REGULAR;

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
    | decision). TrueLife members get the regular modal, satellite members
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
        // everyone else → the regular TrueLife review modal.
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

    public function render()
    {
        $search = trim($this->search);
        $status = trim($this->statusFilter);

        $activations = Activation::query()
            ->with('user')
            ->when($status !== '', fn ($query) => $query->where('status', $status))
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
            ->latest('ActivationID')
            ->paginate(10);

        return view('livewire.admin-pages.transaction-list.activations', [
            'activations' => $activations,
        ]);
    }
}
