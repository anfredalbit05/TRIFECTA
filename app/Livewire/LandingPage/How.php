<?php

namespace App\Livewire\LandingPage;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use App\Models\Context;
use App\Models\Faq;

#[Layout('layouts.app')]
class How extends Component
{
    public array $contexts = [];

    public function mount()
    {
        $this->loadContexts();
    }

    public function loadContexts()
    {
        $this->contexts = Context::pluck(
            'details',
            'context_key'
        )->toArray();
    }

    #[On('context-updated')]
    public function refreshContexts()
    {
        $this->loadContexts();
    }

    public function render()
    {
        return view('livewire.landing-page.how-it-works', [
            'faqs' => Faq::where('status', true)
                ->latest('FaqID')
                ->get(),
        ]);
    }
}
