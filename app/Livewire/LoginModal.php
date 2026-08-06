<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\User;

class LoginModal extends Component
{
    public bool $openLogin = false;
    public bool $isForgotPassword = false; // Toggle view

    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public string $forgotEmail = ''; // Specific email for password reset

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
            'remember',
            'forgotEmail',
            'isForgotPassword'
        ]);

        $this->resetValidation();
        $this->openLogin = false;
    }

    public function toggleForgotPassword()
    {
        $this->isForgotPassword = !$this->isForgotPassword;
        $this->resetValidation();
    }

    public function sendPasswordResetLink()
    {
        $this->validate([
            'forgotEmail' => 'required|email|exists:users,email',
        ], [
            'forgotEmail.required' => 'Please enter your account email address.',
            'forgotEmail.email' => 'Please enter a valid email address.',
            'forgotEmail.exists' => 'We can not find a user with that email address.',
        ]);

        $status = Password::sendResetLink(['email' => $this->forgotEmail]);

        $this->dispatch('swal:close');

        if ($status == Password::RESET_LINK_SENT) {
            $this->dispatch('swal:info', message: __($status));
            $this->toggleForgotPassword();
            $this->reset('forgotEmail');
        } else {
            $this->addError('forgotEmail', __($status));
        }
    }

    public function login()
    {
        $this->validate([
            'email' => 'required',
            'password' => 'required',
        ], [
            'email.required' => 'Please enter your email, mobile number, or username.',
            'password.required' => 'Please enter your password.',
        ]);

        $user = User::where('email', $this->email)
            ->orWhere('contactno', $this->email)
            ->orWhere('username', $this->email)
            ->first();

        if (!$user) {
            $this->dispatch('swal:close');
            $this->addError('email', 'Account not found.');
            return;
        }

        if (!Hash::check($this->password, $user->password)) {
            $this->dispatch('swal:close');
            $this->addError('password', 'Incorrect password.');
            return;
        }

        Auth::login($user, $this->remember);
        session()->regenerate();

        $this->reset(['email', 'password', 'remember', 'forgotEmail', 'isForgotPassword']);
        $this->resetValidation();
        $this->openLogin = false;

        $this->dispatch('swal:successed', message: 'Welcome back, ' . ($user->name ?? $user->firstname) . '!');
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
