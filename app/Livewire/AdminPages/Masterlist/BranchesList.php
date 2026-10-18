<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Livewire\Concerns\AuthorizesModules;
use App\Models\Branch;
use App\Support\Modules;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class BranchesList extends Component
{
    use AuthorizesModules, WithPagination;

    protected $paginationTheme = 'tailwind';

    public bool $isOpen = false;
    public $branch_id = null;

    public string $search = '';
    public string $dateRange = '';

    public $branchname = '';
    public $address = '';
    public string $status = '1';
    public $UserID = null;

    protected function rules(): array
    {
        return [
            'branchname' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string'],
            'status' => ['required', Rule::in(['1', '0'])],
            'UserID' => ['nullable', 'exists:users,UserID'],
        ];
    }

    protected function module(): int
    {
        return Modules::BRANCHES;
    }

    public function openCreateModal(): void
    {
        if (! $this->canCreate) {
            return;
        }

        $this->resetValidation();

        $this->reset([
            'branch_id',
            'branchname',
            'address',
            'UserID',
        ]);

        $this->status = '1';
        $this->isOpen = true;
    }

    public function editBranch($id): void
    {
        if (! $this->canUpdate) {
            return;
        }

        $branch = Branch::findOrFail($id);

        $this->resetValidation();

        $this->branch_id = $branch->BranchID;
        $this->branchname = $branch->branchname;
        $this->address = $branch->address;

        // Convert the database Boolean to the select's string value.
        $this->status = $branch->status ? '1' : '0';

        $this->UserID = $branch->UserID;
        $this->isOpen = true;
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->resetValidation();

        $this->reset([
            'branch_id',
            'branchname',
            'address',
            'UserID',
        ]);

        $this->status = '1';
    }

    public function saveBranch(): void
    {
        if ($this->branch_id) {
            if (! $this->canUpdate) {
                return;
            }

            $branch = Branch::findOrFail($this->branch_id);
        } else {
            if (! $this->canCreate) {
                return;
            }

            $branch = new Branch();
        }

        $validated = $this->validate();

        $branch->branchname = $validated['branchname'];
        $branch->address = $validated['address'];

        // Save '1' as true and '0' as false.
        $branch->status = $validated['status'] === '1';

        $branch->UserID = $validated['UserID'] ?: Auth::id();

        $branch->save();

        $message = $this->branch_id
            ? 'Branch successfully updated!'
            : 'Branch successfully created!';

        $this->closeModal();

        session()->flash('message', $message);
    }

    public function confirmDeleteBranch($id): void
    {
        if ($this->canDelete) {
            $this->dispatch('confirm-delete', id: $id);
        }
    }

    public function deleteBranch($id): void
    {
        if (! $this->canDelete) {
            return;
        }

        Branch::findOrFail($id)->delete();

        session()->flash('message', 'Branch successfully deleted!');
        $this->dispatch('branch-deleted');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDateRange(): void
    {
        $this->resetPage();
    }

    protected function parsedDateRange(): array
    {
        if (! $this->dateRange || ! str_contains($this->dateRange, ' to ')) {
            return [null, null];
        }

        [$from, $to] = explode(' to ', $this->dateRange, 2);

        try {
            return [
                Carbon::parse(trim($from))->startOfDay(),
                Carbon::parse(trim($to))->endOfDay(),
            ];
        } catch (\Throwable $e) {
            return [null, null];
        }
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

        $branches = Branch::query()
            ->when(filled($this->search), function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('branchCode', 'like', "%{$search}%")
                        ->orWhere('branchname', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when($dateFrom && $dateTo, function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            })
            ->latest('BranchID')
            ->paginate(10);

        return view('components.admin-pages.masterlist.branches', [
            'branches' => $branches,
        ]);
    }
}
