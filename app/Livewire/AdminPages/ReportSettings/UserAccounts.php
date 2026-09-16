<?php

namespace App\Livewire\AdminPages\ReportSettings;

use App\Models\User;
use App\Models\Ledger;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class UserAccounts extends Component
{
    use WithPagination;

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

        return User::with(['role', 'wallets', 'ledgers'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('firstname', 'like', '%' . $this->search . '%')
                      ->orWhere('lastname', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('referralCode', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter, function ($query) {
                $query->where('RoleID', $this->roleFilter);
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($dateFrom && $dateTo, function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [
                    Carbon::parse($dateFrom)->startOfDay(),
                    Carbon::parse($dateTo)->endOfDay(),
                ]);
            });
    }

    public function render()
    {
        [$dateFrom, $dateTo] = $this->parsedDateRange();

        $users = (clone $this->baseQuery())
            ->latest('UserID')
            ->paginate(10);

        // Unpaginated set for the printable report, same filters as the table
        $printUsers = (clone $this->baseQuery())
            ->latest('UserID')
            ->get();

        $selectedUser = $this->selectedUserLedgerID
            ? User::with(['wallets', 'ledgers'])->find($this->selectedUserLedgerID)
            : null;

        // Prize rows from the Pangkabuhayan Spin the Wheel are booked with
        // Source 'PK' and RefID = the member's UserID (no ActivationID), so
        // the hasManyThrough ledgers() relation never sees them. Merge them
        // in directly so the member's own statement still shows their prizes.
        $prizeLedgers = $selectedUser
            ? Ledger::with('draw')
                ->where('Source', 'PK')
                ->where('RefID', (string) $selectedUser->UserID)
                ->orderBy('TransactionID')
                ->get()
            : collect();

        // One chronological statement = activation-linked rows + prize rows.
        $statementLedgers = $selectedUser
            ? $selectedUser->ledgers
                ->concat($prizeLedgers)
                ->sortBy(fn (Ledger $ledger) => $ledger->TransactionID)
                ->values()
            : collect();

        return view('livewire.admin-pages.report-settings.users-accounts', [
            'users' => $users,
            'printUsers' => $printUsers,
            'selectedUser' => $selectedUser,
            'prizeLedgers' => $prizeLedgers,
            'statementLedgers' => $statementLedgers,
            'dateRangeLabel' => $dateFrom && $dateTo
                ? Carbon::parse($dateFrom)->format('M d, Y').' — '.Carbon::parse($dateTo)->format('M d, Y')
                : 'All Time',
        ]);
    }
}
