<?php

namespace App\Livewire\LandingPage;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use App\Models\Context;
use App\Models\Branch;

#[Layout('layouts.app')]
class Team extends Component
{
    public array $contexts = [];

    public bool $showCeoModal = false;

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

    public function openCeoModal()
    {
        $this->showCeoModal = true;
    }

    public function closeCeoModal()
    {
        $this->showCeoModal = false;
    }

    public function render()
    {
        return view('livewire.landing-page.team', [
        ]);
    }
}
