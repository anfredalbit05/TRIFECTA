<?php

namespace App\Livewire\MemberPages\MyProfile;

use App\Models\Activation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ActivationButton extends Component
{
    public ?string $activationStatus = null;

    public function mount(): void
    {
        $this->loadStatus();
    }

    // Listen to all possible status-changing events
    #[On('account-activated')]
    #[On('activation-approved')]
    #[On('activation-rejected')]
    public function loadStatus(): void
    {
        $activation = Activation::where('UserID', Auth::id())
            ->latest()
            ->first();

        $this->activationStatus = $activation?->status;
    }

    public function render()
    {
        return view('livewire.member-pages.my-profile.activation-button');
    }
}
