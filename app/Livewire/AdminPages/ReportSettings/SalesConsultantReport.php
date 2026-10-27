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

    /*
     * Group TX only by consultant and exact transaction timestamp.
     * RefID references the consultant's UserID.
     */
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
        ->when(
            $dateFrom && $dateTo,
            fn (Builder $q) => $q->whereBetween('w.created_at', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay(),
            ])
        );

    /*
     * One report row per wallet transaction.
     * TX is displayed as negative and deducted from that row's total.
     */
    return (clone $baseQuery)
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
            CASE WHEN w.source = 'DR'
                THEN w.amount ELSE 0 END as DR
        ")
        ->selectRaw("
            CASE WHEN w.source = 'PF'
                THEN w.amount ELSE 0 END as PF
        ")
        ->selectRaw("
            CASE WHEN w.source = 'CS'
                THEN w.amount ELSE 0 END as CS
        ")
        ->selectRaw("
            CASE WHEN w.source = 'SDR'
                THEN w.amount ELSE 0 END as SDR
        ")
        ->selectRaw("
            CASE WHEN w.source = 'OV'
                THEN w.amount ELSE 0 END as OV
        ")
        ->selectRaw('-COALESCE(tx_ledger.TX, 0) as TX')
        ->selectRaw("
            CASE WHEN w.source = 'WD'
                THEN w.amount ELSE 0 END as WD
        ")
        ->selectRaw(
            'COALESCE(w.amount, 0) - COALESCE(tx_ledger.TX, 0) as TOTAL'
        )
        ->addSelect('w.created_at as report_created_at')
        ->addSelect('w.WalletID as report_row_id');
}

protected function reportQuery(): Builder
{
    $transactions = $this->dailyLedgerQuery();

    return DB::query()
        ->fromSub($transactions, 'daily_ledger')
        ->select('daily_ledger.*')
        ->selectRaw(
            'SUM(TOTAL) OVER (
                PARTITION BY UserID
                ORDER BY
                    report_created_at ASC,
                    report_row_id ASC
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
            ->orderByDesc('report_created_at')
            ->orderByDesc('report_row_id')
            ->paginate(10);

        $printRows = (clone $query)
            ->orderBy('name')
            ->orderByDesc('report_created_at')
            ->orderByDesc('report_row_id')
            ->get();

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
