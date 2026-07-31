<?php

namespace App\Livewire\MemberPages\MyProfile;

use App\Models\Activation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class MyProfile extends Component
{
    public ?string $activationStatus = null;

    public function mount(): void
    {
        $this->loadActivationStatus();
    }

    #[On('account-activated')]
    public function refreshActivation(): void
    {
        $this->loadActivationStatus();
    }

    public function loadActivationStatus(): void
    {
        $activation = Activation::where('UserID', Auth::id())
            ->latest()
            ->first();

        // This should store 'Pending', 'Approved', 'Rejected', or null
        $this->activationStatus = $activation?->status;
    }

    public function render()
    {
        return view('livewire.member-pages.my-profile');
    }
}
