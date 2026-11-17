<?php

namespace App\Livewire\MemberPages\MyProfile;

use App\Models\Activation;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;

class Certificate extends Component
{
    use HasTabSkeleton;

    public function render()
    {
        $user = Auth::user();

        $latestApprovedActivation = Activation::where('UserID', $user->UserID)
            ->where('status', 'Approved')
            ->whereNotNull('receipt_number')
            ->latest('ActivationID')
            ->first();

        return view(
            'livewire.member-pages.my-profile.certificate',
            [
                'user' => $user,
                'latestApprovedActivation' => $latestApprovedActivation,
            ]
        );
    }
}
