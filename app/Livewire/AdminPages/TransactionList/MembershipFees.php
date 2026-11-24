<?php

namespace App\Livewire\AdminPages\TransactionList;

use App\Livewire\Concerns\AuthorizesModules;
use App\Livewire\Concerns\HasTabSkeleton;
use App\Models\MembershipFee;
use App\Support\Modules;
use Livewire\Component;
use Livewire\WithPagination;

class MembershipFees extends Component
{
    use HasTabSkeleton;
    use AuthorizesModules;
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';

    /** '' = all statuses, else 'Active' | 'Inactive'. */
    public string $statusFilter = '';

    /**
     * Permissions come from this module. There is no separate Membership Fees
     * module yet, so it reuses the Disbursement one (change_status = Post/Void).
     * Swap in your own constant when you add one.
     */
    protected function module(): int
    {
        return Modules::TXN_DISBURSEMENTS;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function resetTable(): void
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    /**
     * The ONLY write action: flip a fee between Active and Inactive.
     * The admin must type DEACTIVATE (fee is Active) or ACTIVATE (fee is
     * Inactive) in the SweetAlert prompt; the word is re-checked here.
     */
    public function toggleStatus(string $feeId, string $confirmation = ''): void
    {
        if (! $this->can('change_status')) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'message' => "You don't have permission to change the status of membership fees.",
            ]);
            return;
        }

        $fee = MembershipFee::query()->where('FeeID', $feeId)->firstOrFail();

        $wasActive = strtolower(trim((string) $fee->status)) === 'active';
        $expected = $wasActive ? 'DEACTIVATE' : 'ACTIVATE';

        if (strcasecmp(trim($confirmation), $expected) !== 0) {
            $this->dispatch('swal:error', [
                'title' => 'Confirmation Does Not Match',
                'message' => 'Type '.$expected.' to confirm. Nothing was changed.',
            ]);
            return;
        }

        $fee->status = $wasActive ? 'Inactive' : 'Active';
        $fee->save();

        $this->dispatch('swal:success', [
            'title' => 'Status Updated',
            'message' => $fee->FeeName.' is now '.$fee->status.'.',
        ]);
    }

    public function render()
    {
        $fees = MembershipFee::query()
            ->when(filled($this->search), function ($query) {
                $term = '%'.str_replace(['%', '_'], ['\\%', '\\_'], trim($this->search)).'%';

                $query->where(function ($q) use ($term) {
                    $q->where('FeeID', 'like', $term)
                        ->orWhere('FeeName', 'like', $term);
                });
            })
            ->when($this->statusFilter !== '', fn ($query) => $query
                ->whereRaw("LOWER(TRIM(COALESCE(status, ''))) = ?", [strtolower($this->statusFilter)]))
            ->orderBy('FeeID')
            ->paginate(15);

        return view('livewire.admin-pages.transaction-list.membership-fees', [
            'fees' => $fees,
        ]);
    }
}
