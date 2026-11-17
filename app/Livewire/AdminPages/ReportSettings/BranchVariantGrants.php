<?php

namespace App\Livewire\AdminPages\ReportSettings;

use App\Livewire\Concerns\AuthorizesModules;
use App\Models\Branch;
use App\Models\BranchVariantGrants as BranchVariantGrant;
use App\Models\Variant;
use App\Support\Modules;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;
use Livewire\WithPagination;

class BranchVariantGrants extends Component
{
    use HasTabSkeleton;
    use AuthorizesModules;
    use WithPagination;

    public string $search = '';

    public bool $isOpen = false;
    public bool $grantAllVariants = false;

    public ?int $grant_id = null;
    public ?int $branch_id = null;

    public array $selectedVariantIds = [];

    public string $status = 'Active';

    public function module(): int
    {
        return Modules::RS_GENERAL;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedGrantAllVariants(bool $value): void
    {
        if ($value) {
            $this->selectedVariantIds = [];
        }
    }

    public function openCreateModal(): void
    {
        abort_unless($this->canCreate, 403);

        $this->resetValidation();

        $this->grant_id = null;
        $this->branch_id = null;
        $this->selectedVariantIds = [];
        $this->grantAllVariants = false;
        $this->status = 'Active';
        $this->isOpen = true;
    }

    public function editGrant(int $id): void
    {
        abort_unless($this->canUpdate, 403);

        $grant = BranchVariantGrant::findOrFail($id);

        $this->resetValidation();

        $this->grant_id = $grant->GrantID;
        $this->branch_id = $grant->BranchID;
        $this->selectedVariantIds = [(string) $grant->VID];
        $this->grantAllVariants = false;
        $this->status = $grant->status;
        $this->isOpen = true;
    }

    public function saveGrant(): void
    {
        abort_unless(
            $this->grant_id !== null
                ? $this->canUpdate
                : $this->canCreate,
            403
        );

    $validated = $this->validate([
        'branch_id' => [
            'required',
            'integer',
            'exists:branches,BranchID',
        ],

        'selectedVariantIds' => $this->grantAllVariants
            ? ['nullable', 'array']
            : ['required', 'array', 'min:1'],

        'selectedVariantIds.*' => [
            'required',
            'integer',
            'distinct',
            'exists:variants,VID',
        ],

        'status' => [
            'required',
            'in:Active,Inactive',
        ],
    ]);

        $branchId = (int) $validated['branch_id'];

        $variantIds = $this->grantAllVariants
            ? Variant::query()
                ->whereRaw('LOWER(status) = ?', ['active'])
                ->pluck('VID')
                ->map(fn ($id) => (int) $id)
                ->all()
            : array_map(
                'intval',
                $validated['selectedVariantIds']
            );

        if (empty($variantIds)) {
            throw ValidationException::withMessages([
                'selectedVariantIds' =>
                    'There are no eligible variants to grant.',
            ]);
        }

        try {
            DB::transaction(function () use (
                $branchId,
                $variantIds,
                $validated
            ) {
                if ($this->grant_id !== null) {
                    $grant = BranchVariantGrant::findOrFail(
                        $this->grant_id
                    );

                    $duplicate = BranchVariantGrant::query()
                        ->where('BranchID', $branchId)
                        ->where('VID', $variantIds[0])
                        ->where('GrantID', '!=', $grant->GrantID)
                        ->exists();

                    if ($duplicate) {
                        throw ValidationException::withMessages([
                            'selectedVariantIds' =>
                                'This branch already has a grant for that variant.',
                        ]);
                    }

                    $grant->update([
                        'BranchID' => $branchId,
                        'VID' => $variantIds[0],
                        'status' => $validated['status'],
                    ]);

                    return;
                }

                foreach ($variantIds as $variantId) {
                    BranchVariantGrant::updateOrCreate(
                        [
                            'BranchID' => $branchId,
                            'VID' => $variantId,
                        ],
                        [
                            'status' => $validated['status'],
                        ]
                    );
                }
            });
        } catch (QueryException $e) {
            report($e);

            session()->flash(
                'error',
                'Unable to save the grants. Please check for duplicate records and try again.'
            );

            return;
        }

        session()->flash(
            'success',
            $this->grant_id !== null
                ? 'Branch variant grant updated successfully.'
                : count($variantIds) . ' variant grant(s) processed successfully.'
        );

        $this->closeModal();
        $this->resetPage();
    }

    public function toggleStatus(int $id): void
    {
        abort_unless($this->canUpdate, 403);

        $grant = BranchVariantGrant::findOrFail($id);

        $grant->update([
            'status' => $grant->status === 'Active'
                ? 'Inactive'
                : 'Active',
        ]);

        session()->flash(
            'success',
            'Grant status updated successfully.'
        );
    }

    public function deleteGrant(int $id): void
    {
        abort_unless($this->canDelete, 403);

        $grant = BranchVariantGrant::findOrFail($id);
        $grant->delete();

        session()->flash(
            'success',
            'Branch variant grant deleted successfully.'
        );

        $this->resetPage();
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->grant_id = null;
        $this->branch_id = null;
        $this->selectedVariantIds = [];
        $this->grantAllVariants = false;
        $this->status = 'Active';

        $this->resetValidation();
    }

    public function resetTable(): void
    {
        $this->reset('search');
        $this->resetPage();
    }


    public function render()
    {
        $grants = BranchVariantGrant::query()
            ->with(['branch', 'variant'])
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->whereHas('branch', function ($branchQuery) use ($search) {
                        $branchQuery->where(
                            'branchname',
                            'like',
                            "%{$search}%"
                        );
                    })->orWhereHas('variant', function ($variantQuery) use ($search) {
                        $variantQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    })->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('GrantID')
            ->paginate(10);

        return view(
            'livewire.admin-pages.report-settings.branch-variant-grants',
            [
                'grants' => $grants,
                'branches' => Branch::query()
                    ->where('status', true)
                    ->orderBy('branchname')
                    ->get(),
                'variants' => Variant::query()
                    ->whereRaw('LOWER(status) = ?', ['active'])
                    ->orderBy('name')
                    ->get(),
            ]
        );
    }
}
