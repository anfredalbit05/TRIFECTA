<?php

namespace App\Livewire\LandingPage;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use App\Models\Context;
use App\Models\Inquiry;

#[Layout('layouts.app')]
class Contact extends Component
{
    public array $contexts = [];

    // Inquiry Form Fields
    public $name = '';
    public $email = '';
    public $subject = '';
    public $category = '';
    public $details = '';

    protected $rules = [
        'name' => 'required|string|max:150',
        'email' => 'required|email|max:255',
        'subject' => 'required|string|max:255',
        'category' => 'nullable|string|max:100',
        'details' => 'required|string',
    ];

    public function mount()
    {
        $this->loadContexts();
    }

    public function loadContexts()
    {
        $this->contexts = Context::merged();
    }

    #[On('context-updated')]
    public function refreshContexts()
    {
        $this->loadContexts();
    }

    // Step 1: Trigger validation and prompt SweetAlert confirmation
    public function confirmInquiry()
    {
        $this->validate();

        $this->dispatch('swal:confirm', [
            'title' => 'Submit Inquiry?',
            'text' => 'Are you ready to send this message to our team?',
            'icon' => 'question',
            'nextMethod' => 'submitInquiry'
        ]);
    }

    // Step 2: Handle actual database creation after confirmation
    // Step 2: Handle actual database creation after confirmation
    #[On('submitInquiry')]
    public function submitInquiry()
    {
        $this->validate();

        Inquiry::create([
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'category' => $this->category,
            'details' => $this->details,
        ]);

        $this->reset(['name', 'email', 'subject', 'category', 'details']);

        // Dispatch success popup to frontend
        $this->dispatch('swal:success', [
            'title' => 'Sent Successfully!',
            'text' => 'Your inquiry has been successfully sent! We will get back to you soon.'
        ]);

        session()->flash('inquiry_success', 'Your inquiry has been successfully sent! We will get back to you soon.');
    }
    public function render()
    {
        return view('livewire.landing-page.contact');
    }
}
