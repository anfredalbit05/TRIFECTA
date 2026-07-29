<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Models\User;
use App\Models\Branch; // Adjust model name if different
use App\Models\Benefit;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('components.admin-pages.masterlist.dashboard', [
            'totalMembers' => User::where('RoleID', '!=', 1)->count(),
            // 'activeBranches' => Branch::where('status', 'active')->count() ?? 12, // fallback or direct count
            'totalBenefits' => Benefit::where('status', true)->count(),
        ]);
    }
}
