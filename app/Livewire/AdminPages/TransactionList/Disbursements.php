<?php

namespace App\Livewire\AdminPages\TransactionList;

use App\Livewire\Concerns\AuthorizesModules;
use App\Models\Disbursement;
use App\Models\MembershipFee;
use App\Models\User;
use App\Models\Variant;
use App\Support\Modules;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;

class Disbursements extends Component
{
    use HasTabSkeleton;
    use AuthorizesModules;

    /** Module this component manages (Transaction List - Disbursement). */
    protected function module(): int
    {
        return Modules::TXN_DISBURSEMENTS;
    }

    public string $search = '';

    /** '' = all statuses, else '0' | '1' | '2'. */
    public string $statusFilter = '';

    public bool $showModal = false;

    /** DID of the row being edited, null while creating. */
    public ?int $editingDid = null;

    public ?string $fee_id = null;

    public string $amount = '';

    public string $remarks = '';

    /**
     * Manual record date (Y-m-d). Defaults to today, editable while the
     * disbursement is Unposted, frozen once Posted/Voided.
     */
    public string $disbursement_date = '';

    /** Selected variant card (a VID). There is no "all variants" card. */
    public ?int $variantFilter = null;

    /** '' = every creator, otherwise a users.UserID. */
    public string $creatorFilter = '';

    /** DID waiting on the SweetAlert confirmation for post / void. */
    public ?int $pendingDid = null;

    /**
     * The tab opens on a variant card: the first Active variant that still
     * holds money, falling back to the first Active variant (Variant 1 when
     * two are live). Cards only scope a NEW disbursement's fund list.
     */
    public function mount(): void
    {
        $cards = $this->variantCards;

        $funded = $cards->first(fn (array $card) => (float) $card['available'] > 0);

        $this->variantFilter = $funded['VID'] ?? $cards->first()['VID'] ?? null;
    }

    /* --------------------------------------------------------------------------
    | List + modal data
    | -------------------------------------------------------------------------- */

    #[Computed]
    public function rows()
    {
        $query = Disbursement::query()
            ->with(['fee.variant', 'creator'])
            ->when($this->statusFilter !== '', fn ($q) => $q
                ->where('status', (int) $this->statusFilter))
            ->when($this->creatorFilter !== '', fn ($q) => $q
                ->where('CreatedBy', (int) $this->creatorFilter))
            // The variant card only scopes the fund list of a NEW
            // disbursement — the list itself always shows every row of the
            // branch scope, so a saved row can never "disappear".
            ->when($this->search !== '', function ($q) {
                $term = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $this->search).'%';
                $q->where(function ($q) use ($term) {
                    $q->where('remarks', 'like', $term)
                        ->orWhere('DID', 'like', $term)
                        ->orWhereIn('FeeID', MembershipFee::where('FeeName', 'like', $term)->select('FeeID'))
                        ->orWhereHas('creator', fn ($uq) => $uq
                            ->where('firstname', 'like', $term)
                            ->orWhere('lastname', 'like', $term)
                            ->orWhere('username', 'like', $term));
                });
            })
            ->orderByDesc('DID');

        return $this->scopeToBranch($query)->get();
    }    /**
     * Funds for the modal dropdown. A NEW disbursement is narrowed by the
     * selected variant card; an EDIT is never narrowed, so the row's own
     * source fund always restores into the dropdown (and stays available
     * even while another card is selected).
     */
    #[Computed]
    public function feeOptions()
    {
        $narrow = $this->variantFilter !== null && ! $this->editingDid;

        $query = MembershipFee::query()
            ->with('variant')
            ->when($narrow, fn ($q) => $q->where('VID', $this->variantFilter))
            ->orderBy('FeeName');

        // Branch isolation: only the variants granted to the user's branch
        // may be drawn from (an empty grant list offers no funds at all).
        if (($vids = $this->grantedVids()) !== null) {
            $query->whereIn('VID', $vids);
        }

        return $query->get()->map(fn (MembershipFee $fee) => [
                'FeeID' => $fee->FeeID,
                'FeeName' => $fee->FeeName,
                'Variant' => $fee->variant?->name ?? 'No Variant',
                'Total_Collection' => $this->totalCollectionFor($fee->FeeID),
                'Already_Disbursed' => Disbursement::disbursedForFee($fee->FeeID),
                'Released' => $this->releasedForFee($fee->FeeID),
                'ReleasedLegacy' => $this->releasedLegacyForFee($fee->FeeID),
            ]);
    }

    /**
     * The fee fund's remaining balance: collected membership fees minus
     * the ledger releases that draw this exact fund down (matched by
     * source name — see FEE_SOURCE_MAP) minus what has already been
     * disbursed out of it. Voided releases/disbursements never count.
     */
    #[Computed]
    public function selectedFund(): ?array
    {
        if (blank($this->fee_id)) {
            return null;
        }

        $fee = $this->feeOptions->firstWhere('FeeID', $this->fee_id);

        if (! $fee) {
            return null;
        }

        $disbursed = Disbursement::disbursedForFee($this->fee_id);

        // While editing, the row being edited has not really left the fund
        // yet: add it back so the ceiling matches the value in the form.
        if ($this->editingDid) {
            $disbursed -= (float) Disbursement::query()
                ->where('DID', $this->editingDid)
                ->where('status', '!=', Disbursement::STATUS_VOID)
                ->value('amount');
        }

        return [
            'collected' => round((float) $fee['Total_Collection'], 2),
            'released' => round((float) $fee['Released'], 2),
            'releasedLegacy' => round((float) $fee['ReleasedLegacy'], 2),
            'disbursed' => round($disbursed, 2),
            'available' => round((float) $fee['Total_Collection'] - (float) $fee['Released'] - $disbursed, 2),
        ];
    }

    

    /** Fund left for the fee currently selected in the modal. */
    #[Computed]
    public function availableForSelectedFee(): ?float
    {
        return $this->selectedFund['available'] ?? null;
    }

    /**
     * Top cards. Deliberately independent of the selected variant card and
     * of the table filters: they describe every disbursement CREATED inside
     * the signed-in user's branch scope, not just the rows on screen.
     */
    #[Computed]
    public function statistics(): array
    {
        $sums = $this->scopeToBranch(Disbursement::query())
            ->selectRaw('status, COALESCE(SUM(amount), 0) AS total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'unposted' => round((float) ($sums[Disbursement::STATUS_UNPOSTED] ?? 0), 2),
            'posted' => round((float) ($sums[Disbursement::STATUS_POSTED] ?? 0), 2),
            'void' => round((float) ($sums[Disbursement::STATUS_VOID] ?? 0), 2),
            'net' => round((float) ($sums[Disbursement::STATUS_POSTED] ?? 0), 2),
        ];
    }

    /** Users who created disbursements inside the user's branch scope. */
    #[Computed]
    public function creatorOptions()
    {
        $ids = $this->scopeToBranch(Disbursement::query())
            ->whereNotNull('CreatedBy')
            ->distinct()
            ->pluck('CreatedBy');

        return User::query()
            ->whereIn('UserID', $ids)
            ->orderBy('lastname')
            ->orderBy('firstname')
            ->get(['UserID', 'firstname', 'lastname', 'username'])
            ->map(fn (User $user) => [
                'UserID' => $user->UserID,
                'name' => trim(($user->firstname ?? '').' '.($user->lastname ?? '')) ?: $user->username,
            ]);
    }

    /**
     * Branch isolation. A signed-in user only sees (and only draws from) the
     * variants granted to their branch in branch_variant_grants, where
     * users.branchCode carries branches.BranchID. The System Administrator
     * (RoleID 1) — and contexts with no authenticated user — stay unrestricted.
     */
    private function scopeToBranch($query)
    {
        $vids = $this->grantedVids();

        if ($vids === null) {
            return $query;
        }

        return $query->whereHas('fee', fn ($q) => $q->whereIn('VID', $vids));
    }

    /** RoleID 1 is the System Administrator — no branch restriction. */
    private function isSystemAdministrator(): bool
    {
        return (int) (auth()->user()?->RoleID ?? 0) === 1;
    }

    /**
     * Variant IDs granted (Active) to the signed-in user's branch.
     * null = unrestricted; [] = no grants, so nothing is visible.
     *
     * @return array<int,int>|null
     */
    private function grantedVids(): ?array
    {
        $user = auth()->user();

        if (! $user || $this->isSystemAdministrator()) {
            return null;
        }

        if (blank($user->branchCode)) {
            return [];
        }

        return DB::table('branch_variant_grants')
            ->where('BranchID', (int) $user->branchCode)
            ->whereRaw("LOWER(TRIM(COALESCE(status, ''))) = ?", ['active'])
            ->pluck('VID')
            ->map(fn ($vid) => (int) $vid)
            ->all();
    }

    /** Remaining balance of a fund, optionally ignoring one edited row. */
    private function availableForFee(string $feeId, ?int $ignoreDid = null): ?float
    {
        $fee = $this->feeOptions->firstWhere('FeeID', $feeId);

        if (! $fee) {
            return null;
        }

        $disbursed = Disbursement::disbursedForFee($feeId);

        if ($ignoreDid) {
            $disbursed -= (float) Disbursement::query()
                ->where('DID', $ignoreDid)
                ->where('status', '!=', Disbursement::STATUS_VOID)
                ->value('amount');
        }

        return round((float) $fee['Total_Collection'] - (float) $fee['Released'] - $disbursed, 2);
    }

    /** True when the row's variant falls inside the user's branch grants. */
    private function visibleToUser(Disbursement $disbursement): bool
    {
        $vids = $this->grantedVids();

        if ($vids === null) {
            return true;
        }

        $vid = $disbursement->fee?->VID;

        return $vid !== null && in_array((int) $vid, $vids, true);
    }

    /** Ledger sources that release fee-fund money out of the system. */
    private const OUTFLOW_SOURCES = ['DR', 'OV', 'PF', 'WD', 'PK', 'CL', 'RF', 'SF', 'CS', 'SDR'];

    /** Ledger sources that top the fund up beyond membership fees. */
    private const INFLOW_SOURCES = ['CF', 'TX']; // convenience fee, tax in

    /**
     * Fee-fund name -> the ledger outflow source that pays out of that
     * exact fund. A Direct Referral release (DR) drains the "Direct
     * Referral" fee fund, an Overrides release (OV) drains "Overrides",
     * etc. Funds without a matched source carry releases only on their
     * variant card (e.g. WD withdrawals matched no fee).
     */
    private const FEE_SOURCE_MAP = [
        'direct referral' => 'DR',
        'overrides' => 'OV',
        'provident fund' => 'PF',
        'pangkabuhayan' => 'PK',
    ];

    private function matchedSourceFor(string $feeName): ?string
    {
        return self::FEE_SOURCE_MAP[strtolower(trim($feeName))] ?? null;
    }

    /**
     * Completed ledger money for the given sources attributed to a
     * variant through RefID -> activation -> VID. With $unattributedOnly
     * the legacy share is measured instead (rows reaching no activation
     * that carries a VID). $sign flips outflows to positive releases.
     */
    private function ledgerAmount(array $sources, int $vid, bool $unattributedOnly = false): float
    {
        $query = \App\Models\Ledger::query()
            ->where('status', 'Completed')
            ->whereIn('Source', $sources);

        // Attribution rides the ledgers.ActivationID column. RefID is NOT the
        // activation: on DR/OV/PF rows it holds the member's UserID, so
        // joining on RefID silently matched nothing and every release was
        // dropped from the available figure.
        if ($unattributedOnly) {
            $query->where($this->legacyAttribution());
        } else {
            $query->whereRaw(
                'ActivationID IN (SELECT ActivationID FROM activations WHERE VID = ?)',
                [$vid]
            );
        }

        return (float) $query->sum('amount');
    }

    /**
     * Ledger rows that carry no variant: the activation they point at was
     * created before activations.VID existed, or they point at no activation
     * at all (withdrawal/pangkabuhayan rows use withdrawID / DrawID).
     */
    private function legacyAttribution(): \Closure
    {
        return fn ($q) => $q
            ->whereNull('ActivationID')
            ->orWhereRaw('ActivationID NOT IN (SELECT ActivationID FROM activations WHERE VID IS NOT NULL)');
    }

    /**
     * VID of the variant that absorbs the legacy (unattributable) share: the
     * first Active variant inside the user's branch scope, in card order.
     */
    private function fallbackVid(): ?int
    {
        $query = Variant::query()
            ->whereRaw("LOWER(TRIM(COALESCE(status, ''))) = ?", ['active']);

        if (($granted = $this->grantedVids()) !== null) {
            $query->whereIn('VID', $granted);
        }

        $vid = $query->orderBy('name')->value('VID');

        return $vid !== null ? (int) $vid : null;
    }

    /**
     * Ledger money released out of a fee fund by the source matched to
     * its name (DR out of "Direct Referral", etc.). Strictly attributed
     * rows count always; the legacy share counts while the sole-active
     * fallback applies.
     */
    private function releasedForFee(string $feeId): float
    {
        $fee = MembershipFee::query()->where('FeeID', $feeId)->first(['FeeName', 'VID']);
        $source = $fee ? $this->matchedSourceFor((string) $fee->FeeName) : null;

        if (! $fee || ! $source) {
            return 0.0;
        }

        $vid = $fee->VID !== null ? (int) $fee->VID : null;

        $strict = $vid !== null ? -$this->ledgerAmount([$source], $vid) : 0.0;
        $legacy = $vid !== null && $vid === $this->fallbackVid()
            ? -$this->ledgerAmount([$source], $vid, true)
            : 0.0;

        return $strict + $legacy;
    }

    /** The legacy (unattributable) share behind releasedForFee, for display. */
    private function releasedLegacyForFee(string $feeId): float
    {
        $fee = MembershipFee::query()->where('FeeID', $feeId)->first(['FeeName', 'VID']);
        $source = $fee ? $this->matchedSourceFor((string) $fee->FeeName) : null;

        if (! $fee || ! $source) {
            return 0.0;
        }

        $vid = $fee->VID !== null ? (int) $fee->VID : null;

        return ($vid !== null && $vid === $this->fallbackVid())
            ? -$this->ledgerAmount([$source], $vid, true)
            : 0.0;
    }

    /**
     * Collected amount of a fee — the same definition the Report &
     * Settings Overview uses: SUM of Completed 'MF' ledger rows whose
     * RefID equals the FeeID.
     */
    private function totalCollectionFor(string $feeId): float
    {
        return (float) \App\Models\Ledger::where('Source', 'MF')
            ->where('RefID', $feeId)
            ->where('status', 'Completed')
            ->sum('amount');
    }

    /**
     * One card per Active variant GRANTED to the user's branch, with each
     * variant's fee-fund money summarised: how many funds exist and how much
     * is still undrawn (collected − released + fees/tax − disbursed; voided
     * rows never leave the fund). Unaffected by the selected card — it IS
     * the selector.
     */
    #[Computed]
    public function variantCards()
    {
        // Branch isolation: a branch only sees the variants it was granted.
        $vids = $this->grantedVids();
        // Legacy fallback: money rows that cannot be attributed to a variant
        // (activations created before activations.VID existed, and rows that
        // only carry withdrawID/DrawID) belong to the first Active card —
        // the variant that was already live when they were written. Counting
        // them there keeps the cards' total equal to the whole-company
        // collection: MF + CF + TX − DR − OV − PF − CL − RF − WD − PK − DB.
        $activeVids = Variant::query()
            ->whereRaw("LOWER(TRIM(COALESCE(status, ''))) = ?", ['active'])
            ->when($vids !== null, fn ($q) => $q->whereIn('VID', $vids))
            ->pluck('VID')
            ->all();

        $fallbackVid = $activeVids === [] ? null : (int) reset($activeVids);

        return Variant::query()
            ->whereRaw("LOWER(TRIM(COALESCE(status, ''))) = ?", ['active'])
            ->when($vids !== null, fn ($q) => $q->whereIn('VID', $vids))
            ->orderBy('name')
            ->get(['VID', 'name'])
            ->map(function ($variant) use ($fallbackVid) {
                $fees = MembershipFee::where('VID', $variant->VID)->get(['FeeID']);

                // Collected straight from the ledger, per fee — the same
                // definition as the Report & Settings "Membership Fee
                // Breakdown": SUM of Completed 'MF' rows by RefID.
                $collected = 0.0;
                $disbursed = 0.0;
                foreach ($fees as $fee) {
                    $collected += $this->totalCollectionFor($fee->FeeID);
                    $disbursed += Disbursement::disbursedForFee($fee->FeeID);
                }

                // Released money has already left the variant through the
                // ledger's outflow sources (direct-referral payouts,
                // overrides, provident fund, withdrawals, pangkabuhayan
                // prizes, claims, satellite splits). Those rows carry the
                // activation they paid out of in RefID, and the activation
                // carries the VID — that chain is how released money is
                // attributed to a variant.
                $released = (float) \App\Models\Ledger::query()
                    ->where('status', 'Completed')
                    ->whereIn('Source', self::OUTFLOW_SOURCES)
                    ->whereRaw(
                        'ActivationID IN (SELECT ActivationID FROM activations WHERE VID = ?)',
                        [$variant->VID]
                    )
                    ->selectRaw('COALESCE(SUM(-amount), 0) AS released')
                    ->value('released');

                // Legacy unattributable outflows land on the fallback card.
                $releasedLegacy = $variant->VID === $fallbackVid
                    ? $this->unattributedReleased
                    : 0.0;

                // Fees/tax collected on top of membership fees also belong
                // to the fund (same attribution rule and same fallback).
                $otherIn = (float) \App\Models\Ledger::query()
                    ->where('status', 'Completed')
                    ->whereIn('Source', self::INFLOW_SOURCES)
                    ->whereRaw(
                        'ActivationID IN (SELECT ActivationID FROM activations WHERE VID = ?)',
                        [$variant->VID]
                    )
                    ->selectRaw('COALESCE(SUM(amount), 0) AS inflow')
                    ->value('inflow');

                $otherInLegacy = $variant->VID === $fallbackVid
                    ? $this->unattributedInflow
                    : 0.0;

                return [
                    'VID' => $variant->VID,
                    'name' => $variant->name,
                    'funds' => $fees->count(),
                    'collected' => round($collected, 2),
                    'released' => round($released + $releasedLegacy, 2),
                    'releasedLegacy' => round($releasedLegacy, 2),
                    'otherIn' => round($otherIn + $otherInLegacy, 2),
                    'otherInLegacy' => round($otherInLegacy, 2),
                    'disbursed' => round($disbursed, 2),
                    'available' => round($collected - $released - $releasedLegacy + $otherIn + $otherInLegacy - $disbursed, 2),
                ];
            });
    }

    /**
     * Completed outflow rows that cannot be attributed to any variant
     * through activation VID: legacy activations (VID NULL) and rows whose
     * RefID hits no activation at all. Visible on the sole active card
     * while the sole-active fallback applies.
     */
    #[Computed]
    public function unattributedReleased(): float
    {
        return (float) \App\Models\Ledger::query()
            ->where('status', 'Completed')
            ->whereIn('Source', self::OUTFLOW_SOURCES)
            ->where($this->legacyAttribution())
            ->selectRaw('COALESCE(SUM(-amount), 0) AS released')
            ->value('released');
    }

    /** Legacy share of CF/TX money-in (unattributable through VID). */
    #[Computed]
    public function unattributedInflow(): float
    {
        return (float) \App\Models\Ledger::query()
            ->where('status', 'Completed')
            ->whereIn('Source', self::INFLOW_SOURCES)
            ->where($this->legacyAttribution())
            ->selectRaw('COALESCE(SUM(amount), 0) AS inflow')
            ->value('inflow');
    }

    /* --------------------------------------------------------------------------
    | Variant cards (fund picker scope)
    | -------------------------------------------------------------------------- */

    /**
     * Card click: narrow the modal fund dropdown to the chosen variant.
     * There is no "all variants" card, so the blade always passes a VID
     * (null only means "no narrowing" for callers that omit the card).
     * A fee already picked that no longer belongs to the narrowed
     * drop-down is cleared so the fund line can't go stale.
     */
    public function selectVariant(?int $vid): void
    {
        $this->variantFilter = $vid;

        // Only a NEW disbursement follows the card; an edit keeps its fund.
        if (! $this->editingDid && $this->fee_id
            && $this->feeOptions->firstWhere('FeeID', $this->fee_id) === null) {
            $this->fee_id = null;
        }
    }

    /* --------------------------------------------------------------------------
    | Modal open/close
    | -------------------------------------------------------------------------- */

    public function openCreate(): void
    {
        $this->authorizeCreateOrAbort();

        $this->resetForm();
        $this->disbursement_date = now()->format('Y-m-d'); // default: today
        $this->showModal = true;
    }

    public function openEdit(int $did): void
    {
        $this->authorizeUpdateOrAbort();

        $disbursement = Disbursement::findOrFail($did);

        if (! $this->visibleToUser($disbursement)) {
            $this->abortedAccess('edit a disbursement outside your branch');
            return;
        }

        if ((int) $disbursement->status !== Disbursement::STATUS_UNPOSTED) {
            $this->dispatch('swal:error', [
                'title' => 'Disbursement Locked',
                'message' => Disbursement::statusLabel((int) $disbursement->status).
                    ' disbursements cannot be edited.',
            ]);
            return;
        }

        $this->editingDid = $disbursement->DID;
        $this->fee_id = $disbursement->FeeID;
        // Editing ignores the selected variant card: the dropdown is not
        // narrowed, so the saved fund restores into the list as-is.
        $this->amount = (string) $disbursement->amount;
        $this->remarks = (string) ($disbursement->remarks ?? '');
        $this->disbursement_date = $disbursement->created_at?->format('Y-m-d') ?? '';
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingDid = null;
        $this->fee_id = null;
        $this->amount = '';
        $this->remarks = '';
        $this->disbursement_date = '';
        $this->resetErrorBag();
    }

    /* --------------------------------------------------------------------------
    | Save (create / edit)
    | -------------------------------------------------------------------------- */

    public function save(): void
    {
        if ($this->editingDid) {
            $this->authorizeUpdateOrAbort();
        } else {
            $this->authorizeCreateOrAbort();
        }

        $validated = $this->validate([
            'fee_id' => ['required', 'string', 'exists:membership_fees,FeeID'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'disbursement_date' => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        // The fund must be one this user may draw from (branch grants).
        if ($this->feeOptions->firstWhere('FeeID', $validated['fee_id']) === null) {
            $this->addError('fee_id', 'This source fund is not available to your branch.');
            return;
        }

        // The money must actually be left in the selected source fund: the
        // balance is collected − ledger releases matched to the fund − what
        // is already disbursed out of it, i.e. the very figure the modal
        // shows on its "Fund available" line.
        $available = $this->availableForFee($validated['fee_id'], $this->editingDid);

        if ($available !== null && (float) $validated['amount'] > $available + 0.005) {
            $this->addError('amount', $available > 0
                ? 'Amount exceeds the remaining balance of this source fund (₱'.number_format($available, 2).' available).'
                : 'This source fund has no remaining balance to disburse (₱0.00 available).');
            return;
        }

        $that = Disbursement::find($this->editingDid);

        if ($that && ! $this->visibleToUser($that)) {
            $this->addError('fee_id', 'This disbursement belongs to a variant outside your branch.');
            return;
        }

        // Editing is allowed while Unposted only (post/void are free to
        // proceed but a posted row's content is frozen).
        if ($that && (int) $that->status !== Disbursement::STATUS_UNPOSTED) {
            $this->addError('amount', 'This disbursement is '.
                mb_strtolower(Disbursement::statusLabel((int) $that->status)).
                ' and can no longer be edited.');
            return;
        }

        // The form works with `fee_id`; the column is `FeeID` (the model
        // only fills real column names, so this mapping is mandatory).
        $attributes = [
            'FeeID' => $validated['fee_id'],
            'amount' => $validated['amount'],
            'remarks' => $validated['remarks'] ?? null,
        ];

        $recordDate = $validated['disbursement_date'] ?? null;

        \Illuminate\Support\Facades\DB::transaction(function () use ($attributes, $recordDate) {
            if ($this->editingDid) {
                $disbursement = Disbursement::findOrFail($this->editingDid);

                // The record date keeps its original time of day and is
                // editable while Unposted only (the model freezes it after).
                if ($recordDate !== null && $recordDate !== '') {
                    $base = $disbursement->created_at ?: now();
                    $disbursement->created_at = Carbon::parse(
                        $recordDate.' '.$base->format('H:i:s')
                    );
                }

                $disbursement->fill($attributes)->save(); // ledger auto-syncs
            } else {
                Disbursement::create($attributes + [
                    'status' => Disbursement::STATUS_UNPOSTED,
                    'CreatedBy' => auth()->id(), // audit: who recorded it
                    'created_at' => $recordDate
                        ? Carbon::parse($recordDate.' '.now()->format('H:i:s'))
                        : now(),
                ]);
            }
        });

        $this->dispatch('swal:success', [
            'title' => $this->editingDid ? 'Disbursement Updated' : 'Disbursement Created',
            'message' => ($this->editingDid
                ? 'The pending ledger entry was updated to match.'
                : 'A pending, negative ledger entry (Source DB) was created.'),
        ]);

        $this->closeModal();
    }

    /* --------------------------------------------------------------------------
    | Post (0 -> 1) and Void (0/1 -> 2)
    | -------------------------------------------------------------------------- */

    /**
     * SweetAlert confirmation gates: the button only asks, the shared
     * handler fires the `disbursements-post` / `disbursements-void` event
     * once the admin confirms, and the pending DID does the work.
     */
    public function confirmPost(int $did): void
    {
        $this->authorizeChangeStatusOrAbort();

        $disbursement = Disbursement::find($did);

        if (! $disbursement || ! $this->visibleToUser($disbursement)) {
            $this->abortedAccess('post a disbursement outside your branch');
            return;
        }

        if ((int) $disbursement->status !== Disbursement::STATUS_UNPOSTED) {
            $this->dispatch('swal:error', [
                'title' => 'Already Processed',
                'message' => 'Only Unposted disbursements can be posted.',
            ]);
            return;
        }

        $this->pendingDid = $did;

        $this->dispatch('swal:confirm', [
            'title' => 'Post Disbursement #'.$did.'?',
            'text' => 'Its ledger entry becomes Completed — the expense goes live.',
            'icon' => 'question',
            'confirmText' => 'Yes, post it!',
            'cancelText' => 'Cancel',
            'method' => 'disbursements-post',
            'loadingTitle' => 'Posting disbursement...',
            'loadingText' => 'Updating the ledger entry.',
        ]);
    }

    #[On('disbursements-post')]
    public function postPending(): void
    {
        $did = $this->pendingDid;
        $this->pendingDid = null;

        if ($did) {
            $this->post($did);
        }
    }

    public function confirmVoid(int $did): void
    {
        $this->authorizeChangeStatusOrAbort();

        $disbursement = Disbursement::find($did);

        if (! $disbursement || ! $this->visibleToUser($disbursement)) {
            $this->abortedAccess('void a disbursement outside your branch');
            return;
        }

        if ((int) $disbursement->status !== Disbursement::STATUS_UNPOSTED) {
            $this->dispatch('swal:error', [
                'title' => 'Already Processed',
                'message' => 'Only Unposted disbursements are voided from here.',
            ]);
            return;
        }

        $this->pendingDid = $did;

        $this->dispatch('swal:confirm', [
            'title' => 'Void Disbursement #'.$did.'?',
            'text' => 'Its ledger entry becomes Cancelled and the money stays in the fund. Voiding is final.',
            'icon' => 'warning',
            'confirmText' => 'Yes, void it!',
            'cancelText' => 'Cancel',
            'method' => 'disbursements-void',
            'loadingTitle' => 'Voiding disbursement...',
            'loadingText' => 'Cancelling the ledger entry.',
        ]);
    }

    #[On('disbursements-void')]
    public function voidPending(): void
    {
        $did = $this->pendingDid;
        $this->pendingDid = null;

        if ($did) {
            $this->void($did);
        }
    }

    public function post(int $did): void
    {
        $this->authorizeChangeStatusOrAbort();

        $disbursement = Disbursement::findOrFail($did);

        if (! $this->visibleToUser($disbursement)) {
            $this->abortedAccess('post a disbursement outside your branch');
            return;
        }

        if ((int) $disbursement->status !== Disbursement::STATUS_UNPOSTED) {
            $this->dispatch('swal:error', [
                'title' => 'Already Processed',
                'message' => 'Only Unposted disbursements can be posted.',
            ]);
            return;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($disbursement) {
            $disbursement->update(['status' => Disbursement::STATUS_POSTED]); // ledger -> Completed
        });

        $this->dispatch('swal:success', [
            'title' => 'Disbursement Posted',
            'message' => 'The ledger entry is now Completed — the expense is live.',
        ]);
    }

    public function void(int $did, string $reason = ''): void
    {
        $this->authorizeChangeStatusOrAbort();

        $disbursement = Disbursement::findOrFail($did);

        if (! $this->visibleToUser($disbursement)) {
            $this->abortedAccess('void a disbursement outside your branch');
            return;
        }

        if ((int) $disbursement->status === Disbursement::STATUS_VOID) {
            $this->dispatch('swal:error', [
                'title' => 'Already Voided',
                'message' => 'Voiding is final — this row is already voided.',
            ]);
            return;
        }

        // A posted (ledger-live) expense needs a stated reason before it can
        // be cancelled; unposted rows void freely.
        if ((int) $disbursement->status === Disbursement::STATUS_POSTED && trim($reason) === '') {
            $this->dispatch('swal:error', [
                'title' => 'Void Reason Required',
                'message' => 'Voiding a POSTED disbursement affects live ledger data. Please state the reason for the void.',
            ]);
            return;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($disbursement, $reason) {
            if (trim($reason) !== '') {
                $note = trim((string) ($disbursement->remarks ?? ''));
                $disbursement->remarks = $note === ''
                    ? 'Void reason: '.trim($reason)
                    : $note."\nVoid reason: ".trim($reason);
                $disbursement->save(); // remarks edit is allowed here via the model hook
            }

            $disbursement->update(['status' => Disbursement::STATUS_VOID]); // ledger -> Cancelled
        });

        $this->dispatch('swal:success', [
            'title' => 'Disbursement Voided',
            'message' => 'The ledger entry was cancelled — the money stays in the fund.',
        ]);
    }

    /* --------------------------------------------------------------------------
    | Permission helpers (dispatch toast + no-op when denied)
    | -------------------------------------------------------------------------- */

    private function authorizeCreateOrAbort(): void
    {
        if (! $this->can('create')) {
            $this->abortedAccess('create disbursements');
        }
    }

    private function authorizeUpdateOrAbort(): void
    {
        if (! $this->can('update')) {
            $this->abortedAccess('edit disbursements');
        }
    }

    private function authorizeChangeStatusOrAbort(): void
    {
        if (! $this->can('change_status')) {
            $this->abortedAccess('post or void disbursements');
        }
    }

    private function abortedAccess(string $action): void
    {
        $this->dispatch('swal:error', [
            'title' => 'Not Allowed',
            'message' => "You don't have permission to {$action}.",
        ]);
        $this->showModal = false;
    }


    public function render()
    {
        return view('livewire.admin-pages.transaction-list.disbursements');
    }
}
