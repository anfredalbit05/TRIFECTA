<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Models\Branch;
use Livewire\Component;

class BranchesList extends Component
{
    public $isOpen = false;
    public $branch_id;

    // Form fields
    public $branchCode;
    public $branchname;
    public $type;
    public $address;
    public $status = true;
    public $UserID; // Assuming you might want to assign or default this

    // Validation rules
    protected $rules = [
        'branchCode' => 'required|string|max:20',
        'branchname' => 'required|string|max:150',
       'type' => 'required|in:Branch,Satellite', // Updated to restrict values to Branch or Satellite
        'address' => 'required|string',
        'status' => 'boolean',
        'UserID' => 'nullable|exists:users,UserID',
    ];

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['branchCode', 'branchname', 'type', 'address', 'status', 'UserID', 'branch_id']);
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetValidation();
        $this->reset(['branchCode', 'branchname', 'type', 'address', 'status', 'UserID', 'branch_id']);
    }

    public function editBranch($id)
    {
        $this->resetValidation();
        $branch = Branch::findOrFail($id);

        $this->branch_id = $branch->BranchID;
        $this->branchCode = $branch->branchCode;
        $this->branchname = $branch->branchname;
        $this->type = $branch->type;
        $this->address = $branch->address;
        $this->status = $branch->status;
        $this->UserID = $branch->UserID;

        $this->isOpen = true;
    }

    public function saveBranch()
    {
        $this->validate();

        // Fallback or assignment for UserID if needed (e.g., auth()->id())
        $data = [
            'branchCode' => $this->branchCode,
            'branchname' => $this->branchname,
            'type' => $this->type,
            'address' => $this->address,
            'status' => $this->status,
            'UserID' => $this->UserID ?: 1, // Change default or link to auth user as appropriate
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

    // Trigger SweetAlert popup
    public function confirmDeleteBranch($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    // Actual deletion executed via JS callback
    public function deleteBranch($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->delete();

        $this->dispatch('branch-deleted');
    }

 public function render()
{
    return view('components.admin-pages.masterlist.branches', [
        'branches' => Branch::where('status', true)
            ->latest('BranchID')
            ->get()
    ]);
}
}
