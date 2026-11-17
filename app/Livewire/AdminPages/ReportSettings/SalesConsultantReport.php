<?php

namespace App\Livewire\AdminPages\ReportSettings;

use App\Livewire\Concerns\AuthorizesModules;
use App\Models\Ledger;
use App\Models\User;
use App\Support\Modules;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;
use Livewire\WithPagination;

class SalesConsultantReport extends Component
{
    use HasTabSkeleton;
    use AuthorizesModules, WithPagination;

    protected function module(): int
    {
        return Modules::RS_USERS_REPORTS;
    }

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public string $roleFilter = '';
    public string $statusFilter = '';
    public string $dateRange = '';

    public ?int $selectedUserLedgerID = null;
    public bool $isLedgerModalOpen = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'dateRange' => ['except' => ''],
    ];

    public bool $printing = false;

    public function mount(): void
    {
        // Keep a date range supplied through the URL, if any.
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingRoleFilter(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }
    public function updatedDateRange(): void { $this->resetPage(); }

    protected function parsedDateRange(): array
    {
        if (blank($this->dateRange) || ! str_contains($this->dateRange, ' to ')) {
            return [null, null];
        }

        [$from, $to] = explode(' to ', $this->dateRange, 2);

        return [trim($from) ?: null, trim($to) ?: null];
    }

    public function clearDateFilter(): void
    {
        $this->dateRange = '';
        $this->resetPage();
        $this->dispatch('ranges-reset');
    }

    public function resetTable(): void
    {
        $this->reset(['search', 'roleFilter', 'statusFilter', 'dateRange']);
        $this->resetPage();
        $this->dispatch('ranges-reset');
    }

    public function preparePrint(): void
    {

        $this->printing = true;

        $this->dispatch('print-sales-consultant-report');
    }

    public function viewLedger(int $userId): void
    {
        $this->selectedUserLedgerID = $userId;
        $this->isLedgerModalOpen = true;
    }

    public function closeLedgerModal(): void
    {
        $this->reset(['isLedgerModalOpen', 'selectedUserLedgerID']);
    }

    /**
     * Daily totals for RoleID 4. The date filter is applied before the
     * running total, so a selected range starts its balance at that range.
     */


/**
 * One row per consultant per date.
 *
 * All wallet sources are summed for the day.
 * TX is grouped by consultant and date, then deducted from the daily total.
 * No date-range filter is applied here because running balances need
 * the consultant's complete history.
 */
protected function dailyLedgerQueryWithoutDateFilter(): Builder
{
    // Sum TX ledger entries by consultant and calendar date.
    $taxLedger = DB::table('ledgers as l')
        ->where('l.Source', 'TX')
        ->selectRaw('l.RefID as consultant_id')
        ->selectRaw('DATE(l.created_at) as tax_date')
        ->selectRaw('SUM(ABS(COALESCE(l.amount, 0))) as tax_amount')
        ->groupBy(
            'l.RefID',
            DB::raw('DATE(l.created_at)')
        );

    // Sum every wallet source into one row per consultant per date.
    $walletDaily = DB::table('wallets as w')
        ->join('users as u', 'u.UserID', '=', 'w.UserID')
        ->join('roles as r', 'r.RoleID', '=', 'u.RoleID')
        ->where('u.RoleID', 4)
        ->whereIn('w.source', [
            'DR', 'PF', 'CS', 'SDR', 'OV', 'WD',
        ])
        ->when($this->search !== '', function (Builder $q) {
            $term = '%' . $this->search . '%';

            $q->where(function (Builder $sub) use ($term) {
                $sub->where('u.firstname', 'like', $term)
                    ->orWhere('u.lastname', 'like', $term)
                    ->orWhere('u.middlename', 'like', $term)
                    ->orWhere('u.email', 'like', $term)
                    ->orWhere('u.referralCode', 'like', $term);
            });
        })
        ->when(
            $this->statusFilter !== '',
            fn (Builder $q) => $q->where(
                'u.status',
                $this->statusFilter
            )
        )
        ->when(
            $this->roleFilter !== '',
            fn (Builder $q) => $q->where(
                'u.RoleID',
                $this->roleFilter
            )
        )
        ->selectRaw('DATE(w.created_at) as created_date')
        ->addSelect([
            'u.UserID',
            'u.referralCode',
            'u.lastname',
            'u.firstname',
            'u.middlename',
            'r.rolename',
        ])
        ->selectRaw("
            CONCAT(
                IFNULL(u.lastname, ''),
                ', ',
                IFNULL(u.firstname, ''),
                ' ',
                IFNULL(u.middlename, '')
            ) as name
        ")
        ->selectRaw("
            SUM(CASE WHEN w.source = 'DR'
                THEN COALESCE(w.amount, 0) ELSE 0 END) as DR
        ")
        ->selectRaw("
            SUM(CASE WHEN w.source = 'PF'
                THEN COALESCE(w.amount, 0) ELSE 0 END) as PF
        ")
        ->selectRaw("
            SUM(CASE WHEN w.source = 'CS'
                THEN COALESCE(w.amount, 0) ELSE 0 END) as CS
        ")
        ->selectRaw("
            SUM(CASE WHEN w.source = 'SDR'
                THEN COALESCE(w.amount, 0) ELSE 0 END) as SDR
        ")
        ->selectRaw("
            SUM(CASE WHEN w.source = 'OV'
                THEN COALESCE(w.amount, 0) ELSE 0 END) as OV
        ")
        ->selectRaw("
            SUM(CASE WHEN w.source = 'WD'
                THEN COALESCE(w.amount, 0) ELSE 0 END) as WD
        ")
        ->groupBy(
            DB::raw('DATE(w.created_at)'),
            'u.UserID',
            'u.referralCode',
            'u.lastname',
            'u.firstname',
            'u.middlename',
            'r.rolename'
        );

    // Join the daily TX totals AFTER wallet entries have been aggregated.
    // This prevents TX from multiplying wallet amounts.
    return DB::query()
        ->fromSub($walletDaily, 'wallet_daily')
        ->leftJoinSub($taxLedger, 'tx_ledger', function ($join) {
            $join->on(
                'tx_ledger.consultant_id',
                '=',
                'wallet_daily.UserID'
            )->on(
                'tx_ledger.tax_date',
                '=',
                'wallet_daily.created_date'
            );
        })
        ->select('wallet_daily.*')
        ->selectRaw(
            '-COALESCE(tx_ledger.tax_amount, 0) as TX'
        )
        ->selectRaw("
            COALESCE(wallet_daily.DR, 0)
            + COALESCE(wallet_daily.PF, 0)
            + COALESCE(wallet_daily.CS, 0)
            + COALESCE(wallet_daily.SDR, 0)
            + COALESCE(wallet_daily.OV, 0)
            + COALESCE(wallet_daily.WD, 0)
            as TOTAL
        ")
        ->selectRaw(
            "CONCAT(wallet_daily.UserID, '-', wallet_daily.created_date)
             as report_row_id"
        );
}

/**
 * Calculate running balances from all historical daily totals first.
 * Apply the selected date range afterward.
 */
protected function reportQuery(): Builder
{
    $transactions = $this->dailyLedgerQueryWithoutDateFilter();

    $running = DB::query()
        ->fromSub($transactions, 'transactions')
        ->select('transactions.*')
        ->selectRaw("
            SUM(TOTAL) OVER (
                PARTITION BY UserID
                ORDER BY created_date ASC
                ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
            ) as RUNNING_BALANCE
        ");

    [$dateFrom, $dateTo] = $this->parsedDateRange();

    return DB::query()
        ->fromSub($running, 'report')
        ->select('report.*')
        ->when(
            $dateFrom && $dateTo,
            fn (Builder $q) => $q->whereBetween(
                'report.created_date',
                [
                    Carbon::parse($dateFrom)->toDateString(),
                    Carbon::parse($dateTo)->toDateString(),
                ]
            )
        );
}


    public function render()
{
    [$dateFrom, $dateTo] = $this->parsedDateRange();

    $query = $this->reportQuery();

    // Screen table: one row per consultant per date.
    $rows = (clone $query)
        ->orderBy('name')
        ->orderByDesc('created_date')
        ->orderByDesc('report_row_id')
        ->paginate(10);

    // Print table: all matching records, without pagination.
    $printRows = $this->printing
        ? (clone $query)
            ->orderBy('name')
            ->orderByDesc('created_date')
            ->orderByDesc('report_row_id')
            ->get()
        : collect();

    $selectedUser = $this->selectedUserLedgerID
        ? User::with(['role', 'wallets', 'ledgers'])
            ->where('RoleID', 4)
            ->find($this->selectedUserLedgerID)
        : null;

    $prizeLedgers = $selectedUser
        ? Ledger::with('draw')
            ->where('Source', 'PK')
            ->where('RefID', (string) $selectedUser->UserID)
            ->orderBy('TransactionID')
            ->get()
        : collect();

    $statementLedgers = $selectedUser
        ? $selectedUser->ledgers
            ->concat($prizeLedgers)
            ->sortBy('TransactionID')
            ->values()
        : collect();

    return view(
        'livewire.admin-pages.report-settings.sales-consultant-report',
        [
            'rows' => $rows,
            'printRows' => $printRows,
            'selectedUser' => $selectedUser,
            'prizeLedgers' => $prizeLedgers,
            'statementLedgers' => $statementLedgers,
            'dateRangeLabel' => $dateFrom && $dateTo
                ? Carbon::parse($dateFrom)->format('M d, Y')
                    . ' — '
                    . Carbon::parse($dateTo)->format('M d, Y')
                : 'All Time',
        ]
    );
}
}
