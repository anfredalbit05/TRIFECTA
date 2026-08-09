<?php

namespace App\Livewire\AdminPages\ReportSettings;

use App\Models\Ledger;
use Livewire\Component;
use Livewire\WithPagination;

class Reports extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetTable(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $ledgers = Ledger::query()
            ->when(filled($this->search), function ($query) {
                $search = trim($this->search);
                $query->where(function ($q) use ($search) {
                    $q->where('Source', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('ActivationID', 'like', "%{$search}%")
                        ->orWhere('RefID', 'like', "%{$search}%");
                });
            })
            ->latest('TransactionID')
            ->paginate(10);

        return view('livewire.admin-pages.report-settings.reports', [
            'ledgers' => $ledgers
        ]);
    }
}
