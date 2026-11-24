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
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Disbursements extends Component
{
    use HasTabSkeleton;
    use AuthorizesModules;

    /**
     * Fee name (lowercase) => wallets.source whose Active amounts are
     * deducted from that fund. Commission pays out through DR wallets,
     * Overrides through OV wallets.
     */
    private const WALLET_SOURCE_BY_FEE_NAME = [
        'commission' => 'DR',
        'overrides' => 'OV',
    ];

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

    private const INSURANCE_FEE_ID = 'FEE001';
    private const INSURANCE_PLAN = 'Economy/Standard';
    private const INSURANCE_AMOUNT_PER_MEMBER = 400.00;

    public array $selectedMemberIds = [];

    public bool $showInsuranceMembersModal = false;

    /* --------------------------------------------------------------------------
    | Fund figures
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
     * Wallet amounts deducted from a fund, keyed by FeeID: Commission takes
     * the Active DR wallets, Overrides the Active OV wallets. Other funds 0.
     */
    private function walletByFee()
    {
        $sums = DB::table('wallets')
            ->where('status', 'Active')
            ->whereIn('source', array_values(self::WALLET_SOURCE_BY_FEE_NAME))
            ->selectRaw('source, COALESCE(SUM(amount), 0) AS total')
            ->groupBy('source')
            ->pluck('total', 'source');

        return MembershipFee::query()
            ->get(['FeeID', 'FeeName'])
            ->mapWithKeys(function (MembershipFee $fee) use ($sums) {
                $source = self::WALLET_SOURCE_BY_FEE_NAME[strtolower(trim((string) $fee->FeeName))] ?? null;

                return [$fee->FeeID => $source ? (float) ($sums[$source] ?? 0) : 0.0];
            });
    }

    /**
     * One card per membership fee: collected from the ledger minus wallet
     * amounts (Commission / Overrides) minus disbursements.
     */
    #[Computed]
    public function feeCards()
    {
        $collected = $this->collectedByFee();
        $disbursed = $this->disbursedByFee();
        $wallet = $this->walletByFee();

        return MembershipFee::query()
            ->orderBy('FeeID')
            ->get(['FeeID', 'FeeName', 'status'])
            ->map(function (MembershipFee $fee) use ($collected, $disbursed, $wallet) {
                $in = round((float) ($collected[$fee->FeeID] ?? 0), 2);
                $out = round((float) ($disbursed[$fee->FeeID] ?? 0), 2);
                $wal = round((float) ($wallet[$fee->FeeID] ?? 0), 2);

                return [
                    'FeeID' => $fee->FeeID,
                    'name' => $fee->FeeName,
                    'status' => $fee->status,
                    'collected' => $in,
                    'wallet' => $wal,
                    'disbursed' => $out,
                    'available' => round($in - $wal - $out, 2),
                ];
            });
    }

    /** The "all membership fees" card: collected − wallet − disbursed. */
    #[Computed]
    public function totalAvailable(): float
    {
        return round(
            (float) $this->collectedByFee()->sum()
                - (float) $this->walletByFee()->sum()
                - (float) $this->disbursedByFee()->sum(),
            2
        );
    }

    /** Total collected before any deduction. */
    #[Computed]
    public function totalCollected(): float
    {
        return round((float) $this->collectedByFee()->sum(), 2);
    }

    /** Total wallet amount deducted (Commission DR + Overrides OV). */
    #[Computed]
    public function totalWallet(): float
    {
        return round((float) $this->walletByFee()->sum(), 2);
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
        $wallet = $this->walletByFee();

        return MembershipFee::query()
            ->whereRaw("LOWER(TRIM(COALESCE(status, ''))) = ?", ['active'])
            ->orderBy('FeeName')
            ->get(['FeeID', 'FeeName'])
            ->map(fn (MembershipFee $fee) => [
                'FeeID' => $fee->FeeID,
                'FeeName' => $fee->FeeName,
                'Total_Collection' => (float) ($collected[$fee->FeeID] ?? 0),
                'Wallet' => (float) ($wallet[$fee->FeeID] ?? 0),
            ]);
    }

    /** Fund balance shown in the modal: collected − wallet − disbursed. */
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

        $disbursed = (float) ($this->disbursedByFee()[$this->fee_id] ?? 0);

        $collected = (float) $fee['Total_Collection'];
        $wallet = (float) $fee['Wallet'];

        return [
            'collected' => round($collected, 2),
            'wallet' => round($wallet, 2),
            'disbursed' => round($disbursed, 2),
            'available' => round($collected - $wallet - $disbursed, 2),
        ];
    }

    /** True when the typed amount is greater than the selected fund's balance. */
    #[Computed]
    public function insufficientFund(): bool
    {
        if ($this->isInsuranceFund) {
            if ($this->insuranceMemberCount < 1) {
                return true;
            }

            $amount = $this->insuranceTotalCoverage;
        } else {
            if ($this->amount === '' || ! is_numeric($this->amount)) {
                return false;
            }

            $amount = (float) $this->amount;
        }

        $available = (float) ($this->selectedFund['available'] ?? 0);

        return $amount > $available + 0.005;
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
        // if (! $this->authorizeCreateOrAbort()) {
        //     return;
        // }

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
        $this->selectedMemberIds = [];
        $this->showInsuranceMembersModal = false;

        unset(
            $this->eligibleInsuranceMembers,
            $this->selectedInsuranceMembers,
            $this->insuranceMemberCount,
            $this->insuranceTotalCoverage,
            $this->selectedFund
        );

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
        if ($this->isInsuranceFund) {
            $this->selectedMemberIds = array_values(array_unique(
                array_map('intval', $this->selectedMemberIds)
            ));

            if (empty($this->selectedMemberIds)) {
                $this->addError(
                    'selectedMemberIds',
                    'Please select at least one member.'
                );

                return null;
            }

            $this->amount = number_format(
                count($this->selectedMemberIds) * self::INSURANCE_AMOUNT_PER_MEMBER,
                2,
                '.',
                ''
            );
        }

        $validated = $this->validate([
            'fee_id' => ['required', 'string', 'exists:membership_fees,FeeID'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'disbursement_date' => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        if ($this->feeOptions->firstWhere('FeeID', $validated['fee_id']) === null) {
            $this->addError('fee_id', 'This source fund is not Active.');

            return null;
        }

        if ($this->isInsuranceFund) {
            $eligibleCount = $this->eligibleInsuranceMembers
                ->whereIn('UserID', $this->selectedMemberIds)
                ->count();

            if ($eligibleCount !== count($this->selectedMemberIds)) {
                $this->addError(
                    'selectedMemberIds',
                    'One or more selected members are no longer eligible. Please review your selection.'
                );

                return null;
            }
        }

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
        // if (! $this->authorizeCreateOrAbort()) {
        //     return null;
        // }

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
        // if (! $this->authorizeCreateOrAbort()) {
        //     return;
        // }

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

        try {
            DB::transaction(function () use ($validated) {

                $insuranceMembers = collect();

                if ($this->isInsuranceFund) {
                    // Lock selected member rows to prevent concurrent changes.
                    $insuranceMembers = User::query()
                        ->whereIn('UserID', $this->selectedMemberIds)
                        ->where('RoleID', 8)
                        ->where('status', 'Active')
                        ->orderByDesc('UserID')
                        ->lockForUpdate()
                        ->get();

                    if ($insuranceMembers->count() !== count($this->selectedMemberIds)) {
                        throw new \RuntimeException(
                            'Some selected members are no longer active. Please review your selection.'
                        );
                    }

                    $alreadyInsured = DB::table('cocogen_members_insurance')
                        ->whereIn('UserID', $this->selectedMemberIds)
                        ->exists();

                    if ($alreadyInsured) {
                        throw new \RuntimeException(
                            'Some selected members already have insurance records. Please reopen the member selection.'
                        );
                    }

                    // Recalculate amount from the locked member collection.
                    $this->amount = number_format(
                        $insuranceMembers->count() * self::INSURANCE_AMOUNT_PER_MEMBER,
                        2,
                        '.',
                        ''
                    );
                }

                // Recheck available balance inside the transaction.
                $fund = $this->selectedFund;

                if (! $fund || (float) $this->amount > $fund['available'] + 0.005) {
                    throw new \RuntimeException('Insufficient fund available.');
                }

                $recordDate = $validated['disbursement_date'] ?? null;

                $disbursement = Disbursement::create([
                    'FeeID' => $validated['fee_id'],
                    'amount' => $this->amount,
                    'remarks' => $validated['remarks'] ?? null,
                    'CreatedBy' => Auth::id(),
                    'created_at' => $recordDate
                        ? Carbon::parse($recordDate.' '.now()->format('H:i:s'))
                        : now(),
                ]);

                // Insert insurance records for every selected member.
                if ($this->isInsuranceFund) {
                    $now = now();

                    $insuranceRows = $insuranceMembers->map(function (User $user) use ($now) {
                        return [
                            'UserID' => $user->UserID,

                            'member_name' => trim(implode(' ', array_filter([
                                $user->firstname,
                                $user->middlename,
                                $user->lastname,
                            ]))),

                            'beneficiary_name' => null,

                            'address' => $user->address,
                            'birthdate' => $user->birthdate,
                            'civil_status' => $user->civilstatus,
                            'gender' => $user->gender,
                            'occupation' => $user->occupation,
                            'contact_number' => $user->contactno,

                            'plan' => self::INSURANCE_PLAN,
                            'amount' => self::INSURANCE_AMOUNT_PER_MEMBER,

                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    })->all();

                    DB::table('cocogen_members_insurance')->insert($insuranceRows);
                }
            });

            $this->dispatch('swal:success', [
                'title' => 'Disbursement Saved',
                'message' => $this->isInsuranceFund
                    ? 'Disbursement and insurance records for '.$this->insuranceMemberCount.' members were saved successfully.'
                    : 'The disbursement was recorded.',
            ]);

            $this->closeModal();

            unset(
                $this->feeCards,
                $this->totalAvailable,
                $this->totalCollected,
                $this->totalDisbursed,
                $this->totalWallet,
                $this->rows,
                $this->selectedFund
            );

        } catch (\Throwable $e) {
            report($e);

            $this->dispatch('swal:error', [
                'title' => 'Unable to Save',
                'message' => $e instanceof \RuntimeException
                    ? $e->getMessage()
                    : 'An error occurred while saving the disbursement and insurance records.',
            ]);
        }
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

    /**
     * Whether the selected source fund is Insurance.
     */
    #[Computed]
    public function isInsuranceFund(): bool
    {
        return $this->fee_id === self::INSURANCE_FEE_ID;
    }

    /**
     * Active members who do not yet have an insurance record.
     * Ordered by UserID descending.
     */
    #[Computed]
    public function eligibleInsuranceMembers()
    {
        return User::query()
            ->where('RoleID', 8)
            ->where('status', 'Active')
            ->whereNotIn('UserID', function ($query) {
                $query->select('UserID')
                    ->from('cocogen_members_insurance');
            })
            ->orderByDesc('UserID')
            ->get([
                'UserID',
                'firstname',
                'middlename',
                'lastname',
                'address',
                'birthdate',
                'civilstatus',
                'gender',
                'occupation',
                'contactno',
            ]);
    }

    /**
     * Currently selected eligible members.
     */
    #[Computed]
    public function selectedInsuranceMembers()
    {
        if (! $this->isInsuranceFund || empty($this->selectedMemberIds)) {
            return collect();
        }

        return User::query()
            ->where('RoleID', 8)
            ->where('status', 'Active')
            ->whereIn('UserID', $this->selectedMemberIds)
            ->whereNotIn('UserID', function ($query) {
                $query->select('UserID')
                    ->from('cocogen_members_insurance');
            })
            ->orderByDesc('UserID')
            ->get([
                'UserID',
                'firstname',
                'middlename',
                'lastname',
            ]);
    }

    /**
     * Total number of selected members.
     */
    #[Computed]
    public function insuranceMemberCount(): int
    {
        return $this->selectedInsuranceMembers->count();
    }

    /**
     * Total insurance coverage.
     */
    #[Computed]
    public function insuranceTotalCoverage(): float
    {
        return round(
            $this->insuranceMemberCount * self::INSURANCE_AMOUNT_PER_MEMBER,
            2
        );
    }

    /**
     * Open the member selection modal.
     */
    public function openInsuranceMembersModal(): void
    {
        if (! $this->isInsuranceFund) {
            return;
        }

        unset($this->eligibleInsuranceMembers);

        $this->showInsuranceMembersModal = true;
    }

    /**
     * Close the member selection modal.
     */
    public function closeInsuranceMembersModal(): void
    {
        $this->showInsuranceMembersModal = false;
    }

    /**
     * Select every eligible member.
     */
    public function selectAllInsuranceMembers(): void
    {
        $this->selectedMemberIds = $this->eligibleInsuranceMembers
            ->pluck('UserID')
            ->map(fn ($id) => (string) $id)
            ->all();

        $this->syncInsuranceAmount();
    }

    /**
     * Clear all selected members.
     */
    public function clearInsuranceMembers(): void
    {
        $this->selectedMemberIds = [];

        $this->syncInsuranceAmount();
    }

    /**
     * Keep the amount synchronized with the selection.
     */
    public function updatedSelectedMemberIds(): void
    {
        $this->syncInsuranceAmount();
    }

    /**
     * Update the disbursement amount automatically.
     */
    private function syncInsuranceAmount(): void
    {
        unset($this->selectedInsuranceMembers);
        unset($this->insuranceMemberCount);
        unset($this->insuranceTotalCoverage);
        unset($this->selectedFund);
        unset($this->feeCards);
        unset($this->totalAvailable);

        if ($this->isInsuranceFund) {
            $this->amount = number_format(
                $this->insuranceTotalCoverage,
                2,
                '.',
                ''
            );
        }
    }

    /**
     * Reset insurance selection when changing source fund.
     */
    public function updatedFeeId(): void
    {
        $this->selectedMemberIds = [];
        $this->showInsuranceMembersModal = false;

        unset(
            $this->selectedInsuranceMembers,
            $this->eligibleInsuranceMembers,
            $this->insuranceMemberCount,
            $this->insuranceTotalCoverage,
            $this->selectedFund,
            $this->feeCards,
            $this->totalAvailable
        );

        if ($this->isInsuranceFund) {
            $this->amount = '0.00';
        }
    }

    public function render()
    {
        return view('livewire.admin-pages.transaction-list.disbursements');
    }
}
