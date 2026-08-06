<?php

namespace App\Livewire\MemberPages\MyProfile;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Security extends Component
{
    public string $currentPassword = '';

    public string $newPassword = '';

    public string $newPasswordConfirmation = '';

    public function changePassword(): void
    {
        $this->validate([
            'currentPassword' => [
                'required',
            ],
            'newPassword' => [
                'required',
                'string',
                'min:8',
                'different:currentPassword',
            ],
            'newPasswordConfirmation' => [
                'required',
                'same:newPassword',
            ],
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (
            !Hash::check(
                $this->currentPassword,
                $user->password
            )
        ) {
            $this->addError(
                'currentPassword',
                'Your current password is incorrect.'
            );

            return;
        }

        // If inputs & current password match, trigger confirmation prompt
        $this->dispatch('prompt-password-confirmation');
    }

    public function executePasswordChange(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $user->update([
            'password' => Hash::make(
                $this->newPassword
            ),
        ]);

        $this->reset([
            'currentPassword',
            'newPassword',
            'newPasswordConfirmation',
        ]);

        $this->dispatch('password-updated');
    }

    public function render()
    {
        return view(
            'livewire.member-pages.my-profile.security'
        );
    }
}
