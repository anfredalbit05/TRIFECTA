<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginModal extends Component
{
    public bool $openLogin = false;

    public $email = '';
    public $password = '';
    public bool $remember = false;


    #[On('open-login-modal')]
    public function openModal()
    {
        $this->openLogin = true;
    }


    public function close()
    {
        $this->reset([
            'email',
            'password',
            'remember'
        ]);

        $this->openLogin = false;
    }


    public function login()
    {
        $this->validate([
            'email' => 'required',
            'password' => 'required',
        ]);


        // Find account using email, mobile, or username
        $user = User::where('email', $this->email)
            ->orWhere('contactno', $this->email)
            ->orWhere('username', $this->email)
            ->first();


        if (!$user) {

            $this->addError(
                'email',
                'Account not found.'
            );

            return;
        }


        // Check password
        if (!Hash::check($this->password, $user->password)) {

            $this->addError(
                'password',
                'Incorrect password.'
            );

            return;
        }


        // // Check account status
        // if ($user->status !== 'Active') {

        //     $this->addError(
        //         'email',
        //         'Your account is still inactive.'
        //     );

        //     return;
        // }


        // Login user
        Auth::login(
            $user,
            $this->remember
        );


        session()->regenerate();


        // Close modal
        $this->openLogin = false;


        // Redirect
        return redirect()->route('dashboard');
    }


    public function switchToRegister()
    {
        // Close login modal
        $this->openLogin = false;

        // Open registration modal
        $this->dispatch('open-application-modal');
    }


    public function render()
    {
        return view('livewire.login-modal');
    }
}
