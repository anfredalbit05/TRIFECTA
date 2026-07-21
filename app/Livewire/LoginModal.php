<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class LoginModal extends Component
{
    public bool $openLogin = false;

    #[On('open-login-modal')]
    public function openModal()
    {
        $this->openLogin = true;
    }

    public function close()
    {
        $this->openLogin = false;
    }

    public function switchToRegister()
    {
        // 1. Close current login modal
        $this->openLogin = false;

        // 2. Dispatch event to open the application / register modal
        $this->dispatch('open-application-modal');
    }

    public function render()
    {
        return view('livewire.login-modal');
    }
}
