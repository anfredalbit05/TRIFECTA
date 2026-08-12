<?php

namespace App\Livewire\AdminPages\ReportSettings;

use App\Models\Ledger;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Reports extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public string $dateRange = ''; // e.g. "2026-08-01 to 2026-08-16"

    public function mount()
    {
        $this->dateRange = '';
    }

    public function updatingSearch()
    {
        $this->resetPage();
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

    public function resetTable(): void
    {
        $this->search = '';
        $this->dateRange = '';
        $this->resetPage();
        $this->dispatch('ranges-reset');
    }

    public function render()
    {
        [$dateFrom, $dateTo] = $this->parsedDateRange();

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
            ->when($dateFrom && $dateTo, function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [
                    Carbon::parse($dateFrom)->startOfDay(),
                    Carbon::parse($dateTo)->endOfDay(),
                ]);
            })
            ->latest('TransactionID')
            ->paginate(10);

        return view('livewire.admin-pages.report-settings.reports', [
            'ledgers' => $ledgers,
        ]);
    }
}
