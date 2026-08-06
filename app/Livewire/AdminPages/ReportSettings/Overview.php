<?php

namespace App\Livewire\AdminPages\ReportSettings;

use App\Models\Ledger;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class Overview extends Component
{
    public $selectedYear;
    public array $availableYears = [];

    public function mount()
    {
        $this->selectedYear = date('Y');

        // Dynamically get years from Ledger entries or default to current/previous years
        $ledgerYears = Ledger::selectRaw('YEAR(created_at) as year')
            ->union(User::selectRaw('YEAR(created_at) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $this->availableYears = !empty($ledgerYears) ? $ledgerYears : [date('Y'), date('Y') - 1];

        if (!in_array($this->selectedYear, $this->availableYears)) {
            $this->selectedYear = $this->availableYears[0] ?? date('Y');
        }
    }

    public function refreshAnalytics()
    {
        // Simply triggers a re-render to fetch fresh data
        $this->dispatch('swal:success', [
            'title' => 'Refreshed!',
            'text'  => 'Analytics data has been updated.'
        ]);
    }

    public function render()
    {
        $year = $this->selectedYear;

        // Financial & Report Metrics based on Ledger & Settings
        $totalIncome = Ledger::whereYear('created_at', $year)
            ->where('status', 'Completed')
            ->sum('amount');

        $totalReportsGenerated = Ledger::whereYear('created_at', $year)->count();

        $totalSettingsCount = Setting::count();

        // General System Stats
        $totalUsers = User::count();
        $activeAccounts = User::where('status', 1)->count(); // Adjust column name if active flag differs

        // Monthly Income Chart Data (Ledger breakdown by month)
        $monthlyIncome = Ledger::selectRaw('MONTH(created_at) as month_num, SUM(amount) as total')
            ->whereYear('created_at', $year)
            ->where('status', 'Completed')
            ->groupBy('month_num')
            ->orderBy('month_num')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::create()->month($item->month_num)->format('M'),
                    'total' => (float) $item->total,
                ];
            });

        // Recent Ledger Transactions / Reports
        $recentTransactions = Ledger::whereYear('created_at', $year)
            ->latest('TransactionID')
            ->take(5)
            ->get();

        // Top Income Sources Breakdown
        $incomeSources = Ledger::selectRaw('Source, SUM(amount) as total_amount, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->where('status', 'Completed')
            ->groupBy('Source')
            ->get();

        return view('components.admin-pages.report-settings.overview', [
            'totalIncome'          => $totalIncome,
            'totalReportsGenerated'=> $totalReportsGenerated,
            'totalSettingsCount'   => $totalSettingsCount,
            'totalUsers'           => $totalUsers,
            'activeAccounts'       => $activeAccounts,
            'monthlyIncome'        => $monthlyIncome,
            'recentTransactions'   => $recentTransactions,
            'incomeSources'        => $incomeSources,
        ]);
    }
}
