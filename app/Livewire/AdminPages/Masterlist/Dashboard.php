<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Models\User;
use App\Models\Branch;
use App\Models\Benefit;
use App\Models\Inquiry;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $mapUsers = User::where('RoleID', 4)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get([
                'FirstName',
                'LastName',
                'address',
                'longitude',
                'latitude',
            ]);

        return view('components.admin-pages.masterlist.dashboard', [
            'totalMembers' => User::where('RoleID', '!=', 1)->count(),
            'activeBranches' => Branch::where('status', true)->count(),
            'totalBenefits' => Benefit::where('status', true)->count(),
            'totalInquiries' => Inquiry::count(),
            'mapUsers' => $mapUsers,
        ]);
    }
}
