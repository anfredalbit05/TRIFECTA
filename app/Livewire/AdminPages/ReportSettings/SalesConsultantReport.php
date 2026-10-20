<?php

namespace App\Livewire\AdminPages\ReportSettings;

use App\Livewire\Concerns\AuthorizesModules;
use App\Models\Ledger;
use App\Models\User;
use App\Models\Wallet;
use App\Support\Modules;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class SalesConsultantReport extends Component
{
    use AuthorizesModules;
    use WithPagination;

    protected function module(): int
    {
        return Modules::RS_USERS_REPORTS;
    }

    protected $paginationTheme = 'tailwind';

    public string $search = '';

    public string $roleFilter = '';

    public string $statusFilter = '';

    public string $dateRange = ''; // e.g. "2026-08-01 to 2026-08-16"

    public ?int $selectedUserLedgerID = null;

    public bool $isLedgerModalOpen = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'dateRange' => ['except' => ''],
    ];

    public function mount()
    {
        $this->dateRange = '';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    protected function parsedDateRange(): array
    {
        if (blank($this->dateRange) || ! str_contains($this->dateRange, ' to ')) {
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
        $this->roleFilter = '';
        $this->statusFilter = '';
        $this->dateRange = '';
        $this->resetPage();
        $this->dispatch('ranges-reset');
    }

    public function viewLedger(int $userId)
    {
        $this->selectedUserLedgerID = $userId;
        $this->isLedgerModalOpen = true;
    }

    public function closeLedgerModal()
    {
        $this->isLedgerModalOpen = false;
        $this->selectedUserLedgerID = null;
    }

    /**
     * Shared filtered query so the paginated table and the print export
     * always reflect identical search/role/status/date filters.
     */
 protected function baseQuery()
{
    [$dateFrom, $dateTo] = $this->parsedDateRange();

    return User::with(['role', 'wallets' => fn ($q) => $q->where('status', 'Active')])
     ->whereHas('wallets', fn ($q) => $q->where('status', 'Active'))
        ->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('firstname', 'like', '%'.$this->search.'%')
                    ->orWhere('lastname', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('referralCode', 'like', '%'.$this->search.'%');
            });
        })



        ->when($this->roleFilter, fn ($query) => $query->where('RoleID', $this->roleFilter))
        ->when($this->statusFilter, fn ($query) => $query->where('status', $this->statusFilter))
        // date range now applies to the withdrawal date
        ->when($dateFrom && $dateTo, function ($query) use ($dateFrom, $dateTo) {
            $query->whereHas('wallets', function ($w) use ($dateFrom, $dateTo) {
                $w->where('status', 'Active')
                    ->where('source', 'WD')
                    ->whereBetween('created_at', [
                        Carbon::parse($dateFrom)->startOfDay(),
                        Carbon::parse($dateTo)->endOfDay(),
                    ]);
            });
        });
}


/**
 * Build report rows per ActivationID.
 *
 * DR, PF, and OV are calculated separately for each activation.
 * Withdrawals remain individual entries.
 */
protected function buildRows($users)
{
    [$dateFrom, $dateTo] = $this->parsedDateRange();

    $from = $dateFrom
        ? Carbon::parse($dateFrom)->startOfDay()
        : null;

    $to = $dateTo
        ? Carbon::parse($dateTo)->endOfDay()
        : null;

    return $users->flatMap(function (User $user) use ($from, $to) {
        $wallets = $user->wallets->where('status', 'Active');

        // Commission wallets grouped by ActivationID.
        $commissionGroups = $wallets
            ->whereIn('source', ['DR', 'PF', 'OV'])
            ->groupBy(function ($wallet) {
                // Avoid combining unrelated wallets whose ActivationID is null.
                return $wallet->ActivationID !== null
                    ? 'activation_'.$wallet->ActivationID
                    : 'wallet_'.$wallet->WalletID;
            });

        // Keep withdrawals as individual records.
        $withdrawals = $wallets
            ->where('source', 'WD')
            ->filter(function ($wallet) use ($from, $to) {
                if (! $from || ! $to) {
                    return true;
                }

                return $wallet->created_at
                    && $wallet->created_at->between($from, $to);
            })
            ->sortByDesc('created_at')
            ->values();

        $rows = collect();

        // One commission row per activation.
        foreach ($commissionGroups as $group) {
            $firstWallet = $group->first();

            $dr = (float) $group
                ->where('source', 'DR')
                ->sum('amount');

            $pf = (float) $group
                ->where('source', 'PF')
                ->sum('amount');

            $ov = (float) $group
                ->where('source', 'OV')
                ->sum('amount');
                $latestDate = $group
                    ->pluck('created_at')
                    ->filter()
                    ->sortDesc()
                    ->first();

                $rows->push([
                    'user' => $user,
                    'activation_id' => $firstWallet->ActivationID,
                    'first' => true,
                    'dr' => $dr,
                    'pf' => $pf,
                    'ov' => $ov,
                    'tc' => $dr + $pf + $ov,
                    'tax' => 0,
                    'wd' => 0,
                    'date' => $latestDate,
                ]);
        }

        // One separate row per withdrawal.
        foreach ($withdrawals as $withdrawal) {
            $wd = abs((float) $withdrawal->amount);

            $rows->push([
                'user' => $user,
                'activation_id' => $withdrawal->ActivationID,
                'first' => false,
                'dr' => 0,
                'pf' => 0,
                'ov' => 0,
                'tc' => 0,
                'tax' => round($wd * 0.10, 2),
                'wd' => $wd,
                'date' => $withdrawal->created_at,
            ]);
        }

        // Preserve users that have neither commission nor withdrawal records.
        if ($rows->isEmpty()) {
            $rows->push([
                'user' => $user,
                'activation_id' => null,
                'first' => true,
                'dr' => 0,
                'pf' => 0,
                'ov' => 0,
                'tc' => 0,
                'tax' => 0,
                'wd' => 0,
                'date' => null,
            ]);
        }

        return $rows;
    })->values();
}


public function render()
{
    [$dateFrom, $dateTo] = $this->parsedDateRange();

    $allUsers = (clone $this->baseQuery())
        ->latest('UserID')
        ->get();

    $allRows = $this->buildRows($allUsers)
    ->sortByDesc(function ($row) {
        return $row['date'] ?? null;
    })
    ->values();

    // Paginate the generated report rows, not the users.
    $perPage = 10;
    $currentPage = $this->getPage();

    $rows = new \Illuminate\Pagination\LengthAwarePaginator(
        $allRows->forPage($currentPage, $perPage)->values(),
        $allRows->count(),
        $perPage,
        $currentPage,
        [
            'path' => request()->url(),
            'query' => request()->query(),
            'pageName' => 'page',
        ]
    );

    $printRows = $allRows;

    $selectedUser = $this->selectedUserLedgerID
        ? User::with(['wallets', 'ledgers'])->find($this->selectedUserLedgerID)
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
            ->sortBy(fn (Ledger $ledger) => $ledger->TransactionID)
            ->values()
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
