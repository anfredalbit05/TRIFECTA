<?php

namespace App\Livewire\AdminPages\TransactionList;

use App\Models\Activation;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;
use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;

class Overview extends Component
{
    use HasTabSkeleton;

    public function render()
    {
        return view('livewire.admin-pages.transaction-list.overview', [
            // Withdrawals
            'withdrawalCount' => Withdrawal::count(),
            'withdrawalApproved' => Withdrawal::where('status', 'Approved')->count(),
            'withdrawalTotal' => (float) Withdrawal::where('status', 'Approved')->sum('amount'),

            // Activations
            'activationCount' => Activation::count(),
            'activationApproved' => Activation::where('status', 'Approved')->count(),
            'activationPending' => Activation::where('status', 'Pending')->count(),
            'activationRejected' => Activation::where('status', 'Rejected')->count(),
            'activationTotal' => (float) Activation::where('status', 'Approved')->sum('amount'),

            // Monthly approved-activation totals for the last 6 months
            'monthlyActivations' => Activation::where('status', 'Approved')
                ->whereNotNull('created_at')
                ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->get([
                    DB::raw("DATE_FORMAT(created_at, '%b %Y') as month"),
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(amount) as amount'),
                ]),
        ]);
    }
}
