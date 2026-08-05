<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Models\Faq;
use App\Models\Privilege;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class FaqList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $isOpen = false;
    public $faq_id;
    public string $search = ''; // Added search property

    // Form fields
    public $question;
    public $answer;
    public $status = true;

    // Validation rules
    protected $rules = [
        'question' => 'required|string|max:255',
        'answer' => 'required|string',
        'status' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Permission Checkers (ModuleID assumed, e.g., 5 or matching your DB schema)
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
        $moduleID = 6; // Adjust this ID to match your FAQs module ID in the database

        if ($user->role && method_exists($user->role, 'permissions')) {
            $hasPerm = optional($user->role->permissions)
                ->where('ModuleID', $moduleID)
                ->where($column, 1)
                ->isNotEmpty();

            if ($hasPerm) {
                return true;
            }
        }

        return Privilege::where('RoleID', $user->RoleID)
            ->where('ModuleID', $moduleID)
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
            'question',
            'answer',
            'faq_id'
        ]);

        $this->status = true;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetValidation();
        $this->reset([
            'question',
            'answer',
            'status',
            'faq_id'
        ]);
    }

    public function editFaq($id)
    {
        if (!$this->canUpdate) {
            return;
        }

        $this->resetValidation();

        $faq = Faq::findOrFail($id);

        $this->faq_id = $faq->FaqID;
        $this->question = $faq->question;
        $this->answer = $faq->answer;
        $this->status = $faq->status;

        $this->isOpen = true;
    }

    public function saveFaq()
    {
        if ($this->faq_id && !$this->canUpdate) {
            return;
        }
        if (!$this->faq_id && !$this->canCreate) {
            return;
        }

        $this->validate();

        if ($this->faq_id) {
            $faq = Faq::findOrFail($this->faq_id);

            $faq->update([
                'question' => $this->question,
                'answer' => $this->answer,
                'status' => $this->status,
            ]);

            $message = 'FAQ successfully updated!';
        } else {
            Faq::create([
                'question' => $this->question,
                'answer' => $this->answer,
                'status' => $this->status,
            ]);

            $message = 'FAQ successfully created!';
        }

        $this->closeModal();
        session()->flash('message', $message);
    }

    // SweetAlert confirmation
    public function confirmDeleteFaq($id)
    {
        if (!$this->canDelete) {
            return;
        }

        $this->dispatch('confirm-delete-faq', id: $id);
    }

    // Delete after confirmation
    public function deleteFaq($id)
    {
        if (!$this->canDelete) {
            return;
        }

        $faq = Faq::findOrFail($id);
        $faq->delete();

        $this->dispatch('faq-deleted');
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
        $faqs = Faq::when(filled($this->search), function ($query) {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                    ->orWhere('answer', 'like', "%{$search}%");
            });
        })
            ->latest('FaqID')
            ->paginate(10);

        return view('components.admin-pages.masterlist.faqs', [
            'faqs' => $faqs
        ]);
    }
}
