<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Models\Faq;
use Livewire\Component;
use Livewire\WithPagination;

class FaqList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $isOpen = false;
    public $faq_id;

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


    public function openCreateModal()
    {
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
        $this->dispatch('confirm-delete-faq', id: $id);
    }



    // Delete after confirmation
    public function deleteFaq($id)
    {
        $faq = Faq::findOrFail($id);

        $faq->delete();

        $this->dispatch('faq-deleted');
    }



    public function render()
    {
        return view('components.admin-pages.masterlist.faqs', [
            'faqs' => Faq::latest('FaqID')
                ->paginate(10)
        ]);
    }
}
