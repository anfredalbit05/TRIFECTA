<?php

namespace App\Livewire\AdminPages\Dashboard;

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
    public $selectedYear;
    public $availableYears = [];

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

    public function mount()
    {
        // Generate years from 2020 up to the current year (2026)
        $currentYear = Carbon::now()->year;
        $this->availableYears = range(2020, $currentYear);
        rsort($this->availableYears); // Sort descending (2026, 2025, ... 2020)

        // Default to the current year
        $this->selectedYear = $currentYear;
        $this->loadData();
    }

    public function updatedSelectedYear()
    {
        $this->loadData();
    }

    public function refreshAnalytics()
    {
        $this->loadData();
        $label = $this->selectedYear === '2020-Present' ? '2020 to Present' : $this->selectedYear;
        session()->flash('message', 'Analytics data refreshed successfully for ' . $label . '.');
    }

    public function loadData()
    {
        $year = $this->selectedYear;

        // Reusable query filter handler for single year or 2020-Present range
        $applyFilter = function($query, $column = 'created_at') use ($year) {
            if ($year === '2020-Present') {
                $query->whereYear($column, '>=', 2020);
            } else {
                $query->whereYear($column, $year);
            }
        };

        /*
        |--------------------------------------------------------------------------
        | MEMBER ANALYTICS
        |--------------------------------------------------------------------------
        */
        $this->totalMembers = User::where($applyFilter)->count();
        $this->activeMembers = User::where($applyFilter)->where('status', 1)->count();
        $this->inactiveMembers = User::where($applyFilter)->where('status', 0)->count();

        /*
        |--------------------------------------------------------------------------
        | SYSTEM COUNTS
        |--------------------------------------------------------------------------
        */
        $this->totalBranches = Branch::where($applyFilter)->count();
        $this->totalBenefits = Benefit::where($applyFilter)->count();
        $this->totalBeneficiaries = Beneficiary::where($applyFilter)->count();
        $this->totalNews = NewsUpdate::where($applyFilter)->count();
        $this->totalFaq = Faq::where($applyFilter)->count();
        $this->totalInquiry = Inquiry::where($applyFilter)->count();

        /*
        |--------------------------------------------------------------------------
        | INQUIRIES
        |--------------------------------------------------------------------------
        */
        $this->unreadInquiry = Inquiry::where($applyFilter)->whereNull('date_read')->count();
        $this->readInquiry = Inquiry::where($applyFilter)->whereNotNull('date_read')->count();

        /*
        |--------------------------------------------------------------------------
        | RECENT USERS & NEWS
        |--------------------------------------------------------------------------
        */
        $this->recentMembers = User::where($applyFilter)->latest()->limit(5)->get();
        $this->recentNews = NewsUpdate::where($applyFilter)->latest()->limit(5)->get();

        /*
        |--------------------------------------------------------------------------
        | BRANCH DISTRIBUTION
        |--------------------------------------------------------------------------
        */
        $this->branchStats = Branch::withCount(['user' => function ($query) use ($applyFilter) {
            $applyFilter($query);
        }])->get();

        /*
        |--------------------------------------------------------------------------
        | BENEFIT ANALYTICS
        |--------------------------------------------------------------------------
        */
        try {
            $this->benefitStats = Benefit::withCount(['beneficiaries' => function ($query) use ($applyFilter) {
                $applyFilter($query);
            }])->get();
        } catch (\Throwable $e) {
            $this->benefitStats = Benefit::all();
        }

        /*
        |--------------------------------------------------------------------------
        | MONTHLY REGISTRATION
        |--------------------------------------------------------------------------
        */
        $monthlyQuery = User::select(
                DB::raw("DATE_FORMAT(created_at,'%b') as month"),
                DB::raw("MONTH(created_at) as month_num"),
                DB::raw("COUNT(*) as total")
            );

        $applyFilter($monthlyQuery);

        $this->monthlyUsers = $monthlyQuery
            ->groupBy('month', 'month_num')
            ->orderBy('month_num')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | TOP REFERRERS LEADERBOARD
        |--------------------------------------------------------------------------
        */
        $referrerQuery = User::query()
            ->join('activations', 'users.referralCode', '=', 'activations.upline_code')
            ->select('users.firstname', 'users.lastname', 'users.referralCode', 'users.profilepic', 'users.address', DB::raw('count(activations.ActivationID) as total_invites'))
            ->where('activations.status', 'Approved');

        $applyFilter($referrerQuery, 'activations.created_at');

        $this->topReferrers = $referrerQuery
            ->groupBy('users.UserID', 'users.firstname', 'users.lastname', 'users.referralCode', 'users.profilepic', 'users.address')
            ->orderByDesc('total_invites')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin-pages.dashboard.overview');
    }
}
