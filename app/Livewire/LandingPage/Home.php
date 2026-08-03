<?php

namespace App\Livewire\LandingPage;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use App\Models\Context;
use App\Models\User;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.app')]
class Home extends Component
{
    public array $contexts = [];

    public ?string $referralCode = null;

    public function mount()
    {
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
