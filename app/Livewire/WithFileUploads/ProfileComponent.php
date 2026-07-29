<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileComponent extends Component
{
    use WithFileUploads;

    public $profilepic;

    public function save(): void
    {
        $this->validate([
            'profilepic' => [
                'required',
                'image',
                'max:2048',
            ],
        ]);

        /** @var User|null $user */
        $user = Auth::user();

        if (!$user instanceof User) {
            $this->addError(
                'profilepic',
                'You must be logged in to update your profile picture.'
            );

            return;
        }

        // Delete old profile picture
        if (
            $user->profilepic &&
            Storage::disk('public')->exists($user->profilepic)
        ) {
            Storage::disk('public')->delete(
                $user->profilepic
            );
        }

        // Store new profile picture
        $path = $this->profilepic->store(
            'profiles',
            'public'
        );

        // Save the path to the users table
        $user->update([
            'profilepic' => $path,
        ]);

        $this->reset('profilepic');

        session()->flash(
            'profile-success',
            'Profile picture updated successfully.'
        );
    }

    public function render()
    {
        return view('livewire.profile-component');
    }
}
