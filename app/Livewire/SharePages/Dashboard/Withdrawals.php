<?php

namespace App\Livewire\SharePages\Dashboard;

use App\Models\Withdrawal;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Withdrawals extends Component
{
    // ==========================================
    // REFRESH
    // ==========================================

    public function refreshDashboard(): void
    {
        session()->flash(
            'success',
            'Withdrawal data refreshed successfully.'
        );
    }

    // ==========================================
    // RENDER
    // ==========================================

    public function render()
    {
        $user = Auth::user();

        if (!$user) {
            return view(
                'livewire.share-pages.dashboard.withdrawals',
                [
                    'withdrawals' => collect(),
                    'totalWithdrawals' => 0,
                ]
            );
        }

        // ==========================================
        // USER WITHDRAWALS
        // ==========================================

        $withdrawals = Withdrawal::where(
                'UserID',
                $user->UserID
            )
            ->latest()
            ->get();

        // ==========================================
        // TOTAL APPROVED WITHDRAWALS
        // ==========================================

        $totalWithdrawals = Withdrawal::where(
                'UserID',
                $user->UserID
            )
            ->where('status', 'Approved')
            ->sum('requested_amount');

        // ==========================================
        // RETURN VIEW
        // ==========================================

        return view(
            'livewire.share-pages.dashboard.withdrawals',
            [
                'withdrawals' => $withdrawals,

                'totalWithdrawals' => $totalWithdrawals,
            ]
        );
    }
}
