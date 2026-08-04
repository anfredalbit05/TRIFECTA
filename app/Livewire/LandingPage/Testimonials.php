<?php

namespace App\Livewire\LandingPage;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use Livewire\WithPagination; // <-- 1. Import trait
use App\Models\Context;
use App\Models\Testimonial;

#[Layout('layouts.app')]
class Testimonials extends Component
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
        return view('livewire.landing-page.testimonials', [
            // 4. Use paginate(6) to display 6 items per page (2 rows of 3 columns)
            'testimonials' => Testimonial::where('status', 'Published')
                ->latest('TestimonialsID')
                ->paginate(6)
        ]);
    }
}
