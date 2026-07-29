<?php

namespace App\Livewire\MemberPages\MyProfile;

use App\Models\Activation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyProfile extends Component
{
    public bool $hasActivation = false;

    public function mount(): void
    {
        $currentUserId = Auth::id();

        $this->hasActivation = Activation::where('ProcessedBy', $currentUserId)
            ->whereIn('status', [
                'Pending',
                'Approved',
            ])
            ->exists();


        // Send data to browser console
        $this->dispatch('activation-check', [
            'current_user_id' => $currentUserId,
            'has_activation' => $this->hasActivation,
        ]);
    }


    public function render()
    {
        return view('pages.member-pages.my-profile', [
            'hasActivation' => $this->hasActivation
        ]);
    }
}
