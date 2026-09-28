<?php

namespace App\Livewire\MemberPages\Satellite;

use App\Models\Activation;
use App\Models\Satellite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Member-facing Satellite page.
 *
 * REUSES the existing admin SatelliteMembers stack (SatelliteMembers,
 * SatelliteMembersEdit, SatelliteMembersView, SatelliteMembersActivationReview)
 * rather than introducing a parallel portal. This component only supplies:
 *
 *  - the member's OWN satellite rows (scoped to Auth::id()),
 *  - the downline satellites (RoleID 8 users whose application used this
 *    member's referralCode as the upline code),
 *  - the tab switch, and the events that open the shared Edit/View/Review
 *    modals for rows this member is allowed to act on.
 */
class SatelliteMembersSelf extends Component
{
    public string $activeTab = 'overview';

    public function mount(): void
    {
        $this->activeTab = 'overview';
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['overview', 'downlines'], true)) {
            $this->activeTab = $tab;
        }
    }

    /**
     * The satellite records belonging to the signed-in member's account
     * (same shape the admin table expects: user + latest activation status).
     */
    public function getMySatellitesProperty()
    {
        $userId = Auth::id();

        return Satellite::query()
            ->where('UserID', $userId)
            ->latest('satelliteID')
            ->get()
            ->map(function (Satellite $satellite) {
                $activation = Activation::where('satelliteID', $satellite->satelliteID)
                    ->latest('ActivationID')
                    ->first();

                $satellite->tier = config('satellite.tier_by_type')[(int) $satellite->satellite_typeID]
                    ?? $this->tierName((int) $satellite->satellite_typeID);
                $satellite->activation_status = $activation?->status ?? '—';
                $satellite->amount = (float) ($activation?->amount ?? 0);
                $satellite->applied_at = $activation?->created_at;
                $satellite->activation_id = $activation?->ActivationID;

                return $satellite;
            });
    }

    /**
     * Every satellite user (RoleID 8) whose application used THIS member's
     * referralCode as the upline code.
     */
    public function getDownlineSatellitesProperty()
    {
        $referralCode = (string) (Auth::user()?->referralCode ?? '');

        if ($referralCode === '') {
            return collect();
        }

        return Activation::query()
            ->where('upline_code', $referralCode)
            ->whereHas('user', fn ($q) => $q->where('RoleID', 8))
            ->with('user')
            ->latest('ActivationID')
            ->get()
            ->map(function (Activation $activation) {
                $satellite = Satellite::where('UserID', $activation->UserID)
                    ->latest('satelliteID')
                    ->first();

                return (object) [
                    'activation_id' => $activation->ActivationID,
                    'user_id' => $activation->UserID,
                    'name' => trim(($activation->user?->firstname ?? '').' '.($activation->user?->lastname ?? '')) ?: '—',
                    'username' => $activation->user?->username ?? '—',
                    'satellite_name' => $satellite?->satellite_name ?? '—',
                    'tier' => $this->tierName((int) ($satellite?->satellite_typeID ?? 0)),
                    'status' => $activation->status,
                    'amount' => (float) ($activation->amount ?? 0),
                    'applied_at' => $activation->created_at,
                ];
            });
    }

    public function getHasDownlineSatellitesProperty(): bool
    {
        return $this->downlineSatellites->isNotEmpty();
    }

    private function tierName(int $typeId): string
    {
        return match ($typeId) {
            6 => 'Bronze',
            7 => 'Silver',
            8 => 'Gold',
            default => '—',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Refresh after shared-modal actions (approve / reject) — the member
    | page re-renders its tabs, counts and status pills automatically.
    |--------------------------------------------------------------------------
    */

    #[On('members-refresh')]
    public function refreshSelf(): void {}

    /*
    |--------------------------------------------------------------------------
    | Open the SHARED admin modals for rows this member owns.
    | The View/Review components listen for these same events the admin
    | page dispatches — no duplicate components needed.
    |--------------------------------------------------------------------------
    */

    #[On('self-view-satellite')]
    public function viewSatellite(int $userId): void
    {
        $this->dispatch('open-satellite-member-view', UserID: $userId);
    }

    #[On('self-review-satellite')]
    public function reviewSatellite(int $userId): void
    {
        $this->dispatch('open-satellite-activation-review', UserID: $userId);
    }

    /*
    |--------------------------------------------------------------------------
    | Apply for a NEW satellite membership
    |--------------------------------------------------------------------------
    | Reuses the SAME SatelliteModal wizard the admin Members page uses
    | (open-satellite-modal). It is pre-filled from the signed-in member's
    | account, validates every step, and BLOCKS using the member's own
    | referralCode as the upline code — identical rules to the admin flow.
    |--------------------------------------------------------------------------
    */
    #[On('self-apply-satellite')]
    public function applySatellite(): void
    {
        $this->dispatch('open-satellite-modal', UserID: Auth::id());
    }

    public function render()
    {
        return view('livewire.member-pages.satellite.satellite-members-self');
    }
}
