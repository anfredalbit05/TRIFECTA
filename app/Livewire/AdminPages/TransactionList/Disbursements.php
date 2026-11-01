<?php

namespace App\Livewire\AdminPages\TransactionList;

use App\Livewire\Concerns\AuthorizesModules;
use App\Models\Disbursement;
use App\Models\MembershipFee;
use App\Models\Variant;
use App\Support\Modules;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Disbursements extends Component
{
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

    /** Selected variant card: null = All Variants, otherwise a VID. */
    public ?int $variantFilter = null;

    /* --------------------------------------------------------------------------
    | List + modal data
    | -------------------------------------------------------------------------- */

    #[Computed]
    public function rows()
    {
        return Disbursement::query()
            ->with('fee.variant')
            ->when($this->statusFilter !== '', fn ($q) => $q
                ->where('status', (int) $this->statusFilter))
            ->when($this->variantFilter !== null, fn ($q) => $q
                ->whereHas('fee', fn ($fq) => $fq->where('VID', $this->variantFilter)))
            ->when($this->search !== '', function ($q) {
                $term = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $this->search).'%';
                $q->where(function ($q) use ($term) {
                    $q->where('remarks', 'like', $term)
                        ->orWhere('DID', 'like', $term)
                        ->orWhereIn('FeeID', MembershipFee::where('FeeName', 'like', $term)->select('FeeID'));
                });
            })
            ->orderByDesc('DID')
            ->get();
    }

    /** Funds for the modal dropdown — narrowed by the selected variant card. */
    #[Computed]
    public function feeOptions()
    {
        return MembershipFee::query()
            ->with('variant')
            ->when($this->variantFilter !== null, fn ($q) => $q
                ->where('VID', $this->variantFilter))
            ->orderBy('FeeName')
            ->get()
            ->map(fn (MembershipFee $fee) => [
                'FeeID' => $fee->FeeID,
                'FeeName' => $fee->FeeName,
                'Variant' => $fee->variant?->name ?? 'No Variant',
                'Total_Collection' => $this->totalCollectionFor($fee->FeeID),
                'Already_Disbursed' => Disbursement::disbursedForFee($fee->FeeID),
            ]);
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
     * Fund left for the fee currently selected in the modal:
     * collected minus disbursed (void rows don't count as disbursed).
     */
    #[Computed]
    public function availableForSelectedFee(): ?float
    {
        if (blank($this->fee_id)) {
            return null;
        }

        $fee = $this->feeOptions->firstWhere('FeeID', $this->fee_id);

        if (! $fee) {
            return null;
        }

        return $fee['Total_Collection'] - $fee['Already_Disbursed'];
    }

    /** Ledger sources that release fee-fund money out of the system. */
    private const OUTFLOW_SOURCES = ['DR', 'OV', 'PF', 'WD', 'PK', 'CL', 'SF', 'CS', 'SDR'];

    /** Ledger sources that top the fund up beyond membership fees. */
    private const INFLOW_SOURCES = ['CF', 'TX']; // convenience fee, tax in

    /**
     * One card per Active variant, with each variant's fee-fund money
     * summarised: how many funds exist and how much is still undrawn
     * (collected − disbursed; voided rows never leave the fund).
     * Unaffected by the selected card — it IS the selector.
     */
    #[Computed]
    public function variantCards()
    {
        // Sole-active fallback: outflow rows referencing activations created
        // before activations.VID existed cannot be attributed to a variant.
        // While exactly ONE variant is Active, that is where the money lives
        // in practice, so the unattributable releases count against it.
        $activeVids = Variant::query()
            ->whereRaw("LOWER(TRIM(COALESCE(status, ''))) = ?", ['active'])
            ->pluck('VID')
            ->all();

        $fallbackVid = count($activeVids) === 1 ? (int) reset($activeVids) : null;

        return Variant::query()
            ->whereRaw("LOWER(TRIM(COALESCE(status, ''))) = ?", ['active'])
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
                        'CAST(RefID AS UNSIGNED) IN (SELECT ActivationID FROM activations WHERE VID = ?)',
                        [$variant->VID]
                    )
                    ->selectRaw('COALESCE(SUM(-amount), 0) AS released')
                    ->value('released');

                // Legacy unattributable outflows land on the sole active card.
                $releasedLegacy = $variant->VID === $fallbackVid
                    ? $this->unattributedReleased
                    : 0.0;

                // Fees/tax collected on top of membership fees also belong
                // to the fund (same attribution rule and same fallback).
                $otherIn = (float) \App\Models\Ledger::query()
                    ->where('status', 'Completed')
                    ->whereIn('Source', self::INFLOW_SOURCES)
                    ->whereRaw(
                        'CAST(RefID AS UNSIGNED) IN (SELECT ActivationID FROM activations WHERE VID = ?)',
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
            ->whereRaw(
                'CAST(RefID AS UNSIGNED) NOT IN (SELECT ActivationID FROM activations WHERE VID IS NOT NULL)'
            )
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
            ->whereRaw(
                'CAST(RefID AS UNSIGNED) NOT IN (SELECT ActivationID FROM activations WHERE VID IS NOT NULL)'
            )
            ->selectRaw('COALESCE(SUM(amount), 0) AS inflow')
            ->value('inflow');
    }

    /* --------------------------------------------------------------------------
    | Variant cards (fund picker scope)
    | -------------------------------------------------------------------------- */

    /**
     * Card click: narrow the modal fund dropdown to the chosen variant
     * (null = back to all). A fee already picked that no longer belongs
     * to the narrowed drop-down is cleared so the fund line can't go stale.
     */
    public function selectVariant(?int $vid): void
    {
        $this->variantFilter = $vid;

        if ($this->fee_id && $this->feeOptions->firstWhere('FeeID', $this->fee_id) === null) {
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
        $this->showModal = true;
    }

    public function openEdit(int $did): void
    {
        $this->authorizeUpdateOrAbort();

        $disbursement = Disbursement::findOrFail($did);

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
        // Narrow the dropdown to this fund's variant so the edited fee
        // stays visible (and selectable) inside the card-filtered list.
        $this->variantFilter = $disbursement->fee?->VID;
        $this->amount = (string) $disbursement->amount;
        $this->remarks = (string) ($disbursement->remarks ?? '');
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
        ]);

        $that = Disbursement::find($this->editingDid);

        // Editing is allowed while Unposted only (post/void are free to
        // proceed but a posted row's content is frozen).
        if ($that && (int) $that->status !== Disbursement::STATUS_UNPOSTED) {
            $this->addError('amount', 'This disbursement is '.
                mb_strtolower(Disbursement::statusLabel((int) $that->status)).
                ' and can no longer be edited.');
            return;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
            if ($this->editingDid) {
                $disbursement = Disbursement::findOrFail($this->editingDid);
                $disbursement->fill($validated)->save(); // ledger auto-syncs
            } else {
                Disbursement::create($validated + ['status' => Disbursement::STATUS_UNPOSTED]);
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

    public function post(int $did): void
    {
        $this->authorizeChangeStatusOrAbort();

        $disbursement = Disbursement::findOrFail($did);

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
