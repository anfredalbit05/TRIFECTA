<?php

namespace App\Livewire\MemberPages\Genealogy;

use App\Models\Activation;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Genealogy Overview (hidden from RoleID 4 Members).
 * Search any member, then view and print their direct (1st level) downlines.
 */
class Overview extends Component
{
    public string $memberSearch = '';

    #[Locked]
    public ?int $rootUserId = null;

    public function selectMember(int $userId): void
    {
        abort_unless($this->canView, 403);

        $this->rootUserId = $userId;
        $this->memberSearch = '';
    }

    public function clearSearch(): void
    {
        $this->memberSearch = '';
    }

    #[Computed]
    public function canView(): bool
    {
        return (int) (Auth::user()?->RoleID ?? 0) !== 4;
    }

    #[Computed]
    public function searchResults(): Collection
    {
        $term = trim($this->memberSearch);

        if (mb_strlen($term) < 2 || ! $this->canView) {
            return collect();
        }

        return User::query()
            ->select('UserID', 'firstname', 'lastname', 'username', 'referralCode')
            ->where(fn ($q) => $q
                ->where('username', 'like', "{$term}%")
                ->orWhere('referralCode', 'like', "{$term}%")
                ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$term}%"]))
            ->orderBy('lastname')
            ->limit(12)
            ->get();
    }

    #[Computed]
    public function rootMember(): ?User
    {
        return $this->rootUserId
            ? User::select('UserID', 'firstname', 'lastname', 'referralCode', 'status')->find($this->rootUserId)
            : null;
    }

    /** Direct (1st level) downlines — Approved + Pending activations. */
    #[Computed]
    public function downlines(): array
    {
        $root = $this->rootMember;

        if (! $root || empty($root->referralCode)) {
            return [];
        }

        return Activation::query()
            ->where('upline_code', $root->referralCode)
            ->whereIn('status', ['Approved', 'Pending'])
            ->with('user:UserID,firstname,middlename,lastname,referralCode,status')
            ->get()
            ->pluck('user')
            ->filter(fn ($u) => $u && $u->UserID !== $root->UserID)
            ->unique('UserID')
            ->map(fn ($u) => [
                'id' => $u->UserID,
                'name' => trim($u->firstname.' '.$u->middlename.' '.$u->lastname),
                'referral_code' => $u->referralCode,
                'status' => $u->status,
                // lowercase haystack used by the client-side filter
                'search' => mb_strtolower(trim(preg_replace(
                    '/\s+/',
                    ' ',
                    "{$u->firstname} {$u->middlename} {$u->lastname} {$u->referralCode}"
                ))),
            ])
            ->values()
            ->all();
    }

    public function render()
    {
        return $this->canView
            ? view('livewire.member-pages.genealogy.overview')
            : '<div></div>';
    }
}
