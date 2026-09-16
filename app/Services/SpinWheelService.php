<?php

namespace App\Services;

use App\Models\Ledger;
use App\Models\Setting;
use App\Models\SpinDraw;
use App\Models\SpinWinner;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Spin the Wheel — the monthly Pangkabuhayan raffle.
 *
 * Rules implemented here:
 *  - A cycle is drawn on the 12th of the month and covers the window from the
 *    12th of the previous month up to the day before the draw (e.g. the
 *    September 12 draw covers August 12 – September 11).
 *  - The prize pool is the Pangkabuhayan (FEE004) collection inside that window.
 *  - Number of winners = floor(pool ÷ prize amount), e.g. ₱11,350 ÷ ₱275 = 41.
 *  - Members on the wheel are the uplines of the activations whose
 *    Pangkabuhayan share was collected inside the window — the same rows that
 *    fund the pool, so the prizes are always backed by that cycle's income.
 *  - Every winner is booked as an expense in `ledgers`: Source 'PK', the
 *    member's UserID as RefID, a negative amount and DrawID pointing at the
 *    cycle. The row is 'Pending' until an administrator tags the prize as
 *    claimed, and only then does it count against the books.
 */
class SpinWheelService
{
    /** Day of the month a cycle is drawn. */
    public const DRAW_DAY = 12;

    /** Membership fee row that represents the Pangkabuhayan share. */
    public const PANGKABUHAYAN_FEE_ID = 'FEE004';

    /*
     * Legacy settings (kept for reference — the prize is now derived from the
     * kilos × ₱/kilo settings below, which keeps the rice price dynamic).
     */
    public const PRIZE_NAME_SETTING = 'spin_prize_name';

    public const PRIZE_AMOUNT_SETTING = 'spin_prize_amount';

    /** Kilos of rice per prize — the dynamic half of the prize value. */
    public const PRIZE_KILOS_SETTING = 'spin_prize_kilos';

    /** Market price of one kilo of rice — the other dynamic half. */
    public const PRIZE_PRICE_PER_KILO_SETTING = 'spin_prize_price_per_kilo';

    public const DEFAULT_PRIZE_KILOS = 5.0;

    public const DEFAULT_PRIZE_PRICE_PER_KILO = 55.00;

    public const MEMBERS_ONLY_SETTING = 'spin_members_only';

    public const CLAIM_INSTRUCTIONS_SETTING = 'spin_claim_instructions';

    public const DEFAULT_CLAIM_INSTRUCTIONS = 'Present this email together with a valid government-issued ID at our office to claim your prize, or contact us to arrange pickup. Prizes are released to the winning member only.';

    /** Ledger Source used for a spin prize expense. */
    public const PRIZE_LEDGER_SOURCE = 'PK';

    /** RoleID of an ordinary member. */
    public const MEMBER_ROLE_ID = 4;

    public const DEFAULT_PRIZE_NAME = '5 kls Rice';

    public const DEFAULT_PRIZE_AMOUNT = 275.00;

    /** How many past cycles the modal offers in its month selector. */
    public const RECENT_CYCLES = 6;

    /*
    |--------------------------------------------------------------------------
    | Prize configuration (stored as settings, editable from the modal)
    |--------------------------------------------------------------------------
    */

    /**
     * The prize, derived from two dynamic inputs the admins edit as the rice
     * market moves: kilos per prize × price per kilo. The amount is computed
     * (never stored), so changing the market price re-prices the next cycle
     * while past draws keep the prize snapshotted on their spin_draws row.
     *
     * @return array{name: string, amount: float, kilos: float, price_per_kilo: float}
     */
    public function prizeConfig(): array
    {
        $kilos = Setting::where('name', self::PRIZE_KILOS_SETTING)->value('value');
        $perKilo = Setting::where('name', self::PRIZE_PRICE_PER_KILO_SETTING)->value('value');

        $kilos = is_numeric($kilos) && (float) $kilos > 0
            ? (float) $kilos
            : self::DEFAULT_PRIZE_KILOS;

        $perKilo = is_numeric($perKilo) && (float) $perKilo > 0
            ? (float) $perKilo
            : self::DEFAULT_PRIZE_PRICE_PER_KILO;

        $amount = round($kilos * $perKilo, 2);

        return [
            'name' => self::formatPrizeName($kilos),
            'amount' => $amount > 0 ? $amount : self::DEFAULT_PRIZE_AMOUNT,
            'kilos' => $kilos,
            'price_per_kilo' => $perKilo,
        ];
    }

    public function savePrizeConfig(float $kilos, float $pricePerKilo): void
    {
        Setting::updateOrCreate(
            ['name' => self::PRIZE_KILOS_SETTING],
            ['value' => (string) round(max(0.01, $kilos), 2)]
        );

        Setting::updateOrCreate(
            ['name' => self::PRIZE_PRICE_PER_KILO_SETTING],
            ['value' => (string) round(max(0.01, $pricePerKilo), 2)]
        );
    }

    /** Prize label from the kilos setting, e.g. "5 kls Rice". */
    public static function formatPrizeName(float $kilos): string
    {
        $kilos = rtrim(rtrim((string) round($kilos, 2), '0'), '.');

        return $kilos.' kls Rice';
    }

    /**
     * Whether the wheel is limited to member accounts (RoleID 4). Staff,
     * agency and executive accounts can be allowed in when they recruit too.
     * Defaults to members only.
     */
    public function membersOnly(): bool
    {
        $value = Setting::where('name', self::MEMBERS_ONLY_SETTING)->value('value');

        return $value === null
            ? true
            : filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function saveMembersOnly(bool $membersOnly): void
    {
        Setting::updateOrCreate(
            ['name' => self::MEMBERS_ONLY_SETTING],
            ['value' => $membersOnly ? 'true' : 'false']
        );
    }

    /**
     * "How to claim" text printed in the winner email.
     */
    public function claimInstructions(): string
    {
        $value = Setting::where('name', self::CLAIM_INSTRUCTIONS_SETTING)->value('value');

        return filled($value)
            ? (string) $value
            : self::DEFAULT_CLAIM_INSTRUCTIONS;
    }

    public function saveClaimInstructions(string $instructions): void
    {
        Setting::updateOrCreate(
            ['name' => self::CLAIM_INSTRUCTIONS_SETTING],
            ['value' => trim($instructions) !== ''
                ? trim($instructions)
                : self::DEFAULT_CLAIM_INSTRUCTIONS]
        );
    }

    /**
     * Company details used in the winner email.
     *
     * @return array{name: string, contact: string, email: string}
     */
    public function company(): array
    {
        $value = fn (string $key) => (string) (Setting::where('name', $key)->value('value') ?? '');

        return [
            'name' => $value('company_name') ?: 'True Life Dayongan',
            'contact' => $value('company_contact'),
            'email' => $value('company_email'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Cycle dates
    |--------------------------------------------------------------------------
    */

    /**
     * The draw date (12th) currently in force: the latest 12th that has
     * already happened. A cycle stays in force until the next 12th arrives.
     */
    public function activeDrawDate(?CarbonInterface $now = null): CarbonInterface
    {
        $now = $now ? $now->copy() : Carbon::now();

        return $now->day >= self::DRAW_DAY
            ? $now->copy()->day(self::DRAW_DAY)->startOfDay()
            : $now->copy()->subMonthNoOverflow()->day(self::DRAW_DAY)->startOfDay();
    }

    /**
     * The next draw date, i.e. the cycle that has not opened yet.
     */
    public function nextDrawDate(?CarbonInterface $now = null): CarbonInterface
    {
        return $this->activeDrawDate($now)->copy()->addMonthNoOverflow();
    }

    /**
     * Collection window for a draw date: 12th of the previous month 00:00:00
     * through the day before the draw 23:59:59.
     *
     * @return array{0: CarbonInterface, 1: CarbonInterface}
     */
    public function periodFor(CarbonInterface $drawDate): array
    {
        $start = $drawDate->copy()->subMonthNoOverflow()->startOfDay();
        $end = $drawDate->copy()->subDay()->endOfDay();

        return [$start, $end];
    }

    /**
     * Selectable cycles, newest first (defaults to the active cycle).
     *
     * @return array<int, CarbonInterface>
     */
    public function recentDrawDates(?CarbonInterface $now = null, int $count = self::RECENT_CYCLES): array
    {
        $dates = [];
        $drawDate = $this->activeDrawDate($now);

        for ($i = 0; $i < $count; $i++) {
            $dates[] = $drawDate->copy()->subMonthsNoOverflow($i);
        }

        return $dates;
    }

    /*
    |--------------------------------------------------------------------------
    | Pangkabuhayan pool + the members it elects
    |--------------------------------------------------------------------------
    */

    /**
     * The Pangkabuhayan share collected inside a window.
     */
    public function pangkabuhayanTotal(CarbonInterface $start, CarbonInterface $end): float
    {
        return (float) DB::table('ledgers')
            ->where('Source', 'MF')
            ->where('RefID', self::PANGKABUHAYAN_FEE_ID)
            ->where('status', 'Completed')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');
    }

    /**
     * Members who invited inside the window, in wheel order.
     *
     * Each entry is ['user' => User, 'invites' => int] where holds the number
     * of activations that member brought in during the window.
     *
     * @return Collection<int, array{user: User, invites: int}>
     */
    public function eligibleMembers(
        CarbonInterface $start,
        CarbonInterface $end,
        ?bool $membersOnly = null
    ): Collection {
        $query = DB::table('ledgers as l')
            ->join('activations as a', 'a.ActivationID', '=', 'l.ActivationID')
            ->join('users as u', 'u.referralCode', '=', 'a.upline_code')
            ->where('l.Source', 'MF')
            ->where('l.RefID', self::PANGKABUHAYAN_FEE_ID)
            ->where('l.status', 'Completed')
            ->whereBetween('l.created_at', [$start, $end])
            ->whereColumn('a.UserID', '!=', 'u.UserID'); // ignore self-referrals

        if ($membersOnly ?? $this->membersOnly()) {
            $query->where('u.RoleID', self::MEMBER_ROLE_ID);
        }

        $rows = $query
            ->selectRaw('u.UserID as uid, COUNT(*) as invites')
            ->groupBy('u.UserID')
            ->get();

        if ($rows->isEmpty()) {
            return collect();
        }

        $users = User::whereIn('UserID', $rows->pluck('uid'))
            ->get()
            ->keyBy('UserID');

        return $rows
            ->map(function ($row) use ($users) {
                $user = $users->get($row->uid);

                return $user
                    ? ['user' => $user, 'invites' => (int) $row->invites]
                    : null;
            })
            ->filter()
            ->sortBy(fn ($row) => mb_strtoupper(
                trim(($row['user']->lastname ?? '').' '.($row['user']->firstname ?? ''))
            ))
            ->values();
    }

    /**
     * Winners allowed for a pool: pool ÷ prize, rounded down.
     */
    public function winnersTarget(float $poolTotal, float $prizeAmount): int
    {
        if ($prizeAmount <= 0) {
            return 0;
        }

        return (int) floor($poolTotal / $prizeAmount);
    }

    /*
    |--------------------------------------------------------------------------
    | Draw records
    |--------------------------------------------------------------------------
    */

    /**
     * Get (or create) the draw row for a cycle. While a cycle has no winners
     * yet its snapshot is refreshed, so editing the prize before the first
     * spin recalculates the number of winners.
     */
    public function ensureDraw(CarbonInterface $drawDate, bool $refreshSnapshot = true): SpinDraw
    {
        $drawDate = $drawDate->copy()->startOfDay();
        [$start, $end] = $this->periodFor($drawDate);
        $prize = $this->prizeConfig();

        $draw = SpinDraw::firstOrNew(['draw_date' => $drawDate->toDateString()]);

        if (! $draw->exists) {
            $draw->fill([
                'period_start' => $start->toDateString(),
                'period_end' => $end->toDateString(),
                'pangkabuhayan_total' => $this->pangkabuhayanTotal($start, $end),
                'prize_name' => $prize['name'],
                'prize_amount' => $prize['amount'],
                'allow_repeat_winners' => true,
                'status' => 'Open',
            ]);

            $draw->winners_target = $this->winnersTarget(
                (float) $draw->pangkabuhayan_total,
                (float) $draw->prize_amount
            );

            $draw->save();

            return $draw;
        }

        $hasWinners = SpinWinner::where('DrawID', $draw->DrawID)->exists();

        if ($refreshSnapshot && ! $hasWinners) {
            $draw->forceFill([
                'period_start' => $start->toDateString(),
                'period_end' => $end->toDateString(),
                'pangkabuhayan_total' => $this->pangkabuhayanTotal($start, $end),
                'prize_name' => $prize['name'],
                'prize_amount' => $prize['amount'],
            ]);

            $draw->winners_target = $this->winnersTarget(
                (float) $draw->pangkabuhayan_total,
                (float) $draw->prize_amount
            );

            $draw->save();
        }

        return $draw;
    }

    /**
     * Members still on the wheel for a cycle (past winners are removed when
     * the cycle does not allow a member to win twice).
     *
     * @return Collection<int, array{user: User, invites: int}>
     */
    public function wheelMembers(SpinDraw $draw): Collection
    {
        [$start, $end] = $this->periodFor($draw->draw_date->copy());

        $eligible = $this->eligibleMembers($start, $end);

        if ($draw->allow_repeat_winners) {
            return $eligible;
        }

        $wonIds = SpinWinner::where('DrawID', $draw->DrawID)->pluck('UserID');

        return $eligible
            ->reject(fn ($row) => $wonIds->contains($row['user']->UserID))
            ->values();
    }

    /**
     * Winners already drawn for a cycle, oldest spin first. Each winner gets
     * their prize-expense ledger row attached (relation 'ledger'), paired
     * from the draw's Source PK rows — see prizeLedgersByMember().
     */
    public function winnersOf(SpinDraw $draw): Collection
    {
        $winners = SpinWinner::with('user')
            ->where('DrawID', $draw->DrawID)
            ->orderBy('spin_order')
            ->get();

        $ledgers = $this->prizeLedgersByMember($draw);

        foreach ($winners as $winner) {
            $winner->setRelation('ledger', $this->prizeLedgerFor($ledgers, $winner));
        }

        return $winners;
    }

    /**
     * Winner slots still available. When a member may only win once the slots
     * can never exceed the number of members actually on the wheel.
     */
    public function remainingSlots(SpinDraw $draw): int
    {
        $drawn = SpinWinner::where('DrawID', $draw->DrawID)->count();
        $target = (int) $draw->winners_target;

        if (! $draw->allow_repeat_winners) {
            $target = min($target, $this->wheelMembers($draw)->count() + $drawn);
        }

        return max(0, $target - $drawn);
    }

    /*
    |--------------------------------------------------------------------------
    | Spinning
    |--------------------------------------------------------------------------
    */

    /**
     * Draw one winner for a cycle.
     *
     * The winner is picked with a uniform (cryptographically random) draw on
     * the server; the returned index tells the browser which wheel segment the
     * animation must land on.
     *
     * @return array{index: int, total: int, winner: array<string, mixed>}
     */
    public function spin(SpinDraw $draw, ?int $drawnBy = null): array
    {
        return DB::transaction(function () use ($draw, $drawnBy) {
            $draw = SpinDraw::whereKey($draw->DrawID)->lockForUpdate()->firstOrFail();

            if ($this->remainingSlots($draw) <= 0) {
                throw new RuntimeException('This cycle already has all of its winners.');
            }

            $pool = $this->wheelMembers($draw);

            if ($pool->isEmpty()) {
                throw new RuntimeException('There are no eligible members left on the wheel.');
            }

            $index = random_int(0, $pool->count() - 1);
            $picked = $pool[$index];

            $draw->loadCount('winners');

            $winner = SpinWinner::create([
                'DrawID' => $draw->DrawID,
                'UserID' => $picked['user']->UserID,
                'spin_order' => ((int) $draw->winners_count) + 1,
                'qualified_invites' => $picked['invites'],
                'prize_name' => $draw->prize_name,
                'prize_amount' => $draw->prize_amount,
                'DrawnBy' => $drawnBy,
            ]);

            // Book the prize as a Pangkabuhayan expense straight away, so the
            // cost of the draw is on the books from the moment it is drawn.
            $this->bookPrize($draw, $winner);

            if ($this->remainingSlots($draw->fresh()) <= 0) {
                $draw->forceFill([
                    'status' => 'Completed',
                    'completed_at' => now(),
                ])->save();
            }

            return [
                'index' => $index,
                'total' => $pool->count(),
                'winner' => [
                    'WinnerID' => $winner->WinnerID,
                    'UserID' => $picked['user']->UserID,
                    'name' => $this->displayName($picked['user']),
                    'invites' => $picked['invites'],
                    'prize_name' => (string) $draw->prize_name,
                    'prize_amount' => (float) $draw->prize_amount,
                    'spin_order' => $winner->spin_order,
                ],
            ];
        });
    }

    /**
     * Book one prize as a Pangkabuhayan expense in the ledger.
     *
     * Source 'PK', RefID = the member's UserID, a negative amount (money going
     * out) and DrawID pointing at the cycle — the ledger row is linked to the
     * draw the same way an activation payment is linked through ActivationID.
     * ActivationID and WithdrawID are left NULL — a prize belongs to neither.
     * The row starts as 'Pending' and is only counted against the books once
     * an admin tags the prize claimed.
     *
     * The winner row is not modified: which expense belongs to which winner is
     * derived from per-member win order (see prizeLedgersByMember()).
     */
    private function bookPrize(SpinDraw $draw, SpinWinner $winner): Ledger
    {
        return Ledger::create([
            'RefID' => (string) $winner->UserID,
            'amount' => -abs((float) $winner->prize_amount),
            'Source' => self::PRIZE_LEDGER_SOURCE,
            'DrawID' => $draw->DrawID,
            'ActivationID' => null,
            'status' => 'Pending',
        ]);
    }

    /**
     * Undo the most recent spin of a cycle (an admin safety valve for a
     * mis-click). Re-opens the cycle if it had been completed, and removes the
     * Pangkabuhayan expense that spin booked.
     *
     * A prize that has already been tagged as claimed is refused: that rice is
     * physically out of the door, so quietly deleting its expense would leave
     * the books wrong.
     *
     * @throws RuntimeException when the last winner's prize was already claimed
     */
    public function undoLastWinner(SpinDraw $draw): bool
    {
        return (bool) DB::transaction(function () use ($draw) {
            $last = SpinWinner::where('DrawID', $draw->DrawID)
                ->orderByDesc('spin_order')
                ->orderByDesc('WinnerID')
                ->lockForUpdate()
                ->first();

            if (! $last) {
                return false;
            }

            $ledger = $this->prizeLedgerFor(
                $this->prizeLedgersByMember($draw, true),
                $last
            );

            if ($ledger && $ledger->status === 'Completed') {
                throw new RuntimeException(
                    'That prize is already tagged as claimed, so the spin can no longer be undone. '
                    .'Set its ledger entry back to Pending first if this was a mistake.'
                );
            }

            $ledger?->delete();
            $last->delete();

            $draw->forceFill([
                'status' => 'Open',
                'completed_at' => null,
            ])->save();

            return true;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Claiming prizes
    |--------------------------------------------------------------------------
    */

    /**
     * Tag a prize as claimed: its Pangkabuhayan expense moves from Pending to
     * Completed, which is when it starts counting against the books.
     *
     * @return array{ok: bool, message: string}
     */
    public function markClaimed(SpinDraw $draw, int $winnerID): array
    {
        return DB::transaction(function () use ($draw, $winnerID) {
            $winner = SpinWinner::where('DrawID', $draw->DrawID)
                ->where('WinnerID', $winnerID)
                ->with('user')
                ->lockForUpdate()
                ->first();

            if (! $winner) {
                return [
                    'ok' => false,
                    'message' => 'That winner does not belong to this cycle.',
                ];
            }

            $ledger = $this->ledgerForWinner($draw, $winner);

            if ($ledger->status === 'Completed') {
                return [
                    'ok' => false,
                    'message' => $this->displayName($winner->user).' was already tagged as claimed.',
                ];
            }

            $ledger->forceFill(['status' => 'Completed'])->save();

            return [
                'ok' => true,
                'message' => 'Prize released to '.$this->displayName($winner->user)
                    .'. The ₱'.number_format(abs((float) $ledger->amount), 2)
                    .' Pangkabuhayan expense (ledger #'.$ledger->TransactionID.') is now Completed.',
            ];
        });
    }

    /**
     * Tag every still-pending prize of a cycle as claimed.
     *
     * @return array{ok: bool, claimed: int, message: string}
     */
    public function markAllClaimed(SpinDraw $draw): array
    {
        $claimed = 0;

        foreach (SpinWinner::where('DrawID', $draw->DrawID)->pluck('WinnerID') as $winnerID) {
            if ($this->markClaimed($draw, (int) $winnerID)['ok']) {
                $claimed++;
            }
        }

        return [
            'ok' => $claimed > 0,
            'claimed' => $claimed,
            'message' => $claimed > 0
                ? $claimed.' prize(s) tagged as claimed. Their Pangkabuhayan expenses are now Completed.'
                : 'There were no pending prizes to tag for this cycle.',
        ];
    }

    /**
     * The draw's prize-expense rows (Source 'PK', DrawID = this cycle), grouped
     * by the winning member's UserID (RefID) in booking order.
     *
     * The link between a winner and their expense runs entirely through the
     * ledger row itself — DrawID + RefID, mirroring how an activation payment
     * is linked through ActivationID. A member can win more than once per
     * cycle, so their Nth win pairs with their Nth PK row (TransactionID order
     * within their RefID group): rows are written one per spin, strictly in
     * spin order, inside the same transaction.
     *
     * @return Collection<int, Collection<int, Ledger>>
     */
    private function prizeLedgersByMember(SpinDraw $draw, bool $lock = false): Collection
    {
        $query = Ledger::where('DrawID', $draw->DrawID)
            ->where('Source', self::PRIZE_LEDGER_SOURCE)
            ->orderBy('TransactionID');

        return ($lock ? $query->lockForUpdate() : $query)
            ->get()
            ->groupBy(fn (Ledger $ledger) => (int) $ledger->RefID);
    }

    /**
     * The expense row that belongs to a winner: the Nth 'PK' row of their
     * UserID within the draw, where N is how many times that member has won
     * this cycle (see memberWinIndex()). Null when none was booked yet — a
     * winner drawn before the ledger integration, or whose row was deleted by
     * hand; callers book one on the spot in that case.
     */
    private function prizeLedgerFor(Collection $ledgersByMember, SpinWinner $winner): ?Ledger
    {
        return $ledgersByMember
            ->get((int) $winner->UserID)
            ?->get($this->memberWinIndex($winner));
    }

    /**
     * The 0-based index of a winner within their own wins of the cycle: how
     * many times the same member had already won before this spin. This is
     * deliberately NOT the global spin_order — that counts every spin of the
     * cycle, so member B winning spin #2 is still on their FIRST prize.
     */
    private function memberWinIndex(SpinWinner $winner): int
    {
        return SpinWinner::query()
            ->where('DrawID', $winner->DrawID)
            ->where('UserID', $winner->UserID)
            ->where('spin_order', '<', $winner->spin_order)
            ->count();
    }

    /**
     * The expense row for a winner, booked on the spot when it is missing —
     * e.g. for winners drawn before the ledger integration, or whose expense
     * row was deleted by hand.
     */
    private function ledgerForWinner(SpinDraw $draw, SpinWinner $winner): Ledger
    {
        $ledger = $this->prizeLedgerFor(
            $this->prizeLedgersByMember($draw, true),
            $winner
        );

        return $ledger ?: $this->bookPrize($draw, $winner);
    }

    /**
     * How many prizes of a cycle are claimed and how many are still pending.
     *
     * Anything drawn but not tagged as claimed counts as pending, including a
     * winner drawn before the ledger integration whose expense row has not been
     * booked yet — their row is written the moment they are tagged.
     *
     * @return array{claimed: int, pending: int}
     */
    private function claimCounts(SpinDraw $draw): array
    {
        $drawn = SpinWinner::where('DrawID', $draw->DrawID)->count();

        $claimed = Ledger::where('DrawID', $draw->DrawID)
            ->where('Source', self::PRIZE_LEDGER_SOURCE)
            ->where('status', 'Completed')
            ->count();

        return [
            'claimed' => $claimed,
            'pending' => max(0, $drawn - $claimed),
        ];
    }

    /**
     * Everything the modal needs to describe a cycle.
     */
    public function summary(SpinDraw $draw): array
    {
        $draw->refresh();

        $drawn = SpinWinner::where('DrawID', $draw->DrawID)->count();
        $eligible = $this->eligibleMembers(
            $draw->period_start->copy()->startOfDay(),
            $draw->period_end->copy()->endOfDay()
        );

        $claim = $this->claimCounts($draw);

        $next = $this->nextDrawDate();

        return [
            'draw_date' => $draw->draw_date->copy(),
            'period_start' => $draw->period_start->copy(),
            'period_end' => $draw->period_end->copy(),
            'pangkabuhayan_total' => (float) $draw->pangkabuhayan_total,
            'prize_name' => (string) $draw->prize_name,
            'prize_amount' => (float) $draw->prize_amount,
            'winners_target' => (int) $draw->winners_target,
            'winners_count' => $drawn,
            'claimed_count' => $claim['claimed'],
            'pending_count' => $claim['pending'],
            'claim_costs' => [
                'claimed' => round($claim['claimed'] * (float) $draw->prize_amount, 2),
                'pending' => round($claim['pending'] * (float) $draw->prize_amount, 2),
            ],
            'remaining_slots' => $this->remainingSlots($draw),
            'eligible_count' => $eligible->count(),
            'allow_repeat_winners' => (bool) $draw->allow_repeat_winners,
            'status' => (string) $draw->status,
            'next_draw_date' => $next->copy(),
            'next_period' => $this->periodFor($next),
            'has_winners' => $drawn > 0,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Fund report + the full winners list
    |--------------------------------------------------------------------------
    */

    /**
     * Where the Pangkabuhayan money went, per cycle: what was collected in the
     * window, what has been booked as prize expenses and how much of that is
     * already claimed (Released) vs still pending — the fund balance is what
     * has not been promised as rice yet. Derived entirely from ledgers +
     * spin_draws, so nothing new is stored.
     *
     * @return array{rows: Collection, unrecorded: Collection, totals: array{collected: float, booked: float, claimed: float, pending: float, balance: float}}
     */
    public function fundReport(): array
    {
        $draws = SpinDraw::withCount('winners')
            ->orderByDesc('draw_date')
            ->get();

        $rows = $draws->map(function (SpinDraw $draw) {
            $expenses = Ledger::where('DrawID', $draw->DrawID)
                ->where('Source', self::PRIZE_LEDGER_SOURCE)
                ->get(['status', 'amount']);

            $claimed = round((float) $expenses
                ->where('status', 'Completed')->sum('amount'), 2);
            $pending = round((float) $expenses
                ->where('status', '!=', 'Completed')->sum('amount'), 2);

            $collected = round((float) $draw->pangkabuhayan_total, 2);

            return [
                'draw_id' => (int) $draw->DrawID,
                'draw_date' => $draw->draw_date->copy(),
                'period_label' => $draw->period_start->copy()->format('M j')
                    .' – '.$draw->period_end->copy()->format('M j, Y'),
                'status' => (string) $draw->status,
                'prize_name' => (string) $draw->prize_name,
                'prize_amount' => (float) $draw->prize_amount,
                'collected' => $collected,
                'winners_target' => (int) $draw->winners_target,
                'winners_drawn' => (int) $draw->winners_count,
                'booked' => round(abs($claimed) + abs($pending), 2),
                'claimed' => round(abs($claimed), 2),
                'pending' => round(abs($pending), 2),
                'pending_count' => $expenses->where('status', '!=', 'Completed')->count(),
                'balance' => round($collected - (abs($claimed) + abs($pending)), 2),
            ];
        });

        // Cycles whose window has fully passed but that were never drawn —
        // their Pangkabuhayan collection exists but no prizes were booked.
        $recorded = $draws->pluck('draw_date')->map->toDateString()->all();

        $unrecorded = collect($this->recentDrawDates(null, 12))
            ->filter(fn (CarbonInterface $date) => ! in_array($date->toDateString(), $recorded, true))
            ->map(function (CarbonInterface $date) {
                [$start, $end] = $this->periodFor($date);

                return [
                    'draw_date' => $date->copy(),
                    'period_label' => $start->format('M j').' – '.$end->format('M j, Y'),
                    'collected' => round($this->pangkabuhayanTotal($start, $end), 2),
                ];
            })
            ->values();

        $totals = [
            'collected' => round($rows->sum('collected') + $unrecorded->sum('collected'), 2),
            'booked' => round($rows->sum('booked'), 2),
            'claimed' => round($rows->sum('claimed'), 2),
            'pending' => round($rows->sum('pending'), 2),
        ];
        $totals['balance'] = round($totals['collected'] - $totals['booked'], 2);

        return [
            'rows' => $rows,
            'unrecorded' => $unrecorded,
            'totals' => $totals,
        ];
    }

    /**
     * Every winner ever drawn, newest cycle first, each carrying their
     * expense row as the relation 'ledger' (paired the same way the modal
     * pairs them — see prizeLedgersByMember()).
     *
     * @return Collection<int, SpinWinner>
     */
    public function allWinners(): Collection
    {
        $winners = SpinWinner::with(['user', 'draw'])
            ->orderByDesc('DrawID')
            ->orderBy('spin_order')
            ->get();

        if ($winners->isEmpty()) {
            return $winners;
        }

        $ledgers = Ledger::where('Source', self::PRIZE_LEDGER_SOURCE)
            ->whereIn('DrawID', $winners->pluck('DrawID')->unique())
            ->orderBy('TransactionID')
            ->get()
            ->groupBy(fn (Ledger $ledger) => ((int) $ledger->DrawID).'-'.(int) $ledger->RefID);

        // Nth win of a member in a cycle pairs with their Nth PK row.
        $seen = [];

        foreach ($winners as $winner) {
            $key = ((int) $winner->DrawID).'-'.(int) $winner->UserID;
            $seen[$key] = ($seen[$key] ?? -1) + 1;

            $winner->setRelation('ledger', $ledgers->get($key)?->get($seen[$key]));
        }

        return $winners;
    }

    /*
    |--------------------------------------------------------------------------
    | Presentational helpers
    |--------------------------------------------------------------------------
    */

    public function displayName(?User $user): string
    {
        if (! $user) {
            return 'Unknown Member';
        }

        $name = $this->normalizeSpace(
            ($user->firstname ?? '').' '.($user->lastname ?? '')
        );

        return $name !== '' ? $name : (string) ($user->username ?? 'Member');
    }

    /**
     * Short label used on the wheel segments, e.g. "Juan D.".
     */
    public function shortName(?User $user): string
    {
        if (! $user) {
            return '—';
        }

        $first = $this->normalizeSpace((string) ($user->firstname ?? ''));
        $last = $this->normalizeSpace((string) ($user->lastname ?? ''));

        if ($first === '' && $last === '') {
            return (string) ($user->username ?? 'Member');
        }

        if ($first === '') {
            return $last;
        }

        return $last !== '' ? $first.' '.mb_substr($last, 0, 1).'.' : $first;
    }

    /**
     * First name only — wheel labels have very little room to breathe.
     */
    public function firstName(?User $user): string
    {
        if (! $user) {
            return '—';
        }

        $first = $this->normalizeSpace((string) ($user->firstname ?? ''));

        if ($first !== '') {
            return $first;
        }

        $last = $this->normalizeSpace((string) ($user->lastname ?? ''));

        return $last !== '' ? $last : (string) ($user->username ?? 'Member');
    }

    private function normalizeSpace(string $value): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $value));
    }
}
