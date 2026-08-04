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

class OverviewAnalytics extends Component
{

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
        /*
        |--------------------------------------------------------------------------
        | MEMBER ANALYTICS
        |--------------------------------------------------------------------------
        */

        $this->totalMembers = User::count();
        $this->activeMembers = User::where('status', 1)->count();
        $this->inactiveMembers = User::where('status', 0)->count();

        /*
        |--------------------------------------------------------------------------
        | SYSTEM COUNTS
        |--------------------------------------------------------------------------
        */

        $this->totalBranches = Branch::count();
        $this->totalBenefits = Benefit::count();
        $this->totalBeneficiaries = Beneficiary::count();
        $this->totalNews = NewsUpdate::count();
        $this->totalFaq = Faq::count();
        $this->totalInquiry = Inquiry::count();

        /*
        |--------------------------------------------------------------------------
        | INQUIRIES
        |--------------------------------------------------------------------------
        */

        $this->unreadInquiry = Inquiry::whereNull('date_read')->count();
        $this->readInquiry = Inquiry::whereNotNull('date_read')->count();

        /*
        |--------------------------------------------------------------------------
        | RECENT USERS & NEWS
        |--------------------------------------------------------------------------
        */

        $this->recentMembers = User::latest()->limit(5)->get();
        $this->recentNews = NewsUpdate::latest()->limit(5)->get();

        /*
        |--------------------------------------------------------------------------
        | BRANCH DISTRIBUTION
        |--------------------------------------------------------------------------
        */

        $this->branchStats = Branch::withCount('user')->get();

        /*
        |--------------------------------------------------------------------------
        | BENEFIT ANALYTICS (Safely handled if relationship is missing)
        |--------------------------------------------------------------------------
        */

        try {
            $this->benefitStats = Benefit::withCount('beneficiaries')->get();
        } catch (\Throwable $e) {
            $this->benefitStats = Benefit::all();
        }

        /*
        |--------------------------------------------------------------------------
        | MONTHLY REGISTRATION
        |--------------------------------------------------------------------------
        */

        $this->monthlyUsers = User::select(
                DB::raw("DATE_FORMAT(created_at,'%b') as month"),
                DB::raw("COUNT(*) as total")
            )
            ->groupBy('month')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | TOP REFERRERS LEADERBOARD
        |--------------------------------------------------------------------------
        */

        $this->topReferrers = User::query()
            ->join('activations', 'users.referralCode', '=', 'activations.upline_code')
            ->select('users.firstname', 'users.lastname', 'users.referralCode', 'users.profilepic', 'users.address', DB::raw('count(activations.ActivationID) as total_invites'))
            ->where('activations.status', 'Approved')
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
