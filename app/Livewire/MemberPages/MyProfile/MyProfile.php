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

    #[On('activation-updated')]
    public function refreshActivation(): void
    {
        $this->loadActivationStatus();
    }

    private function loadActivationStatus(): void
    {
        $activation = Activation::where('ProcessedBy', Auth::id())
            ->latest()
            ->first();

        $this->activationStatus = $activation?->status;
    }

    public function render()
    {
        return view('livewire.member-pages.my-profile');
    }
}
