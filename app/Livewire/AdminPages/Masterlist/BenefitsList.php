<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Models\Benefit;
use App\Models\Privilege;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class BenefitsList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';
    public $isOpen = false;
    public $benefit_id;
    public string $search = ''; // Added search property

    // Form fields
    public $name;
    public $details;
    public $amount = 0;
    public $holdertype = '';
    public $status = true;

    // Validation rules
    protected $rules = [
        'name' => 'required|string|max:150',
        'details' => 'nullable|string',
        'amount' => 'required|numeric|min:0',
        'holdertype' => 'required|in:Primary Owner,Beneficiaries',
        'status' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Permission Checkers (ModuleID 4 assumed for Benefits)
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

        if ($user->role && method_exists($user->role, 'permissions')) {
            $hasPerm = optional($user->role->permissions)
                ->where('ModuleID', 4)
                ->where($column, 1)
                ->isNotEmpty();

            if ($hasPerm) {
                return true;
            }
        }

        return Privilege::where('RoleID', $user->RoleID)
            ->where('ModuleID', 4)
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

        $this->reset([
            'name',
            'details',
            'amount',
            'benefit_id'
        ]);

        $this->holdertype = 'Both';
        $this->status = true;

        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetValidation();
        $this->reset(['name', 'details', 'amount', 'holdertype', 'status', 'benefit_id']);
    }

    public function editBenefit($id)
    {
        if (!$this->canUpdate) {
            return;
        }

        $this->resetValidation();
        $benefit = Benefit::findOrFail($id);

        $this->benefit_id = $benefit->BentID;
        $this->name = $benefit->name;
        $this->details = $benefit->details;
        $this->amount = $benefit->amount;
        $this->holdertype = $benefit->holdertype;
        $this->status = $benefit->status;

        $this->isOpen = true;
    }

    public function saveBenefit()
    {
        if ($this->benefit_id && !$this->canUpdate) {
            return;
        }
        if (!$this->benefit_id && !$this->canCreate) {
            return;
        }

        $this->validate();

        if ($this->benefit_id) {
            $benefit = Benefit::findOrFail($this->benefit_id);
            $benefit->update([
                'name' => $this->name,
                'details' => $this->details,
                'amount' => $this->amount,
                'holdertype' => $this->holdertype,
                'status' => $this->status,
            ]);
            $message = 'Benefit successfully updated!';
        } else {
            Benefit::create([
                'name' => $this->name,
                'details' => $this->details,
                'amount' => $this->amount,
                'holdertype' => $this->holdertype,
                'status' => $this->status,
            ]);
            $message = 'Benefit successfully created!';
        }

        $this->closeModal();
        session()->flash('message', $message);
    }

    // Trigger SweetAlert popup
    public function confirmDeleteBenefit($id)
    {
        if (!$this->canDelete) {
            return;
        }

        $this->dispatch('confirm-delete', id: $id);
    }

    // Actual deletion executed via JS callback
    public function deleteBenefit($id)
    {
        if (!$this->canDelete) {
            return;
        }

        $benefit = Benefit::findOrFail($id);
        $benefit->delete();

        $this->dispatch('benefit-deleted');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetTable(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $benefits = Benefit::where('status', true)
            ->when(filled($this->search), function ($query) {
                $search = trim($this->search);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('details', 'like', "%{$search}%")
                        ->orWhere('holdertype', 'like', "%{$search}%");
                });
            })
            ->latest('BentID')
            ->paginate(10);

        return view('components.admin-pages.masterlist.benefits', [
            'benefits' => $benefits
        ]);
    }
}
