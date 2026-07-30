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
        return view('components.admin-pages.masterlist.dashboard', [
            'totalMembers' => User::where('RoleID', '!=', 1)->count(),
            'activeBranches' => Branch::where('status', true)->count(),
            'totalBenefits' => Benefit::where('status', true)->count(),
            'totalInquiries' => Inquiry::count(),
        ]);
    }
}
