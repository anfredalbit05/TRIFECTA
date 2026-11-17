<?php

namespace App\Livewire\MemberPages\MyProfile;

use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Activity extends Component
{
    use HasTabSkeleton;

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
