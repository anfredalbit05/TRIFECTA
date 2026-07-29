<?php

namespace App\Livewire\MemberPages\MyProfile;

use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Profile extends Component
{
    use WithFileUploads;

    public $profilepic = null;

    public string $firstname = '';
    public string $lastname = '';
    public string $middlename = '';

    public ?string $birthdate = null;
    public ?string $contactno = null;
    public ?string $address = null;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->firstname = $user->firstname ?? '';
        $this->lastname = $user->lastname ?? '';
        $this->middlename = $user->middlename ?? '';

        $this->birthdate = $user->birthdate
            ? Carbon::parse($user->birthdate)->format('Y-m-d')
            : null;

        $this->contactno = $user->contactno ?? '';
        $this->address = $user->address ?? '';
    }

    public function updatedProfilepic(): void
    {
        $this->validateOnly('profilepic', [
            'profilepic' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);
    }

    public function saveProfile(): void
    {
        $this->validate([
            'firstname' => [
                'required',
                'string',
                'max:100',
            ],

            'lastname' => [
                'required',
                'string',
                'max:100',
            ],

            'middlename' => [
                'nullable',
                'string',
                'max:100',
            ],

            'birthdate' => [
                'nullable',
                'date',
                'before:today',
            ],

            'contactno' => [
                'nullable',
                'string',
                'max:20',
            ],

            'address' => [
                'nullable',
                'string',
            ],
        ]);

        /** @var User $user */
        $user = Auth::user();

        $user->update([
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'middlename' => $this->middlename,
            'birthdate' => $this->birthdate,
            'contactno' => $this->contactno,
            'address' => $this->address,
        ]);

        // Refresh the authenticated user
        $user->refresh();

        // Notify the sidebar
        $this->dispatch('profile-updated');

        session()->flash(
            'profile-success',
            'Personal information updated successfully.'
        );
    }

    public function updateProfilePicture(): void
    {
        $this->validate([
            'profilepic' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Delete old picture
        if (
            !empty($user->profilepic) &&
            Storage::disk('public')->exists($user->profilepic)
        ) {
            Storage::disk('public')->delete(
                $user->profilepic
            );
        }

        // Save new picture
        $path = $this->profilepic->store(
            'profiles',
            'public'
        );

        // Update database
        $user->update([
            'profilepic' => $path,
        ]);

        // IMPORTANT:
        // Reload the latest user data from the database
        $user->refresh();

        // Notify the sidebar to refresh
        $this->dispatch('profile-updated');

        $this->reset('profilepic');

        session()->flash(
            'picture-success',
            'Profile picture updated successfully.'
        );
    }

    public function render()
    {
        return view(
            'livewire.member-pages.my-profile.profile'
        );
    }
}
