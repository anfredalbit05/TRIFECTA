<?php

namespace App\Livewire\AdminPages\Dashboard;

use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;
use App\Models\User;
use App\Models\Branch;
use App\Models\Benefit;
use App\Models\Beneficiary;
use App\Models\Inquiry;
use App\Models\NewsUpdate;
use App\Models\Faq;
use App\Models\Activation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OverviewAnalytics extends Component
{
    use HasTabSkeleton;

    public $selectedYear;
    public array $availableYears = [];

    public $totalMembers;
    public $activeMembers;
    public $inactiveMembers;

    public $totalBranches;
    public $totalBenefits;
    public $totalBeneficiaries;

    public $totalNews;
    public $totalFaq;
    public $totalInquiry;

    public $unreadInquiry;
    public $readInquiry;

    public $recentMembers;
    public $recentNews;

    public $branchStats;
    public $benefitStats;
    public $monthlyUsers;
    public $topReferrers;

    public $showInvitesModal = false;
    public $selectedReferrerName = '';
    public $referredUsers; // Changed from array hint to support collection

    public function mount()
    {
        $currentYear = Carbon::now()->year;
        $this->availableYears = range(2020, $currentYear);
        rsort($this->availableYears);

        $this->selectedYear = $currentYear;
        $this->loadData();
    }

    public function updatedSelectedYear()
    {
        $this->loadData();
        $this->dispatchChartData();
    }

    public function refreshAnalytics()
    {
        $this->loadData();
        $this->dispatchChartData();

        $label = $this->selectedYear === '2020-Present' ? '2020 to Present' : $this->selectedYear;
        // $this->dispatch('swal:success', [
        //     'title' => 'Refreshed!',
        //     'text'  => 'Analytics data refreshed successfully for ' . $label . '.'
        // ]);
    }

    private function getMonthlyUserData($year)
    {
        $monthlyQuery = User::select(
            DB::raw("MONTH(created_at) as month_num"),
            DB::raw("COUNT(*) as total")
        );

        if ($year === '2020-Present') {
            $monthlyQuery->whereYear('created_at', '>=', 2020);
        } else {
            $monthlyQuery->whereYear('created_at', $year);
        }

        $rawMonthlyUsers = $monthlyQuery
            ->groupBy('month_num')
            ->pluck('total', 'month_num');

        return collect(range(1, 12))->map(function ($monthNum) use ($rawMonthlyUsers) {
            return [
                'month' => Carbon::create()->month($monthNum)->format('M'),
                'total' => (int) ($rawMonthlyUsers->get($monthNum, 0)),
            ];
        });
    }

    private function dispatchChartData()
    {
        $this->dispatch(
            'chart-data-updated',
            labels: $this->monthlyUsers->pluck('month'),
            data: $this->monthlyUsers->pluck('total')
        );
    }

    public function loadData()
    {
        $year = $this->selectedYear;

        $applyFilter = function ($query, $column = 'created_at') use ($year) {
            if ($year === '2020-Present') {
                $query->whereYear($column, '>=', 2020);
            } else {
                $query->whereYear($column, $year);
            }
        };

        // Member Analytics
        $this->totalMembers = User::where(fn($q) => $applyFilter($q))->where('RoleID', 8)->count();
        $this->activeMembers = User::where(fn($q) => $applyFilter($q))->where('status', 1)->where('RoleID', 8)->count();
        $this->inactiveMembers = User::where(fn($q) => $applyFilter($q))->where('status', 0)->where('RoleID', 8)->count();

        // System Counts
        $this->totalBranches = Branch::where(fn($q) => $applyFilter($q))->count();
        $this->totalBenefits = Benefit::where(fn($q) => $applyFilter($q))->count();
        $this->totalBeneficiaries = Beneficiary::where(fn($q) => $applyFilter($q))->count();
        $this->totalNews = NewsUpdate::where(fn($q) => $applyFilter($q))->count();
        $this->totalFaq = Faq::where(fn($q) => $applyFilter($q))->count();
        $this->totalInquiry = Inquiry::where(fn($q) => $applyFilter($q))->count();

        // Inquiries
        $this->unreadInquiry = Inquiry::where(fn($q) => $applyFilter($q))->whereNull('date_read')->count();
        $this->readInquiry = Inquiry::where(fn($q) => $applyFilter($q))->whereNotNull('date_read')->count();

        // Recent Users & News
        $this->recentMembers = User::where(fn($q) => $applyFilter($q))->latest()->limit(5)->get();
        $this->recentNews = NewsUpdate::where(fn($q) => $applyFilter($q))->latest()->limit(5)->get();

        // Branch Distribution
        $this->branchStats = Branch::withCount(['user' => function ($query) use ($applyFilter) {
            $applyFilter($query);
        }])->get();

        // Benefit Analytics
        try {
            $this->benefitStats = Benefit::withCount(['beneficiaries' => function ($query) use ($applyFilter) {
                $applyFilter($query);
            }])->get();
        } catch (\Throwable $e) {
            $this->benefitStats = Benefit::all();
        }

        // Monthly Registrations (Guarantees all 12 months)
        $this->monthlyUsers = $this->getMonthlyUserData($year);

        // Top Referrers Leaderboard
        $referrerQuery = User::query()
            ->join('activations', 'users.referralCode', '=', 'activations.upline_code')
            ->select(
                'users.UserID',
                'users.firstname',
                'users.lastname',
                'users.referralCode',
                'users.profilepic',
                'users.address',
                DB::raw('count(activations.ActivationID) as total_invites')
            )
            ->where('activations.status', 'Approved');

        $applyFilter($referrerQuery, 'activations.created_at');

        $this->topReferrers = $referrerQuery
            ->groupBy('users.UserID', 'users.firstname', 'users.lastname', 'users.referralCode', 'users.profilepic', 'users.address')
            ->orderByDesc('total_invites')
            ->limit(5)
            ->get();
    }

    public function viewInvites($userId)
    {
        $year = $this->selectedYear;

        $referrer = User::where('UserID', $userId)->first();
        if (!$referrer) {
            return;
        }

        $this->selectedReferrerName = $referrer->firstname . ' ' . $referrer->lastname;

        // FIXED: Join users table using activations.user_id instead of referralCode
        $query = User::query()
            ->join('activations', 'users.UserID', '=', 'activations.UserID')
            ->where('activations.upline_code', $referrer->referralCode)
            ->where('activations.status', 'Approved')
            ->select('users.*', 'activations.created_at as activation_date');

        if ($year === '2020-Present') {
            $query->whereYear('activations.created_at', '>=', 2020);
        } else {
            $query->whereYear('activations.created_at', $year);
        }

        $this->referredUsers = $query->get();
        $this->showInvitesModal = true;
    }

    public function closeInvitesModal()
    {
        $this->showInvitesModal = false;
        $this->referredUsers = collect();
        $this->selectedReferrerName = '';
    }

    public function render()
    {
        return view('livewire.admin-pages.dashboard.overview');
    }
}
