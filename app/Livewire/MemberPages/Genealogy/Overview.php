<?php

namespace App\Livewire\MemberPages\Genealogy;

use App\Models\Activation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;

/**
 * Genealogy Overview (hidden from RoleID 4 Members).
 * Search any member, then view and print their direct (1st level) downlines.
 */
class Overview extends Component
{
    use HasTabSkeleton;

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


    /** Direct (1st level) downlines — Approved activations only.
     * Date filtering is based on the latest Approved activation history.
     */
    #[Computed]
    public function downlines(): array
    {
        $root = $this->rootMember;

        if (! $root || empty($root->referralCode)) {
            return [];
        }

        [$from, $to] = $this->range();

        return Activation::query()
            ->select(
                'activations.ActivationID',
                'activations.UserID',
                'activations.payment_method',
                'activations.payment_reference'
            )
            ->where('activations.upline_code', $root->referralCode)
            ->where('activations.status', 'Approved')

            // Get only activations whose latest history is Approved.
            ->whereHas('histories', function ($q) use ($from, $to) {
                $q->where('status', 'Approved')
                    ->whereRaw('activation_histories.HistoryID = (
                        SELECT ah.HistoryID
                        FROM activation_histories AS ah
                        WHERE ah.ActivationID = activation_histories.ActivationID
                        ORDER BY ah.created_at DESC, ah.HistoryID DESC
                        LIMIT 1
                    )')
                    ->when($from && $to, fn ($q) =>
                        $q->whereBetween('activation_histories.created_at', [$from, $to])
                    );
            })

            ->with('user:UserID,firstname,middlename,lastname,referralCode,status')
            ->get()
            ->filter(fn ($a) => $a->user && $a->UserID !== $root->UserID)
            ->unique('UserID')
            ->map(function ($a) {
                $latestApproval = $a->histories()
                    ->where('status', 'Approved')
                    ->orderByDesc('created_at')
                    ->orderByDesc('HistoryID')
                    ->first();

                return [
                    'id' => $a->UserID,
                    'name' => trim($a->user->firstname.' '.$a->user->middlename.' '.$a->user->lastname),
                    'referral_code' => $a->user->referralCode,

                    // Payment information
                    'payment_method' => $a->payment_method,
                    'payment_reference' => $a->payment_method === 'Cash'
                        ? '—'
                        : ($a->payment_reference ?: '—'),

                    'status' => $a->user->status,
                    'activated_at' => $latestApproval?->created_at?->format('M d, Y h:i A'),

                    'search' => mb_strtolower(trim(preg_replace(
                        '/\s+/',
                        ' ',
                        "{$a->user->firstname} {$a->user->middlename} {$a->user->lastname} {$a->user->referralCode} {$a->payment_method} {$a->payment_reference}"
                    ))),
                ];
            })
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
