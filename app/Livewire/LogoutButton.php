<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LogoutButton extends Component
{

    public function confirmLogout()
    {
        $this->dispatch('confirm-logout');
    }


    public function logout()
    {
        Auth::logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('home');
    }


    public function render()
    {
        return view('livewire.logout-button');
    }
}
