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

        // 2. Find account using email, mobile number, or username
        $user = User::where('email', $this->email)
            ->orWhere('contactno', $this->email)
            ->orWhere('username', $this->email)
            ->first();

        if (!$user) {
            $this->addError('email', 'Account not found.');
            return;
        }

        // 3. Verify password
        if (!Hash::check($this->password, $user->password)) {
            $this->addError('password', 'Incorrect password.');
            return;
        }

        // Optional: Check account status if enabled
        // if ($user->status !== 'Active') {
        //     $this->addError('email', 'Your account is currently inactive.');
        //     return;
        // }

        // 4. Authenticate user session
        Auth::login($user, $this->remember);

        session()->regenerate();

        // 5. Reset inputs & close modal
        $this->reset(['email', 'password', 'remember']);
        $this->openLogin = false;

        // 6. Redirect to dashboard
        return redirect()->route('dashboard');
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
