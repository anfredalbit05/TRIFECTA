<?php

namespace App\Livewire\AdminPages\TransactionList;

use App\Livewire\Concerns\AuthorizesModules;
use App\Livewire\Concerns\HasTabSkeleton;
use App\Support\Modules;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CocogenMembersInsuranceExport;

class CocogeMembersInsurance extends Component
{
    use HasTabSkeleton;
    use AuthorizesModules;
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';

    /**
     * Empty = all civil statuses.
     */
    public string $statusFilter = '';

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
     * Download the currently filtered records as an Excel file.
     */
    public function exportExcel()
    {
        if (! $this->canRead) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'message' => "You don't have permission to export membership insurance records.",
            ]);

            return;
        }

        return Excel::download(
            new CocogenMembersInsuranceExport(
                $this->search,
                $this->statusFilter
            ),
            'cocogen-members-insurance-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function render()
    {
        $query = DB::table('cocogen_members_insurance');

        $query->when(filled($this->search), function ($query) {
            $term = '%' . trim($this->search) . '%';

            $query->where(function ($q) use ($term) {
                $q->where('member_name', 'like', $term)
                    ->orWhere('beneficiary_name', 'like', $term)
                    ->orWhere('address', 'like', $term)
                    ->orWhere('contact_number', 'like', $term)
                    ->orWhere('plan', 'like', $term);
            });
        });

        $query->when(
            $this->statusFilter !== '',
            fn ($query) => $query->where(
                'civil_status',
                $this->statusFilter
            )
        );

        $fees = $query
            ->orderBy('CocogenID', 'desc')
            ->paginate(15);

        $civilStatuses = DB::table('cocogen_members_insurance')
            ->whereNotNull('civil_status')
            ->where('civil_status', '!=', '')
            ->distinct()
            ->orderBy('civil_status')
            ->pluck('civil_status');

        return view(
            'livewire.admin-pages.transaction-list.cocogen-members-insurance',
            [
                'fees' => $fees,
                'civilStatuses' => $civilStatuses,
            ]
        );
    }
}
