<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Models\Branch;
use App\Models\Privilege;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class BranchesList extends Component
{

    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $isOpen = false;
    public $branch_id;
    public string $search = ''; // Added search property
    public string $dateRange = '';   // e.g. "2026-08-01 to 2026-08-16"

    // Form fields
    public $branchCode;
    public $branchname;
    public $address;
    public $status = true;
    public $UserID;

    // Validation rules
    protected $rules = [
        'branchname' => 'required|string|max:150',
        'address' => 'required|string',
        'status' => 'boolean',
        'UserID' => 'nullable|exists:users,UserID',
    ];

       public function mount()
    {
        $this->dateRange = '';
    }
    /*
    |--------------------------------------------------------------------------
    | Permission Checkers (ModuleID 3 assumed for Branches)
    |--------------------------------------------------------------------------
    */

    protected function hasPermission($action): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Super Admin override (RoleID 1)
        if ((int) $user->RoleID === 1) {
            return true;
        }

        $column = 'can_' . $action;

        // Safely check relation or fall back directly to query to prevent null errors
        if ($user->role && method_exists($user->role, 'permissions')) {
            $hasPerm = optional($user->role->permissions)
                ->where('ModuleID', 3)
                ->where($column, 1)
                ->isNotEmpty();

            if ($hasPerm) {
                return true;
            }
        }

        // Direct fallback query check
        return Privilege::where('RoleID', $user->RoleID)
            ->where('ModuleID', 3)
            ->where($column, 1)
            ->exists();
    }

    public function getCanCreateProperty(): bool
    {
        return $this->hasPermission('create');
    }

    public function getCanReadProperty(): bool
    {
        return $this->hasPermission('read');
    }

    public function getCanUpdateProperty(): bool
    {
        return $this->hasPermission('update');
    }

    public function getCanDeleteProperty(): bool
    {
        return $this->hasPermission('delete');
    }

    /*
    |--------------------------------------------------------------------------
    | Component Actions
    |--------------------------------------------------------------------------
    */

    public function openCreateModal()
    {
        if (!$this->canCreate) {
            return;
        }

        $this->resetValidation();
        $this->reset(['branchCode', 'branchname', 'address', 'status', 'UserID', 'branch_id']);
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetValidation();
        $this->reset(['branchCode', 'branchname', 'address', 'status', 'UserID', 'branch_id']);
    }

    public function editBranch($id)
    {
        if (!$this->canUpdate) {
            return;
        }

        $this->resetValidation();
        $branch = Branch::findOrFail($id);

        $this->branchname = $branch->branchname;
        $this->address = $branch->address;
        $this->status = $branch->status;
        $this->UserID = $branch->UserID;

        $this->isOpen = true;
    }

    public function saveBranch()
    {
        if ($this->branch_id && !$this->canUpdate) {
            return;
        }
        if (!$this->branch_id && !$this->canCreate) {
            return;
        }

        $this->validate();

        $data = [
            'branchname' => $this->branchname,
            'address' => $this->address,
            'status' => $this->status,
            'UserID' => $this->UserID ?: (Auth::id() ?? 1),
        ];

        if ($this->branch_id) {
            $branch = Branch::findOrFail($this->branch_id);
            $branch->update($data);
            $message = 'Branch successfully updated!';
        } else {
            Branch::create($data);
            $message = 'Branch successfully created!';
        }

        $this->closeModal();
        session()->flash('message', $message);
    }

    public function confirmDeleteBranch($id)
    {
        if (!$this->canDelete) {
            return;
        }

        $this->dispatch('confirm-delete', id: $id);
    }

    public function deleteBranch($id)
    {
        if (!$this->canDelete) {
            return;
        }

        $branch = Branch::findOrFail($id);
        $branch->delete();

        $this->dispatch('branch-deleted');
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

        $branches = Branch::where('status', true)
            ->when(filled($this->search), function ($query) {
                $search = trim($this->search);
                $query->where(function ($q) use ($search) {
                    $q->where('branchCode', 'like', "%{$search}%")
                        ->orWhere('branchname', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when($dateFrom && $dateTo, function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [
                    Carbon::parse($dateFrom)->startOfDay(),
                    Carbon::parse($dateTo)->endOfDay(),
                ]);
            })
            ->latest('BranchID')
            ->paginate(10);

        return view('components.admin-pages.masterlist.branches', [
            'branches' => $branches
        ]);
    }
}
