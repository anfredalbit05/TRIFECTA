<?php

namespace App\Livewire\AdminPages\TransactionList;

use App\Livewire\Concerns\AuthorizesModules;
use App\Livewire\Concerns\HasTabSkeleton;
use App\Models\Disbursement;
use App\Models\Ledger;
use App\Models\MembershipFee;
use App\Models\User;
use App\Support\Modules;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
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

    /** '' = every creator, otherwise a users.UserID. */
    public string $creatorFilter = '';

    public bool $showModal = false;

    public ?string $fee_id = null;

    public string $amount = '';

    public string $remarks = '';

    /** Record date (Y-m-d), defaults to today. */
    public string $disbursement_date = '';

    /* --------------------------------------------------------------------------
    | Fee cards (top of the tab)
    | -------------------------------------------------------------------------- */

    /** Completed 'MF' ledger totals keyed by FeeID (RefID = FeeID). */
    private function collectedByFee()
    {
        return Ledger::query()
            ->where('Source', 'MF')
            ->where('status', 'Completed')
            ->selectRaw('RefID, COALESCE(SUM(amount), 0) AS total')
            ->groupBy('RefID')
            ->pluck('total', 'RefID')
            ->map(fn ($total) => (float) $total);
    }

    /** Disbursed totals keyed by FeeID. */
    private function disbursedByFee()
    {
        return Disbursement::query()
            ->selectRaw('FeeID, COALESCE(SUM(amount), 0) AS total')
            ->groupBy('FeeID')
            ->pluck('total', 'FeeID')
            ->map(fn ($total) => (float) $total);
    }

    /**
     * One card per membership fee: collected from the ledger minus what
     * has been disbursed, so the card shows what is left in the fund.
     */
    #[Computed]
    public function feeCards()
    {
        $collected = $this->collectedByFee();
        $disbursed = $this->disbursedByFee();

        return MembershipFee::query()
            ->orderBy('FeeID')
            ->get(['FeeID', 'FeeName', 'status'])
            ->map(function (MembershipFee $fee) use ($collected, $disbursed) {
                $in = round((float) ($collected[$fee->FeeID] ?? 0), 2);
                $out = round((float) ($disbursed[$fee->FeeID] ?? 0), 2);

                return [
                    'FeeID' => $fee->FeeID,
                    'name' => $fee->FeeName,
                    'status' => $fee->status,
                    'collected' => $in,
                    'disbursed' => $out,
                    'available' => round($in - $out, 2),
                ];
            });
    }

    /** The "all membership fees" card: total collected minus all disbursements. */
    #[Computed]
    public function totalAvailable(): float
    {
        return round(
            (float) $this->collectedByFee()->sum() - (float) $this->disbursedByFee()->sum(),
            2
        );
    }

    /** Total collected before any disbursement (shown under the total card). */
    #[Computed]
    public function totalCollected(): float
    {
        return round((float) $this->collectedByFee()->sum(), 2);
    }

    /** Total disbursed across every fund. */
    #[Computed]
    public function totalDisbursed(): float
    {
        return round((float) $this->disbursedByFee()->sum(), 2);
    }

    /* --------------------------------------------------------------------------
    | List + modal data
    | -------------------------------------------------------------------------- */

    #[Computed]
    public function rows()
    {
        return Disbursement::query()
            ->with(['fee', 'creator'])
            ->when($this->creatorFilter !== '', fn ($q) => $q
                ->where('CreatedBy', (int) $this->creatorFilter))
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
            ->orderByDesc('DID')
            ->get();
    }

    /** Modal dropdown: Active membership fees only. */
    #[Computed]
    public function feeOptions()
    {
        $collected = $this->collectedByFee();

        return MembershipFee::query()
            ->whereRaw("LOWER(TRIM(COALESCE(status, ''))) = ?", ['active'])
            ->orderBy('FeeName')
            ->get(['FeeID', 'FeeName'])
            ->map(fn (MembershipFee $fee) => [
                'FeeID' => $fee->FeeID,
                'FeeName' => $fee->FeeName,
                'Total_Collection' => (float) ($collected[$fee->FeeID] ?? 0),
            ]);
    }

    /** Fund balance shown in the modal: collected − disbursed. */
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

        return [
            'collected' => round((float) $fee['Total_Collection'], 2),
            'disbursed' => round($disbursed, 2),
            'available' => round((float) $fee['Total_Collection'] - $disbursed, 2),
        ];
    }

    /** True when the typed amount is greater than the selected fund's balance. */
    #[Computed]
    public function insufficientFund(): bool
    {
        if (blank($this->fee_id) || $this->amount === '' || ! is_numeric($this->amount)) {
            return false;
        }

        $available = (float) ($this->selectedFund['available'] ?? 0);

        return (float) $this->amount > $available + 0.005;
    }

    /** Users who created disbursements. */
    #[Computed]
    public function creatorOptions()
    {
        $ids = Disbursement::query()
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

    /* --------------------------------------------------------------------------
    | Modal open/close
    | -------------------------------------------------------------------------- */

    public function openCreate(): void
    {
        if (! $this->authorizeCreateOrAbort()) {
            return;
        }

        $this->resetForm();
        $this->disbursement_date = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->fee_id = null;
        $this->amount = '';
        $this->remarks = '';
        $this->disbursement_date = '';
        $this->resetErrorBag();
    }

    /* --------------------------------------------------------------------------
    | Review (step 1) and Save (step 2, after the SweetAlert confirmation)
    | -------------------------------------------------------------------------- */

    /**
     * Validates the form. Returns the validated data, or null after adding
     * the error to the form (field errors show under the inputs).
     */
    private function validatedInput(): ?array
    {
        $validated = $this->validate([
            'fee_id' => ['required', 'string', 'exists:membership_fees,FeeID'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'disbursement_date' => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        // Only Active fees are allowed.
        if ($this->feeOptions->firstWhere('FeeID', $validated['fee_id']) === null) {
            $this->addError('fee_id', 'This source fund is not Active.');
            return null;
        }

        // Cannot proceed when the amount is greater than the available fund.
        if ($this->insufficientFund) {
            $this->addError('amount', 'Insufficient fund available.');
            return null;
        }

        return $validated;
    }

    /**
     * Step 1: called by the Save button. Validates, then returns the
     * (HTML-escaped) review data the SweetAlert shows. Nothing is saved.
     */
    public function prepareReview(): ?array
    {
        if (! $this->authorizeCreateOrAbort()) {
            return null;
        }

        $validated = $this->validatedInput();

        if ($validated === null) {
            return null;
        }

        $fee = $this->feeOptions->firstWhere('FeeID', $validated['fee_id']);
        $available = (float) ($this->selectedFund['available'] ?? 0);
        $amount = (float) $validated['amount'];
        $date = ! empty($validated['disbursement_date'])
            ? Carbon::parse($validated['disbursement_date'])->format('M j, Y')
            : now()->format('M j, Y');
        $remarks = trim((string) ($validated['remarks'] ?? ''));

        return [
            'fund' => e($fee['FeeName'].' ('.$fee['FeeID'].')'),
            'available' => '₱'.number_format($available, 2),
            'amount' => '₱'.number_format($amount, 2),
            'remaining' => '₱'.number_format($available - $amount, 2),
            'date' => e($date),
            'remarks' => $remarks !== '' ? nl2br(e($remarks)) : '—',
        ];
    }

    /**
     * Step 2: inserts the row. Only runs after the admin ticked the
     * confirmation box in the SweetAlert ($confirmed = true).
     */
    public function save(bool $confirmed = false): void
    {
        if (! $this->authorizeCreateOrAbort()) {
            return;
        }

        if (! $confirmed) {
            $this->dispatch('swal:error', [
                'title' => 'Confirmation Required',
                'message' => 'Please review and confirm the information before saving.',
            ]);
            return;
        }

        $validated = $this->validatedInput();

        if ($validated === null) {
            return;
        }

        $recordDate = $validated['disbursement_date'] ?? null;

        Disbursement::create([
            'FeeID' => $validated['fee_id'],
            'amount' => $validated['amount'],
            'remarks' => $validated['remarks'] ?? null,
            'CreatedBy' => Auth::id(),
            'created_at' => $recordDate
                ? Carbon::parse($recordDate.' '.now()->format('H:i:s'))
                : now(),
        ]);

        $this->dispatch('swal:success', [
            'title' => 'Disbursement Saved',
            'message' => 'The disbursement was recorded.',
        ]);

        $this->closeModal();
    }

    /* --------------------------------------------------------------------------
    | Permission helper
    | -------------------------------------------------------------------------- */

    private function authorizeCreateOrAbort(): bool
    {
        if ($this->can('create')) {
            return true;
        }

        $this->dispatch('swal:error', [
            'title' => 'Not Allowed',
            'message' => "You don't have permission to create disbursements.",
        ]);
        $this->showModal = false;

        return false;
    }

    public function render()
    {
        return view('livewire.admin-pages.transaction-list.disbursements');
    }
}
