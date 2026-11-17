<?php

namespace App\Livewire\AdminPages\ReportSettings;

use App\Livewire\Concerns\AuthorizesModules;
use App\Models\Variant;
use App\Support\Modules;
use Illuminate\Database\QueryException;
use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;
use Livewire\WithPagination;

class Variants extends Component
{
    use HasTabSkeleton;
    use AuthorizesModules;
    use WithPagination;

    public string $search = '';

    public bool $isOpen = false;
    public bool $confirmingDelete = false;

    public ?int $variant_id = null;

    public string $name = '';
    public string $description = '';
    public string $status = 'active';

    public function module(): int
    {
        return Modules::RS_GENERAL;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        abort_unless($this->canCreate, 403);

        $this->resetValidation();

        $this->variant_id = null;
        $this->name = '';
        $this->description = '';
        $this->status = 'active';

        $this->isOpen = true;
    }

    public function editVariant(int $id): void
    {
        abort_unless($this->canUpdate, 403);

        $variant = Variant::findOrFail($id);

        $this->resetValidation();

        $this->variant_id = $variant->VID;
        $this->name = $variant->name;
        $this->description = $variant->description ?? '';
        $this->status = strtolower((string) $variant->status);

        $this->isOpen = true;
    }

    public function saveVariant(): void
    {
        $isUpdating = $this->variant_id !== null;

        abort_unless(
            $isUpdating ? $this->canUpdate : $this->canCreate,
            403
        );

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        if ($isUpdating) {
            $variant = Variant::findOrFail($this->variant_id);
            $variant->update($validated);

            session()->flash('success', 'Variant updated successfully.');
        } else {
            Variant::create($validated);

            session()->flash('success', 'Variant created successfully.');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function closeModal(): void
    {
        $this->isOpen = false;

        $this->variant_id = null;
        $this->name = '';
        $this->description = '';
        $this->status = 'active';

        $this->resetValidation();
    }

    public function confirmDelete(int $id): void
    {
        abort_unless($this->canDelete, 403);

        Variant::findOrFail($id);

        $this->variant_id = $id;
        $this->confirmingDelete = true;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDelete = false;
        $this->variant_id = null;
    }

    public function deleteVariant(): void
    {
        abort_unless($this->canDelete, 403);

        $variant = Variant::findOrFail($this->variant_id);

        // Do not delete a variant that is still referenced.
        if (
            $variant->activations()->exists() ||
            $variant->membershipFees()->exists()
        ) {
            $this->confirmingDelete = false;
            $this->variant_id = null;

            session()->flash(
                'error',
                'This variant cannot be deleted because it is being used by activations or membership fees.'
            );

            return;
        }

        try {
            $variant->delete();
        } catch (QueryException $e) {
            report($e);

            $this->confirmingDelete = false;
            $this->variant_id = null;

            session()->flash(
                'error',
                'Unable to delete this variant because it is still referenced by other records.'
            );

            return;
        }

        $this->confirmingDelete = false;
        $this->variant_id = null;

        session()->flash('success', 'Variant deleted successfully.');

        $this->resetPage();
    }

    public function resetTable(): void
{
    $this->reset('search');
    $this->resetPage();
}


    public function render()
    {
        $variants = Variant::query()
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('VID')
            ->paginate(10);

        return view('livewire.admin-pages.report-settings.variants', [
            'variants' => $variants,
        ]);
    }
}
