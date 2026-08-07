<?php

namespace App\Livewire\LandingPage;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use Livewire\WithPagination; // <-- 1. Import trait
use App\Models\Context;
use App\Models\Faq;

#[Layout('layouts.app')]
class How extends Component
{
    use WithPagination; // <-- 2. Use trait

    protected $paginationTheme = 'tailwind'; // <-- 3. Ensure Tailwind style pagination

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
            // <-- 4. Change get() to paginate(5) (adjust count as needed)
            'faqs' => Faq::where('status', true)
                ->latest('FaqID')
                ->paginate(5),
        ]);
    }
}
