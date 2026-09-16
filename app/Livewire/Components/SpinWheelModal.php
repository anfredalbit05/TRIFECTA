<?php

namespace App\Livewire\Components;

use App\Models\SpinDraw;
use App\Models\SpinWinner;
use App\Services\SpinWheelAnnouncer;
use App\Services\SpinWheelService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Throwable;

/**
 * Pangkabuhayan "Spin the Wheel" raffle.
 *
 * Everyone signed in may open the modal and watch the wheel; only
 * administrators (RoleID 1 / 2) can spin, change the prize or undo a spin.
 */
class SpinWheelModal extends Component
{
    /** Admin roles allowed to spin. */
    private const ADMIN_ROLES = [1, 2, 5, 6, 7];

    public bool $open = false;

    public bool $canSpin = false;

    /** Y-m-d of the cycle currently displayed. */
    public ?string $drawDate = null;

    /** @var array<int, array<string, mixed>> */
    public array $cycles = [];

    /** @var array<string, mixed> */
    public array $summary = [];

    /** @var array<int, array<string, mixed>> */
    public array $wheel = [];

    /** @var array<int, array<string, mixed>> */
    public array $winners = [];

    /** Index / size of the wheel the last spin must land on. */
    public ?int $spinIndex = null;

    public ?int $spinTotal = null;

    public ?array $lastWin = null;

    /** Rice kilos per prize — the prize value is kilos × ₱/kilo. */
    public string $prizeKilos = '5';

    /** Current market price per kilo of rice. */
    public string $prizePerKilo = '55.00';

    public bool $allowRepeat = true;

    public bool $membersOnly = true;

    public string $claimInstructions = '';

    public ?string $errorMessage = null;

    // ==========================================
    // LIFECYCLE
    // ==========================================

    #[On('open-spin-wheel')]
    public function openModal(): void
    {
        $this->resetErrorBag();

        $this->errorMessage = null;
        $this->lastWin = null;
        $this->spinIndex = null;
        $this->spinTotal = null;

        $this->drawDate = $this->service()
            ->activeDrawDate()
            ->toDateString();

        $this->load();

        $this->open = true;

        // Tell Alpine to reveal the dialog (matches the
        // withdrawal modal's server → browser event pattern).
        $this->dispatch('show-spin-wheel');
    }

    public function closeModal(): void
    {
        $this->open = false;
        $this->lastWin = null;
        $this->errorMessage = null;
    }

    // ==========================================
    // CYCLE SELECTION
    // ==========================================

    public function selectCycle(string $date): void
    {
        $allowed = collect($this->service()->recentDrawDates())
            ->map(fn (CarbonInterface $d) => $d->toDateString());

        if (! $allowed->contains($date)) {
            return;
        }

        $this->drawDate = $date;
        $this->lastWin = null;
        $this->spinIndex = null;
        $this->spinTotal = null;
        $this->errorMessage = null;

        $this->load();
    }

    /**
     * Rebuild the wheel after the animation has finished (so the segments
     * never shift underneath a spin that is still playing).
     */
    public function refreshWheel(): void
    {
        $this->lastWin = null;
        $this->spinIndex = null;
        $this->spinTotal = null;

        $this->load();
    }

    // ==========================================
    // SPIN
    // ==========================================

    public function spin(): void
    {
        $admin = Auth::user();

        if (! $this->userIsAdmin()) {
            $this->errorMessage = 'Only administrators can spin the wheel.';

            return;
        }

        $this->errorMessage = null;

        try {
            $result = $this->service()->spin($this->currentDraw(), $admin?->UserID);
        } catch (Throwable $e) {
            Log::warning('Spin the Wheel: spin failed.', [
                'draw_date' => $this->drawDate,
                'admin_id' => $admin?->UserID,
                'error' => $e->getMessage(),
            ]);

            $this->errorMessage = $e->getMessage();
            $this->load();

            return;
        }

        // Notify the winner and refresh the news feed announcement. Both are
        // best effort — the announcer swallows its own failures — so the spin
        // itself is already safely recorded either way.
        $notification = $this->announce($result['winner']['WinnerID']);

        $this->spinIndex = $result['index'];
        $this->spinTotal = $result['total'];
        $this->lastWin = array_merge($result['winner'], [
            'emailed' => $notification['emailed'],
            'email' => $notification['email'],
            'notice' => $notification['message'],
        ]);

        // The wheel deliberately is NOT rebuilt here: the browser needs the
        // exact segment list the index refers to until the animation ends.
        $this->dispatch('spin-wheel-result', $result);
    }

    /**
     * Email a winner and refresh this cycle's announcement post.
     *
     * @return array{emailed: bool, email: ?string, message: string}
     */
    private function announce(int $winnerID): array
    {
        $draw = $this->currentDraw();

        $winner = SpinWinner::with('user')
            ->where('DrawID', $draw->DrawID)
            ->where('WinnerID', $winnerID)
            ->first();

        if (! $winner) {
            return [
                'emailed' => false,
                'email' => null,
                'message' => 'The winner could not be reloaded for notification.',
            ];
        }

        $announcer = app(SpinWheelAnnouncer::class);

        $result = $announcer->notifyWinner($draw, $winner);
        $announcer->announce($draw, Auth::user());

        return $result;
    }

    /**
     * Re-send a winner's email and re-sync the announcement — the safety net
     * when a notification failed or the member gets a new address.
     */
    public function resendWinnerEmail(int $winnerID): void
    {
        if (! $this->userIsAdmin()) {
            return;
        }

        $result = $this->announce($winnerID);

        $this->load();

        $this->dispatch('swal:success', [
            'title' => $result['emailed'] ? '✅ Email sent' : '⚠️ Email not sent',
            'text' => $result['message'],
        ]);
    }

    /**
     * Called by the browser once the wheel has landed, so the announcement
     * shows up exactly when the pointer stops.
     */
    public function announceWinner(): void
    {
        if (! $this->lastWin) {
            return;
        }

        $this->dispatch('swal:success', [
            'title' => '🎉 '.$this->lastWin['name'].' wins!',
            'text' => $this->lastWin['prize_name']
                .' worth ₱'.number_format((float) $this->lastWin['prize_amount'], 2)
                .' · spin #'.$this->lastWin['spin_order']
                .' · invited '.$this->lastWin['invites']
                .' member'.((int) $this->lastWin['invites'] === 1 ? '' : 's')
                .'\n'.$this->lastWin['notice'],
        ]);
    }

    public function undoLast(): void
    {
        if (! $this->userIsAdmin()) {
            return;
        }

        $this->errorMessage = null;

        $draw = $this->currentDraw();

        try {
            $undone = $this->service()->undoLastWinner($draw);
        } catch (Throwable $e) {
            // e.g. the prize was already tagged as claimed — the expense must
            // not vanish from the books.
            $this->errorMessage = $e->getMessage();
            $this->load();

            return;
        }

        if (! $undone) {
            $this->errorMessage = 'There is no spin to undo for this cycle.';

            return;
        }

        $removedPrize = (float) ($this->lastWin['prize_amount'] ?? $draw->prize_amount);

        // Keep the announcement in step with the wheel.
        app(SpinWheelAnnouncer::class)->announce($draw->fresh(), Auth::user());

        $this->lastWin = null;
        $this->load();

        $this->dispatch('swal:success', [
            'title' => 'Spin removed',
            'text' => 'The most recent winner and their ₱'.number_format($removedPrize, 2)
                .' Pangkabuhayan expense were removed from this cycle, and the announcement was updated.'
                .' An email that was already sent cannot be recalled.',
        ]);
    }

    // ==========================================
    // CLAIMING (admins)
    // ==========================================

    /**
     * Tag a prize as claimed: its Pangkabuhayan expense ledger row
     * (Source PK) moves from Pending to Completed.
     */
    public function markClaimed(int $winnerID): void
    {
        if (! $this->userIsAdmin()) {
            return;
        }

        $result = $this->service()->markClaimed($this->currentDraw(), $winnerID);

        $this->errorMessage = null;
        $this->load();

        $this->dispatch('swal:success', [
            'title' => $result['ok'] ? '✅ Prize tagged as claimed' : '⚠️ Nothing changed',
            'text' => $result['message'],
        ]);
    }

    /**
     * Tag every still-pending prize of the displayed cycle as claimed.
     */
    public function markAllClaimed(): void
    {
        if (! $this->userIsAdmin()) {
            return;
        }

        $result = $this->service()->markAllClaimed($this->currentDraw());

        $this->errorMessage = null;
        $this->load();

        $this->dispatch('swal:success', [
            'title' => $result['ok'] ? '✅ '.(int) $result['claimed'].' prize(s) claimed' : '⚠️ Nothing to update',
            'text' => $result['message'],
        ]);
    }

    // ==========================================
    // PRIZE / RULES (admins, before the first spin)
    // ==========================================

    /** Live prize preview computed from the two dynamic inputs (blade calls $this->derivedPrizeName). */
    public function derivedPrizeName(): string
    {
        return SpinWheelService::formatPrizeName((float) $this->prizeKilos);
    }

    /** Cleaned kilos for the preview. */
    public function derivedPrizeKilos(): string
    {
        return rtrim(rtrim(number_format(max(0, (float) $this->prizeKilos), 2, '.', ''), '0'), '.');
    }

    /** ₱/kilo for the preview. */
    public function derivedPerKilo(): float
    {
        return round(max(0, (float) $this->prizePerKilo), 2);
    }

    /** Kilos × ₱/kilo, rounded to centavos. */
    public function derivedAmount(): float
    {
        return round((float) $this->derivedPrizeKilos() * $this->derivedPerKilo(), 2);
    }

    /** Winners the current pool would elect at the previewed prize. */
    public function derivedTarget(): int
    {
        $pool = (float) ($this->summary['pangkabuhayan_total'] ?? 0);
        $amount = $this->derivedAmount();

        return $amount > 0
            ? (int) floor($pool / $amount)
            : 0;
    }

    public function savePrize(): void
    {
        if (! $this->userIsAdmin()) {
            return;
        }

        if ($this->summary['has_winners'] ?? false) {
            $this->errorMessage = 'The prize can no longer be changed once the cycle has started spinning.';

            return;
        }

        $this->validate([
            'prizeKilos' => ['required', 'numeric', 'min:0.25', 'max:1000'],
            'prizePerKilo' => ['required', 'numeric', 'min:1', 'max:100000'],
        ], [
            'prizeKilos.required' => 'Please enter how many kilos of rice one prize is.',
            'prizeKilos.numeric' => 'The kilos must be a number.',
            'prizeKilos.min' => 'The kilos must be at least 0.25.',
            'prizePerKilo.required' => 'Please enter the current price per kilo of rice.',
            'prizePerKilo.numeric' => 'The price per kilo must be a number.',
            'prizePerKilo.min' => 'The price per kilo must be at least ₱1.',
        ]);

        $this->service()->savePrizeConfig(
            (float) $this->prizeKilos,
            (float) $this->prizePerKilo
        );

        $this->errorMessage = null;
        $this->load();

        $this->dispatch('swal:success', [
            'title' => 'Prize updated',
            'text' => $this->summary['prize_name'].' at ₱'
                .number_format($this->summary['prize_amount'], 2)
                .' — winners recalculated from ₱'
                .number_format($this->summary['pangkabuhayan_total'], 2)
                .' ÷ ₱'.number_format($this->summary['prize_amount'], 2)
                .' = '.$this->summary['winners_target'].' winners.',
        ]);
    }

    public function setRepeatMode(bool $allowRepeat): void
    {
        if (! $this->userIsAdmin()) {
            return;
        }

        $draw = $this->currentDraw();

        if (SpinWinner::where('DrawID', $draw->DrawID)->exists()) {
            $this->errorMessage = 'Repeat winners can only be changed before the first spin.';

            return;
        }

        $draw->forceFill(['allow_repeat_winners' => $allowRepeat])->save();

        $this->errorMessage = null;
        $this->load();
    }

    /**
     * Whether staff / agency / executive accounts may appear on the wheel.
     * Only changeable before the cycle has started spinning.
     */
    public function setMembersOnly(bool $membersOnly): void
    {
        if (! $this->userIsAdmin()) {
            return;
        }

        $draw = $this->currentDraw();

        if (SpinWinner::where('DrawID', $draw->DrawID)->exists()) {
            $this->errorMessage = 'Who may join the wheel can only be changed before the first spin.';

            return;
        }

        $this->service()->saveMembersOnly($membersOnly);

        $this->errorMessage = null;
        $this->load();
    }

    /**
     * "How to claim" text used in the winner email and the announcement.
     * Editable at any time — it does not affect the draw itself.
     */
    public function saveClaimInstructions(): void
    {
        if (! $this->userIsAdmin()) {
            return;
        }

        $this->validate([
            'claimInstructions' => ['required', 'string', 'max:1000'],
        ], [
            'claimInstructions.required' => 'Please describe how winners can claim their prize.',
            'claimInstructions.max' => 'The claim instructions are too long (1000 characters max).',
        ]);

        $this->service()->saveClaimInstructions($this->claimInstructions);

        $this->errorMessage = null;
        $this->load();

        $this->dispatch('swal:success', [
            'title' => 'Claim instructions saved',
            'text' => 'New winner emails and the announcement will use this text.',
        ]);
    }

    // ==========================================
    // DATA
    // ==========================================

    private function load(): void
    {
        $service = $this->service();

        $draw = $service->ensureDraw(
            $this->drawDate ? Carbon::parse($this->drawDate) : $service->activeDrawDate()
        );

        $this->drawDate = $draw->draw_date->toDateString();

        $summary = $service->summary($draw);

        $this->summary = [
            'draw_label' => $summary['draw_date']->format('F j, Y'),
            'period_label' => $summary['period_start']->format('M j')
                .' – '.$summary['period_end']->format('M j, Y'),
            'pangkabuhayan_total' => $summary['pangkabuhayan_total'],
            'prize_name' => $summary['prize_name'],
            'prize_amount' => $summary['prize_amount'],
            'winners_target' => $summary['winners_target'],
            'winners_count' => $summary['winners_count'],
            'remaining_slots' => $summary['remaining_slots'],
            'eligible_count' => $summary['eligible_count'],
            'allow_repeat_winners' => $summary['allow_repeat_winners'],
            'status' => $summary['status'],
            'next_draw_label' => $summary['next_draw_date']->format('F j, Y'),
            'next_period_label' => $summary['next_period'][0]->format('M j')
                .' – '.$summary['next_period'][1]->format('M j, Y'),
            'has_winners' => $summary['has_winners'],
            'claimed_count' => $summary['claimed_count'],
            'pending_count' => $summary['pending_count'],
            'claimed_cost' => $summary['claim_costs']['claimed'],
            'pending_cost' => $summary['claim_costs']['pending'],
            'members_only' => $service->membersOnly(),
        ];

        $this->wheel = $service->wheelMembers($draw)
            ->map(fn ($row) => [
                'uid' => $row['user']->UserID,
                'name' => $service->displayName($row['user']),
                'short' => $service->firstName($row['user']),
                'invites' => $row['invites'],
            ])
            ->values()
            ->all();

        $this->winners = $service->winnersOf($draw)
            ->map(fn (SpinWinner $winner) => [
                'WinnerID' => $winner->WinnerID,
                'name' => $service->displayName($winner->user),
                'invites' => (int) $winner->qualified_invites,
                'prize_name' => (string) $winner->prize_name,
                'prize_amount' => (float) $winner->prize_amount,
                'spin_order' => (int) $winner->spin_order,
                'drawn_at' => optional($winner->created_at)->format('M j, Y g:i A'),
                'ledger_id' => $winner->ledger?->TransactionID,
                'claim_status' => (string) ($winner->ledger->status ?? 'Pending'),
                'claimed_at' => $winner->ledger?->status === 'Completed'
                    ? optional($winner->ledger->updated_at)->format('M j, Y g:i A')
                    : null,
            ])
            ->values()
            ->all();

        $prize = $service->prizeConfig();
        $this->prizeKilos = number_format((float) $prize['kilos'], 2, '.', '');
        $this->prizePerKilo = number_format((float) $prize['price_per_kilo'], 2, '.', '');
        $this->allowRepeat = (bool) $summary['allow_repeat_winners'];
        $this->membersOnly = $service->membersOnly();
        $this->claimInstructions = $service->claimInstructions();
        $this->canSpin = $this->userIsAdmin();
        $this->cycles = $this->buildCycles($service);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildCycles(SpinWheelService $service): array
    {
        $active = $service->activeDrawDate()->toDateString();

        return collect($service->recentDrawDates())
            ->map(function (CarbonInterface $date) use ($service, $active) {
                [$start, $end] = $service->periodFor($date);

                $draw = SpinDraw::where('draw_date', $date->toDateString())->first();

                return [
                    'date' => $date->toDateString(),
                    'label' => $date->format('F j, Y'),
                    'period' => $start->format('M j').' – '.$end->format('M j, Y'),
                    'winners' => $draw
                        ? SpinWinner::where('DrawID', $draw->DrawID)->count()
                        : 0,
                    'target' => $draw?->winners_target,
                    'is_current' => $date->toDateString() === $active,
                ];
            })
            ->all();
    }

    private function currentDraw(): SpinDraw
    {
        $service = $this->service();

        return $service->ensureDraw(
            $this->drawDate ? Carbon::parse($this->drawDate) : $service->activeDrawDate()
        );
    }

    private function userIsAdmin(): bool
    {
        $user = Auth::user();

        return $user !== null
            && in_array((int) $user->RoleID, self::ADMIN_ROLES, true);
    }

    private function service(): SpinWheelService
    {
        return app(SpinWheelService::class);
    }

    public function render()
    {
        return view('livewire.components.spin-wheel-modal');
    }
}
