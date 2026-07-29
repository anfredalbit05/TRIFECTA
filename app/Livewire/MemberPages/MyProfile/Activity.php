<?php

namespace App\Livewire\MemberPages\MyProfile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Activity extends Component
{
    public function render()
    {
        return view(
            'livewire.member-pages.my-profile.activity',
            [
                'user' => Auth::user(),
            ]
        );
    }
}
