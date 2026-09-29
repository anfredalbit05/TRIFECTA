<?php

namespace App\Livewire\AdminPages\TransactionList;

use App\Models\Privilege;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class WithdrawalReviewModal extends Component
{
    public bool $open = false;

    public ?int $withdrawalId = null;

    public ?Withdrawal $withdrawal = null;

    /*
    | True only when the signed-in user is Super Admin (RoleID 1) or holds a
    | ModuleID 2 privilege with can_update OR can_delete — the same canProcess
    | rule as the tab components. Everyone else never sees the actions.
    */
    public bool $isPrivileged = false;

    #[On('open-withdrawal-review')]
    public function openModal(int $withdrawalId): void
    {
        $isSuperAdmin = (int) (Auth::user()?->RoleID ?? 0) === 1;

        $hasPrivilege = Privilege::where('RoleID', Auth::user()?->RoleID)
            ->where('ModuleID', 2)
            ->where(fn ($q) => $q->where('can_update', 1)->orWhere('can_delete', 1))
            ->exists();

        if (! ($isSuperAdmin || $hasPrivilege)) {
            Log::warning('Withdrawal review blocked: caller lacks privileges.', [
                'WithdrawID' => $withdrawalId,
                'caller' => Auth::id(),
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to review withdrawals.',
            ]);

            return;
        }

        $this->withdrawal = Withdrawal::with('user')->find($withdrawalId);

        if (! $this->withdrawal) {
            Log::warning('Withdrawal review failed: withdrawal not found.', [
                'WithdrawID' => $withdrawalId,
                'caller' => Auth::id(),
            ]);

            $this->withdrawalId = null;
            $this->withdrawal = null;

            $this->dispatch('swal:error', [
                'title' => 'Not Found',
                'text' => 'This withdrawal could not be found.',
            ]);

            return;
        }

        $this->withdrawalId = (int) $this->withdrawal->WithdrawID;

        $this->isPrivileged = $isSuperAdmin || $hasPrivilege;

        $this->open = true;
    }

    /*
    |--------------------------------------------------------------------------
    | CONFIRM GATES — typed dialogs from the page shell
    |--------------------------------------------------------------------------
    | Details are already visible (the modal itself); the typed APPROVE/REJECT
    | dialogs re-use the same global handlers the old row buttons used. After
    | the tab component processes the request it broadcasts
    | withdrawal-processed, which closes this modal and refreshes the rows.
    |--------------------------------------------------------------------------
    */

    public function confirmApprove(): void
    {
        if (! $this->isPrivileged) {
            Log::warning('Withdrawal approval blocked: user lacks privileges.', [
                'UserID' => Auth::id(),
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to approve withdrawals.',
            ]);

            return;
        }

        $withdrawal = Withdrawal::find($this->withdrawalId);

        if (! $withdrawal || $withdrawal->status !== 'Pending') {
            $this->dispatch('swal:error', [
                'title' => 'Already Processed',
                'text' => 'This withdrawal request has already been processed.',
            ]);

            return;
        }

        $this->dispatch('txn-approve-withdrawal-prompt',
            withdrawalId: $this->withdrawalId,
            memberName: trim(($withdrawal->user?->lastname ?? '').' '.($withdrawal->user?->firstname ?? '')) ?: 'this member',
            amount: number_format((float) $withdrawal->amount, 2),
        );
    }

    public function confirmReject(): void
    {
        if (! $this->isPrivileged) {
            Log::warning('Withdrawal rejection blocked: user lacks privileges.', [
                'UserID' => Auth::id(),
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to reject withdrawals.',
            ]);

            return;
        }

        $withdrawal = Withdrawal::find($this->withdrawalId);

        if (! $withdrawal || $withdrawal->status !== 'Pending') {
            $this->dispatch('swal:error', [
                'title' => 'Already Processed',
                'text' => 'This withdrawal request has already been processed.',
            ]);

            return;
        }

        $this->dispatch('txn-reject-withdrawal-prompt', withdrawalId: $this->withdrawalId);
    }

    /*
    | The Withdrawals tab component (which owns the DB writes) broadcasts this
    | after approve/reject so the review modal closes itself and stays in sync.
    */
    #[On('withdrawal-processed')]
    public function closeOnProcessed(): void
    {
        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->open = false;
        $this->isPrivileged = false;
        $this->withdrawalId = null;
        $this->withdrawal = null;
    }

    public function render()
    {
        return view('livewire.admin-pages.transaction-list.withdrawal-review-modal');
    }
}
