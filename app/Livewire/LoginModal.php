<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

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
            'isForgotPassword',
        ]);

        $this->resetValidation();
        $this->openLogin = false;
    }

    public function toggleForgotPassword()
    {
        $this->isForgotPassword = ! $this->isForgotPassword;

        // When the user opens the forgot-password view from the login form,
        // prefill the email field with whatever they already typed.
        if ($this->isForgotPassword && filled($this->email)) {
            $this->forgotEmail = $this->email;
        }

        $this->resetValidation();
    }

    /**
     * Return to the sign-in view after a reset link has been sent.
     */
    public function resetToLogin(): void
    {
        $this->isForgotPassword = false;
        $this->forgotEmail = '';
        $this->resetValidation();
    }

    public function sendPasswordResetLink()
    {
        try {
            $this->validate([
                'forgotEmail' => 'required|email|exists:users,email',
            ], [
                'forgotEmail.required' => 'Please enter your account email address.',
                'forgotEmail.email' => 'Please enter a valid email address.',
                'forgotEmail.exists' => 'No account was found with that email address.',
            ]);
        } catch (ValidationException $e) {
            // Close the loading dialog so the inline field error is visible.
            $this->dispatch('swal:close');

            throw $e;
        }

        $status = Password::sendResetLink(['email' => $this->forgotEmail]);

        // Close the loading dialog before showing the result.
        $this->dispatch('swal:close');

        if ($status == Password::RESET_LINK_SENT) {
            $this->dispatch(
                'password-reset-link-sent',
                message: __($status)
            );
        } else {
            $this->addError('forgotEmail', __($status));
        }
    }

    public function login()
    {
        try {
            $this->validate([
                'email' => 'required',
                'password' => 'required',
            ], [
                'email.required' => 'Please enter your email, mobile number, or username.',
                'password.required' => 'Please enter your password.',
            ]);
        } catch (ValidationException $e) {
            // Close the loading dialog so the inline field error is visible.
            $this->dispatch('swal:close');

            throw $e;
        }

        $user = User::where('email', $this->email)
            ->orWhere('contactno', $this->email)
            ->orWhere('username', $this->email)
            ->first();

        if (! $user) {
            $this->dispatch('swal:close');
            $this->addError('email', 'Account not found.');

            return;
        }

        if (! Hash::check($this->password, $user->password)) {
            $this->dispatch('swal:close');
            $this->addError('password', 'Incorrect password.');

            return;
        }

        Auth::login($user, $this->remember);
        session()->regenerate();

        $this->reset(['email', 'password', 'remember', 'forgotEmail', 'isForgotPassword']);
        $this->resetValidation();
        $this->openLogin = false;

        $this->dispatch('swal:successed', message: 'Welcome back, '.($user->name ?? $user->firstname).'!');
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

    public function mount()
    {
        // Only open the login modal automatically if the user is a GUEST (not authenticated)
        if (! Auth::check() && (request()->routeIs('secret.login') || request()->has('auto_login'))) {
            $this->openLogin = true;
        }
    }
}
