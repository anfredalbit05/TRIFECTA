<?php

namespace App\Livewire\AdminPages\ReportSettings;

use App\Models\Ledger;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
        $activeAccounts = User::where('status', 1)->count();

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

        // Membership Fee Collection Breakdown
        $membershipFeeCollections = DB::table('membership_fees as m')
            ->leftJoin('ledgers as l', function ($join) use ($year) {
                $join->on('l.RefID', '=', 'm.FeeID')
                     ->whereYear('l.created_at', '=', $year)
                     ->where('l.Source', '=', 'MF')
                     ->where('l.status', '=', 'Completed');
            })
            ->select(
                'm.FeeID',
                'm.FeeName',
                DB::raw('COALESCE(SUM(l.amount), 0) AS Total_Collection')
            )
            ->groupBy('m.FeeID', 'm.FeeName')
            ->orderBy('m.FeeName')
            ->get();

       // Collection Summary by Source (Predefined categories with Left Join and correct sequence)
        $incomeSources = DB::table(DB::raw("(
            SELECT 1 AS SEQUENCE, 'MF' AS Source, 'Membership Fee' AS Collection_Type
            UNION ALL SELECT 2, 'DR', 'Direct Referral'
            UNION ALL SELECT 3, 'OV', 'Overrides'
            UNION ALL SELECT 4, 'PF', 'Provident Fund'
            UNION ALL SELECT 5, 'CL', 'Claim'
            UNION ALL SELECT 6, 'RF', 'Refund'
            UNION ALL SELECT 7, 'OT', 'Others'
        ) as s"))
        ->leftJoin('ledgers as l', function ($join) use ($year) {
            $join->on('l.Source', '=', 's.Source')
                 ->where('l.status', '=', 'Completed')
                 ->whereYear('l.created_at', '=', $year);
        })
        ->select(
            's.Source',
            's.Collection_Type',
            DB::raw('COALESCE(SUM(l.amount), 0) AS Total_Collection'),
            DB::raw('COUNT(l.amount) AS count')
        )
        ->groupBy('s.SEQUENCE', 's.Source', 's.Collection_Type')
        ->orderBy('s.SEQUENCE')
        ->get();

        return view('components.admin-pages.report-settings.overview', [
            'totalIncome'              => $totalIncome,
            'totalReportsGenerated'    => $totalReportsGenerated,
            'totalSettingsCount'       => $totalSettingsCount,
            'totalUsers'               => $totalUsers,
            'activeAccounts'           => $activeAccounts,
            'monthlyIncome'            => $monthlyIncome,
            'membershipFeeCollections' => $membershipFeeCollections,
            'incomeSources'            => $incomeSources,
        ]);
    }
}
