<?php

namespace App\Livewire\AdminPages\ReportSettings;

use App\Models\Ledger;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class Overview extends Component
{
    use HasTabSkeleton;
    public $selectedYear;

    public array $availableYears = [];

    // Date range filters for specific sections
    public $membershipFeeStart;

    public $membershipFeeEnd;

    public $sourceStart;

    public $sourceEnd;

    public $walletStart;

    public $walletEnd;

    public $monthlyStart;

    public $monthlyEnd;

    public $selectedVariant = 1;

    public function mount()
    {
        $this->selectedYear = date('Y');

        // Dynamically set years from current year down to 2020
        $this->availableYears = range(date('Y'), 2020);

        if (! in_array($this->selectedYear, $this->availableYears)) {
            $this->selectedYear = $this->availableYears[0] ?? date('Y');
        }

        $this->resetDatesToYear($this->selectedYear);
    }

    public function updatedSelectedYear($value)
    {
        $this->resetDatesToYear($value);
        $this->dispatchChartData();
    }

    private function resetDatesToYear($year)
    {
        $startOfYear = Carbon::create($year, 1, 1)->format('Y-m-d');
        $endOfYear = Carbon::create($year, 12, 31)->format('Y-m-d');

        $this->membershipFeeStart = $startOfYear;
        $this->membershipFeeEnd = $endOfYear;

        $this->sourceStart = $startOfYear;
        $this->sourceEnd = $endOfYear;

        $this->walletStart = $startOfYear;
        $this->walletEnd = $endOfYear;

        $this->monthlyStart = $startOfYear;
        $this->monthlyEnd = $endOfYear;
    }

    public function refreshAnalytics()
    {
        $this->dispatchChartData();
        // $this->dispatch('swal:success', [
        //     'title' => 'Refreshed!',
        //     'text'  => 'Analytics data has been updated.'
        // ]);
    }

    private function getMonthlyIncomeData($year)
    {
        $rawMonthlyIncome = Ledger::selectRaw('MONTH(created_at) as month_num, SUM(amount) as total')
            ->whereYear('created_at', $year)
            ->whereNot('source', 'DB')
            ->where('status', 'Completed')
            ->groupBy('month_num')
            ->pluck('total', 'month_num');

        // Ensure this explicitly returns a Laravel Collection instance
        return collect(range(1, 12))->map(function ($monthNum) use ($rawMonthlyIncome) {
            return [
                'month' => Carbon::create()->month($monthNum)->format('M'),
                'total' => (float) ($rawMonthlyIncome->get($monthNum, 0)),
            ];
        });
    }

    private function dispatchChartData()
    {
        $monthlyIncome = $this->getMonthlyIncomeData($this->selectedYear);

        $this->dispatch('chart-data-updated',
            labels: $monthlyIncome->pluck('month'),
            data: $monthlyIncome->pluck('total')
        );
    }

    public function updatedSelectedVariant($value): void
    {
        $this->selectedVariant = $value;

        Log::info('Selected variant updated', [
            'selected_variant' => $this->selectedVariant,
        ]);
    }


    public function render()
    {
        $year = $this->selectedYear;

        // Safety check: ensure date ranges match the selected year if stale state persists
        if (Carbon::parse($this->membershipFeeStart)->year != $year) {
            $this->resetDatesToYear($year);
        }

        // Global Financial & Report Metrics

        // 1. Gross Income (all completed ledger transactions)
        $grossIncome = Ledger::whereYear('created_at', $year)
            ->where('status', 'Completed')
            ->sum('amount');

        // 2. Approved Withdrawals
        $approvedWithdrawals = DB::table('withdrawals')
            ->whereYear('created_at', $year)
            ->where('status', 'Approved')
            ->sum('amount');

        // 3. Total Disbursements (all statuses)
        $totalDisbursements = DB::table('disburstments')
            ->whereYear('created_at', $year)
            ->sum('amount');

        // 4. Net Total Income
        $totalIncome = $grossIncome
            - $approvedWithdrawals
            - $totalDisbursements;

        // Other metrics
        $totalReportsGenerated = Ledger::whereYear('created_at', $year)->count();
        $totalSettingsCount = Setting::count();

        $totalUsers = User::count();
        $activeAccounts = User::where('status', 1)->count();

        // Monthly Income Chart Data (Guarantees all 12 months for the chosen year)
        $monthlyIncome = $this->getMonthlyIncomeData($year);


        // Membership Fee Breakdown
        $variants = DB::table('variants')
            ->orderBy('name')
            ->get(['VID', 'name']);

            // Membership Fee Collection Breakdown
            $membershipFeeCollections = DB::table('membership_fees as m')
                ->leftJoin('variants as v', 'v.VID', '=', 'm.VID')
                ->leftJoin('ledgers as l', function ($join) {
                    $join->on('l.RefID', '=', 'm.FeeID')
                        ->where('l.Source', '=', 'MF')
                        ->where('l.status', '=', 'Completed')
                        ->whereBetween('l.created_at', [
                            $this->membershipFeeStart . ' 00:00:00',
                            $this->membershipFeeEnd . ' 23:59:59',
                        ]);
                })
                ->where('m.VID', (int) $this->selectedVariant)
                ->select(
                    'm.FeeID',
                    'm.FeeName',
                    'm.VID',
                    'v.name as Variant_Name',
                    'm.Cost',
                    DB::raw('COALESCE(SUM(l.amount), 0) AS Total_Collection')
                )
                ->groupBy(
                    'm.FeeID',
                    'm.FeeName',
                    'm.VID',
                    'v.name',
                    'm.Cost'
                )
                ->orderBy('m.FeeName')
                ->get();

            $membershipFeeTotal = (float) $membershipFeeCollections->sum(
                'Total_Collection'
            );

            // Collection Summary by Source
            // 1. Membership Fee collections grouped by variant.
            $membershipFeeSourceRows = DB::table('membership_fees as m')
                ->leftJoin('variants as v', 'v.VID', '=', 'm.VID')
                ->leftJoin('ledgers as l', function ($join) {
                    $join->on('l.RefID', '=', 'm.FeeID')
                        ->where('l.Source', '=', 'MF')
                        ->where('l.status', '=', 'Completed')
                        ->whereBetween('l.created_at', [
                            $this->sourceStart . ' 00:00:00',
                            $this->sourceEnd . ' 23:59:59',
                        ]);
                })
                ->selectRaw("
                    1 AS SEQUENCE,
                    'MF' AS Source,
                    CONCAT(
                        'Membership Fee (',
                        COALESCE(v.name, 'No Variant'),
                        ')'
                    ) AS Collection_Type,
                    COALESCE(SUM(l.amount), 0) AS Total_Collection,
                    COUNT(l.amount) AS count
                ")
                ->groupBy('m.VID', 'v.name')
                ->get();

        Log::info('Membership fee variant filter changed', [
            'selected_variant' => $this->selectedVariant

        ]);

            // 2. Other collection sources remain unchanged.
            $otherIncomeSources = DB::table(DB::raw("(
                SELECT 2 AS SEQUENCE, 'DR' AS Source, 'Direct Referral' AS Collection_Type
                UNION ALL SELECT 3, 'OV', 'Overrides'
                UNION ALL SELECT 4, 'PF', 'Provident Fund'
                UNION ALL SELECT 5, 'CL', 'Claim'
                UNION ALL SELECT 6, 'RF', 'Refund'
                UNION ALL SELECT 7, 'CF', 'Convenience Fee'
                UNION ALL SELECT 8, 'TX', 'TAX'
                UNION ALL SELECT 9, 'WD', 'Withdrawals'
                UNION ALL SELECT 10, 'PK', 'Pangkabuhayan'
                UNION ALL SELECT 11, 'OT', 'Others'
                UNION ALL SELECT 12, 'DB', 'Disbursement'
            ) AS s"))
                ->leftJoin('ledgers as l', function ($join) {
                    $join->on('l.Source', '=', 's.Source')
                        ->where('l.status', '=', 'Completed')
                        ->whereBetween('l.created_at', [
                            $this->sourceStart . ' 00:00:00',
                            $this->sourceEnd . ' 23:59:59',
                        ]);
                })
                ->select(
                    's.SEQUENCE',
                    's.Source',
                    's.Collection_Type',
                    DB::raw('COALESCE(SUM(l.amount), 0) AS Total_Collection'),
                    DB::raw('COUNT(l.amount) AS count')
                )
                ->groupBy('s.SEQUENCE', 's.Source', 's.Collection_Type')
                ->get();

            // 3. Combine and sort all collection sources.
            $incomeSources = $membershipFeeSourceRows
                ->concat($otherIncomeSources)
                ->sortBy(function ($source) {
                    return sprintf(
                        '%02d-%s',
                        $source->SEQUENCE,
                        $source->Collection_Type
                    );
                })
                ->values();

            $incomeSourcesTotal = (float) $incomeSources->sum('Total_Collection');

        // Wallet Incentives Breakdown Query
        $walletCollections = DB::table(DB::raw("(
            SELECT
                u.UserID,
                CONCAT(u.FirstName, ' ', u.LastName) AS Member_Name,
                u.referralCode,
                s.Sequence,
                s.Source,
                s.Incentive_Type,
                COALESCE(SUM(w.Amount), 0) AS Total_Collection
            FROM users u
            CROSS JOIN (
                SELECT 1 AS Sequence, 'DR' AS Source, 'Direct Referral' AS Incentive_Type
                UNION ALL
                SELECT 2, 'PF', 'Provident Fund'
                UNION ALL
                SELECT 3, 'OV', 'Overrides'
                UNION ALL
                SELECT 4, 'WD', 'Withdrawals'
            ) s
            LEFT JOIN wallets w
                ON w.UserID = u.UserID
                AND w.Source = s.Source
                AND w.created_at BETWEEN '".$this->walletStart." 00:00:00' AND '".$this->walletEnd." 23:59:59'
            GROUP BY
                u.UserID,
                u.FirstName,
                u.LastName,
                u.referralCode,
                s.Sequence,
                s.Source,
                s.Incentive_Type
        ) x"))
            ->leftJoin(DB::raw("(
            SELECT
                UserID,
                SUM(Amount) AS User_Total
            FROM wallets
            WHERE Source IN ('DR', 'PF', 'OV')
              AND created_at BETWEEN '".$this->walletStart." 00:00:00' AND '".$this->walletEnd." 23:59:59'
            GROUP BY UserID
        ) t"), 'x.UserID', '=', 't.UserID')
            ->select('x.*', DB::raw('COALESCE(t.User_Total, 0) as User_Total'))
            ->where('x.Total_Collection', '>', 0)
            ->orderByDesc('User_Total')
            ->orderBy('x.Member_Name')
            ->orderBy('x.Sequence')
            ->get();

            $walletCollectionsTotal = (float) $walletCollections->sum('Total_Collection');

        return view('livewire.admin-pages.report-settings.overview', [
            'grossIncome' => $grossIncome,
            'approvedWithdrawals' => $approvedWithdrawals,
            'totalDisbursements' => $totalDisbursements,
            'totalIncome' => $totalIncome,
            'totalReportsGenerated' => $totalReportsGenerated,
            'totalSettingsCount' => $totalSettingsCount,
            'totalUsers' => $totalUsers,
            'activeAccounts' => $activeAccounts,
            'monthlyIncome' => $monthlyIncome,
            'membershipFeeCollections' => $membershipFeeCollections,
            'incomeSources' => $incomeSources,
            'walletCollections' => $walletCollections,

            'variants' => $variants,
            'membershipFeeTotal' => $membershipFeeTotal,

            'incomeSourcesTotal' => $incomeSourcesTotal,
            'walletCollectionsTotal' => $walletCollectionsTotal,

            // Formatted labels for the per-card print headers
            'membershipFeeStartLabel' => Carbon::parse($this->membershipFeeStart)->format('M d, Y'),
            'membershipFeeEndLabel' => Carbon::parse($this->membershipFeeEnd)->format('M d, Y'),
            'sourceStartLabel' => Carbon::parse($this->sourceStart)->format('M d, Y'),
            'sourceEndLabel' => Carbon::parse($this->sourceEnd)->format('M d, Y'),
            'walletStartLabel' => Carbon::parse($this->walletStart)->format('M d, Y'),
            'walletEndLabel' => Carbon::parse($this->walletEnd)->format('M d, Y'),
        ]);
    }
}
