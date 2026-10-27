<?php

namespace App\Livewire\AdminPages\ReportSettings;

use App\Livewire\Concerns\AuthorizesModules;
use App\Models\Ledger;
use App\Models\User;
use App\Support\Modules;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class SalesConsultantReport extends Component
{
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

protected function dailyLedgerQuery(): Builder
{
    [$dateFrom, $dateTo] = $this->parsedDateRange();

    // Group TX by consultant and exact transaction timestamp.
    $taxLedger = DB::table('ledgers as l')
        ->where('l.Source', 'TX')
        ->selectRaw('l.RefID as consultant_id')
        ->addSelect('l.created_at as tax_created_at')
        ->selectRaw('SUM(ABS(l.amount)) as TX')
        ->groupBy('l.RefID', 'l.created_at');

    $baseQuery = DB::table('wallets as w')
        ->join('users as u', 'u.UserID', '=', 'w.UserID')
        ->join('roles as r', 'r.RoleID', '=', 'u.RoleID')
        ->leftJoinSub($taxLedger, 'tx_ledger', function ($join) {
            $join->on('tx_ledger.consultant_id', '=', 'u.UserID')
                ->on('tx_ledger.tax_created_at', '=', 'w.created_at');
        })
        ->where('u.RoleID', 4)
        ->whereIn('w.source', [
            'DR', 'PF', 'CS', 'SDR', 'WD', 'OV',
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
            fn (Builder $q) => $q->where('u.status', $this->statusFilter)
        )
        ->when(
            $this->roleFilter !== '',
            fn (Builder $q) => $q->where('u.RoleID', $this->roleFilter)
        )
        ->when(
            $dateFrom && $dateTo,
            fn (Builder $q) => $q->whereBetween('w.created_at', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay(),
            ])
        );

    /*
     * OV rows: one row per wallet record.
     * Match TX using consultant ID + exact created_at.
     */
    $ovRows = (clone $baseQuery)
        ->where('w.source', 'OV')
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
        ->selectRaw('0 as DR')
        ->selectRaw('0 as PF')
        ->selectRaw('0 as CS')
        ->selectRaw('0 as SDR')
        ->selectRaw('w.amount as OV')
        ->selectRaw('-COALESCE(tx_ledger.TX, 0) as TX')
        ->selectRaw('0 as WD')
        ->selectRaw('w.amount - COALESCE(tx_ledger.TX, 0) as TOTAL')
        // Unique key for each OV wallet transaction.
        ->addSelect('w.WalletID as report_row_id');

    /*
     * Non-OV wallet sources: retain daily grouping.
     * TX is not deducted from these rows.
     */
    $dailyRows = (clone $baseQuery)
        ->whereIn('w.source', ['DR', 'PF', 'CS', 'SDR', 'WD'])
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
        ->selectRaw("SUM(CASE WHEN w.source = 'DR' THEN w.amount ELSE 0 END) as DR")
        ->selectRaw("SUM(CASE WHEN w.source = 'PF' THEN w.amount ELSE 0 END) as PF")
        ->selectRaw("SUM(CASE WHEN w.source = 'CS' THEN w.amount ELSE 0 END) as CS")
        ->selectRaw("SUM(CASE WHEN w.source = 'SDR' THEN w.amount ELSE 0 END) as SDR")
        ->selectRaw('0 as OV')
        ->selectRaw('0 as TX')
        ->selectRaw("SUM(CASE WHEN w.source = 'WD' THEN w.amount ELSE 0 END) as WD")
        ->selectRaw('SUM(w.amount) as TOTAL')
        ->selectRaw('NULL as report_row_id')
        ->groupBy(
            DB::raw('DATE(w.created_at)'),
            'u.UserID',
            'u.referralCode',
            'u.lastname',
            'u.firstname',
            'u.middlename',
            'r.rolename'
        );

    // Combine daily rows and individual OV rows.
    return DB::query()
        ->fromSub(
            $dailyRows->unionAll($ovRows),
            'report_rows'
        )
        ->select('report_rows.*');
}


protected function reportQuery(): Builder
{
    $daily = $this->dailyLedgerQuery();

    return DB::query()
        ->fromSub($daily, 'daily_ledger')
        ->select('daily_ledger.*')
        ->selectRaw(
            'SUM(TOTAL) OVER (
                PARTITION BY UserID
                ORDER BY
                    created_date DESC,
                    report_row_id DESC
                ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
            ) as RUNNING_BALANCE'
        );
}


    public function render()
    {
        [$dateFrom, $dateTo] = $this->parsedDateRange();

        $query = $this->reportQuery();

        $rows = (clone $query)
            ->orderBy('name')
            ->orderByDesc('created_date')
            ->paginate(10);

        $printRows = (clone $query)
            ->orderBy('name')
            ->orderByDesc('created_date')
            ->get();

        //Update lang sa live

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
            ? $selectedUser->ledgers->concat($prizeLedgers)
                ->sortBy('TransactionID')->values()
            : collect();

        return view('livewire.admin-pages.report-settings.sales-consultant-report', [
            'rows' => $rows,
            'printRows' => $printRows,
            'selectedUser' => $selectedUser,
            'prizeLedgers' => $prizeLedgers,
            'statementLedgers' => $statementLedgers,
            'dateRangeLabel' => $dateFrom && $dateTo
                ? Carbon::parse($dateFrom)->format('M d, Y').' — '.Carbon::parse($dateTo)->format('M d, Y')
                : 'All Time',
        ]);
    }
}
