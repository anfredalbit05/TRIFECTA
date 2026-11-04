<?php

namespace App\Livewire\MemberPages\Genealogy;

use App\Models\Activation;
use App\Models\User;
use Carbon\Carbon;
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

    /** "Y-m-d to Y-m-d" (activation date range) */
    public string $dateRange = '';

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

    public function clearDateFilter(): void
    {
        $this->dateRange = '';
        $this->dispatch('ranges-reset');
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

    /** [Carbon|null $from, Carbon|null $to] parsed from $dateRange. */
    private function range(): array
    {
        if (! str_contains($this->dateRange, ' to ')) {
            return [null, null];
        }

        [$from, $to] = explode(' to ', $this->dateRange, 2);

        try {
            return [
                Carbon::parse(trim($from))->startOfDay(),
                Carbon::parse(trim($to))->endOfDay(),
            ];
        } catch (\Throwable) {
            return [null, null];
        }
    }

    /** Printable range label, e.g. "Aug 01, 2026 – Aug 31, 2026". */
    #[Computed]
    public function dateLabel(): string
    {
        [$from, $to] = $this->range();

        return $from && $to
            ? $from->format('M d, Y').' – '.$to->format('M d, Y')
            : '';
    }

    /** Direct (1st level) downlines — Approved activations only. */
    #[Computed]
    public function downlines(): array
    {
        $root = $this->rootMember;

        if (! $root || empty($root->referralCode)) {
            return [];
        }

        [$from, $to] = $this->range();

        return Activation::query()
            ->select('ActivationID', 'UserID', 'created_at')
            ->where('upline_code', $root->referralCode)
            ->where('status', 'Approved')
            ->when($from && $to, fn ($q) => $q->whereBetween('created_at', [$from, $to]))
            ->orderByRaw('created_at IS NULL, created_at') // dated first, null dates last
            ->with('user:UserID,firstname,middlename,lastname,referralCode,status')
            ->get()
            ->filter(fn ($a) => $a->user && $a->UserID !== $root->UserID)
            ->unique('UserID')
            ->map(fn ($a) => [
                'id' => $a->UserID,
                'name' => trim($a->user->firstname.' '.$a->user->middlename.' '.$a->user->lastname),
                'referral_code' => $a->user->referralCode,
                'status' => $a->user->status,
                'activated_at' => $a->created_at?->format('M d, Y h:i A'),
                // lowercase haystack used by the client-side filter
                'search' => mb_strtolower(trim(preg_replace(
                    '/\s+/',
                    ' ',
                    "{$a->user->firstname} {$a->user->middlename} {$a->user->lastname} {$a->user->referralCode}"
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
