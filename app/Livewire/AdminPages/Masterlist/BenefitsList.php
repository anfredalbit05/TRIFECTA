<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Models\Benefit;
use Livewire\Component;

class BenefitsList extends Component
{
    public $isOpen = false;
    public $benefit_id;

    // Form fields
    public $name;
    public $details;
    public $amount = 0;
    public $holdertype = 'Principal';
    public $status = true;

    // Validation rules
    protected $rules = [
        'name' => 'required|string|max:150',
        'details' => 'nullable|string',
        'amount' => 'required|numeric|min:0',
        'holdertype' => 'required|in:Principal,Dependent,Both',
        'status' => 'boolean',
    ];

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['name', 'details', 'amount', 'holdertype', 'status', 'benefit_id']);
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
        $this->dispatch('confirm-delete', id: $id);
    }

    // Actual deletion executed via JS callback
    public function deleteBenefit($id)
    {
        $benefit = Benefit::findOrFail($id);
        $benefit->delete();

        $this->dispatch('benefit-deleted');
    }

    public function render()
    {
        return view('components.admin-pages.masterlist.benefits', [
            'benefits' => Benefit::where('status', true)->get()
        ]);
    }
}
