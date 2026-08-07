<?php

namespace App\Livewire\AdminPages\ReportSettings;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserAccounts extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public string $roleFilter = '';
    public string $statusFilter = '';
    public ?int $selectedUserLedgerID = null;
    public bool $isLedgerModalOpen = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

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

    public function resetTable(): void
    {
        $this->search = '';
        $this->roleFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
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

    public function render()
    {
        $users = User::with(['role', 'wallets', 'ledgers'])
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
            ->latest('UserID')
            ->paginate(10);

        $selectedUser = $this->selectedUserLedgerID
            ? User::with(['wallets', 'ledgers'])->find($this->selectedUserLedgerID)
            : null;

        return view('livewire.admin-pages.report-settings.users-accounts', [
            'users' => $users,
            'selectedUser' => $selectedUser,
        ]);
    }
}
