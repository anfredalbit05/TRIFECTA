<?php

namespace App\Livewire\LandingPage;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use App\Models\Context;
use App\Models\User;

#[Layout('layouts.app')]
class Home extends Component
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
        // Query total registered users (or filter by status/RoleID if required, e.g. ->where('status', 'Active'))
        $totalMembers = User::count();

        return view('livewire.landing-page.home', [
            'totalMembers' => $totalMembers,
        ]);
    }
}
