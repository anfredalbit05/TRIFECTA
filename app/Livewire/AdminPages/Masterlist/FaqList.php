<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Livewire\Concerns\AuthorizesModules;
use App\Models\Faq;
use App\Support\Modules;
use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;
use Livewire\WithPagination;

class FaqList extends Component
{
    use HasTabSkeleton;
    use AuthorizesModules;
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
    | Permission Checkers — global module system (Masterlist - FAQs)
    |--------------------------------------------------------------------------
    */

    protected function module(): int
    {
        return Modules::FAQS;
    }

    /*
    |--------------------------------------------------------------------------
    | Component Actions
    |--------------------------------------------------------------------------
    */

    public function openCreateModal()
    {
        if (! $this->canCreate) {
            return;
        }

        $this->resetValidation();

        $this->reset([
            'question',
            'answer',
            'faq_id',
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
            'faq_id',
        ]);
    }

    public function editFaq($id)
    {
        if (! $this->canUpdate) {
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
        if ($this->faq_id && ! $this->canUpdate) {
            return;
        }
        if (! $this->faq_id && ! $this->canCreate) {
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
        if (! $this->canDelete) {
            return;
        }

        $this->dispatch('confirm-delete-faq', id: $id);
    }

    // Delete after confirmation
    public function deleteFaq($id)
    {
        if (! $this->canDelete) {
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

        return view('livewire.admin-pages.masterlist.faqs', [
            'faqs' => $faqs,
        ]);
    }
}
