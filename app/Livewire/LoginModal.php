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

    public string $email = '';
    public string $password = '';
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

        $this->resetValidation();
        $this->openLogin = false;
    }

    public function login()
    {
        // 1. Validate inputs
        $this->validate([
            'email' => 'required',
            'password' => 'required',
        ], [
            'email.required' => 'Please enter your email, mobile number, or username.',
            'password.required' => 'Please enter your password.',
        ]);

        // Trigger loading SweetAlert on frontend
        $this->dispatch('swal:loading');

        // Add a small artificial pause (e.g., ~1.5 to 3 seconds) to let users see the loading state smoothly
        usleep(1500000); // 1.5 seconds delay

        // 2. Find account using email, mobile number, or username
        $user = User::where('email', $this->email)
            ->orWhere('contactno', $this->email)
            ->orWhere('username', $this->email)
            ->first();

        if (!$user) {
            $this->dispatch('swal:close');
            $this->addError('email', 'Account not found.');
            return;
        }

        // 3. Verify password
        if (!Hash::check($this->password, $user->password)) {
            $this->dispatch('swal:close');
            $this->addError('password', 'Incorrect password.');
            return;
        }

        // 4. Authenticate user session
        Auth::login($user, $this->remember);
        session()->regenerate();

        // 5. Reset inputs & close modal view backdrop
        $this->reset([
            'email',
            'password',
            'remember',
        ]);
        $this->resetValidation();
        $this->openLogin = false;

        // Trigger success SweetAlert with message (displays for 3 seconds then redirects)
        $this->dispatch('swal:success', message: 'Welcome back, ' . ($user->name ?? $user->firstname) . '!');
    }

    public function switchToRegister()
    {
        $this->close();
        $this->dispatch('open-application-modal');
    }

    public function render()
    {
        return view('livewire.login-modal');
    }
}
