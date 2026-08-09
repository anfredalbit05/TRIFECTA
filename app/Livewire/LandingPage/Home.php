<?php

namespace App\Livewire\LandingPage;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use App\Models\Context;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

#[Layout('layouts.app')]
class Home extends Component
{
    public array $contexts = [];

    public ?string $referralCode = null;

    public bool $openLogin = false;

    // Restore $autoLogin = false here so Livewire receives it from the route defaults
    public function mount($autoLogin = false)
    {
        // 1. Handle /secret-login route logic
        if ($autoLogin) {
            // If already logged in as Super Admin, redirect to dashboard
            if (Auth::check() && Auth::user()->RoleID === 1) {
                return redirect()->to('/dashboard');
            } else {
                Log::info("Secret login visited by non-super admin or guest");
            }
        } else {
            // 2. Check Maintenance Mode logic (Only applies to normal routes, not secret-login)
            $maintenance = Setting::where('name', 'maintenance_mode')->value('value');
            $isMaintenanceActive = ($maintenance === '1' || $maintenance === 'true');

            if ($isMaintenanceActive) {
                if (Auth::check()) {
                    if (Auth::user()->RoleID !== 1) {
                        return redirect()->to('/dashboard');
                    }
                }
            }
        }

        // 3. Standard initializations
        $this->loadContexts();

        $this->referralCode = request()->query('ref');

        Log::info('Referral code detected', [
            'referralCode' => $this->referralCode,
            'url' => request()->fullUrl(),
        ]);
    }

    public function loadContexts()
    {
        $this->contexts = Context::pluck(
            'details',
            'context_key'
        )->toArray();
    }

    #[On('context-updated')]
    public function refreshContexts()
    {
        $this->loadContexts();
    }

    public function render()
    {
        $totalMembers = User::count();

        return view('livewire.landing-page.home', [
            'totalMembers' => $totalMembers,
            'referralCode' => $this->referralCode,
        ]);
    }
}
