<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Roles extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public bool $isOpen = false;

    public $role_id = null;

    public string $search = '';
    public string $dateRange = '';

    public string $rolename = '';
    public string $description = '';
    public string $status = '1';

    /**
     * Validation rules.
     */
    protected function rules(): array
    {
        return [
            'rolename' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'rolename')
                    ->ignore($this->role_id, 'RoleID'),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                Rule::in(['1', '0']),
            ],
        ];
    }

    /**
     * Open create modal.
     */
    public function openCreateModal(): void
    {
        $this->resetValidation();

        $this->reset([
            'role_id',
            'rolename',
            'description',
        ]);

        $this->status = '1';
        $this->isOpen = true;
    }

    /**
     * Open edit modal.
     */
    public function editRole($id): void
    {
        $role = Role::findOrFail($id);

        $this->resetValidation();

        $this->role_id = $role->RoleID;
        $this->rolename = $role->rolename;
        $this->description = $role->description ?? '';
        $this->status = $role->status ? '1' : '0';

        $this->isOpen = true;
    }

    /**
     * Close modal.
     */
    public function closeModal(): void
    {
        $this->isOpen = false;

        $this->resetValidation();

        $this->reset([
            'role_id',
            'rolename',
            'description',
        ]);

        $this->status = '1';
    }

    /**
     * Save or update role.
     */
    public function saveRole(): void
    {
        $validated = $this->validate();

        if ($this->role_id) {
            $role = Role::findOrFail($this->role_id);
        } else {
            $role = new Role();
        }

        $role->rolename = trim($validated['rolename']);

        $role->description = filled($validated['description'])
            ? trim($validated['description'])
            : null;

        $role->status = $validated['status'] === '1';

        $role->save();

        $message = $this->role_id
            ? 'Role successfully updated!'
            : 'Role successfully created!';

        $this->closeModal();

        session()->flash('message', $message);
    }

    /**
     * Confirm delete.
     */
    public function confirmDeleteRole($id): void
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    /**
     * Delete role.
     */
    public function deleteRole($id): void
    {
        $role = Role::findOrFail($id);

        $role->delete();

        session()->flash('message', 'Role successfully deleted!');

        $this->dispatch('role-deleted');
    }

    /**
     * Reset pagination when searching.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when date range changes.
     */
    public function updatedDateRange(): void
    {
        $this->resetPage();
    }

    /**
     * Parse date range.
     */
    protected function parsedDateRange(): array
    {
        if (
            ! $this->dateRange ||
            ! str_contains($this->dateRange, ' to ')
        ) {
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

    /**
     * Clear date filter.
     */
    public function clearDateFilter(): void
    {
        $this->dateRange = '';

        $this->resetPage();

        $this->dispatch('ranges-reset');
    }

    /**
     * Reset table filters.
     */
    public function resetTable(): void
    {
        $this->search = '';
        $this->dateRange = '';

        $this->resetPage();

        $this->dispatch('ranges-reset');
    }

    /**
     * Render roles table.
     */
    public function render()
    {
        [$dateFrom, $dateTo] = $this->parsedDateRange();

        $roles = Role::query()
            ->when(filled($this->search), function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('RoleID', 'like', "%{$search}%")
                        ->orWhere('rolename', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($dateFrom && $dateTo, function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [
                    $dateFrom,
                    $dateTo,
                ]);
            })
            ->latest('RoleID')
            ->paginate(10);

        return view(
            'livewire.admin-pages.masterlist.roles',
            [
                'roles' => $roles,
            ]
        );
    }
}
