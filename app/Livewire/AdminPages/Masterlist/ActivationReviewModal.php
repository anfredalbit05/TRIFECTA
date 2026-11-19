<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Livewire\Concerns\AuthorizesModules;
use App\Models\Activation;
use App\Models\ActivationHistory;
use App\Models\Beneficiary;
use App\Models\MembershipFee;
use App\Models\Setting;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\EmploymentType;
use App\Models\MemberType;
use App\Services\ActivationApprovalService;
use App\Support\Modules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class ActivationReviewModal extends Component
{
    use AuthorizesModules;

    protected function module(): int
    {
        return Modules::TXN_ACTIVATIONS;
    }

    public bool $open = false;

    public ?int $selectedUserID = null;

    public ?User $member = null;

    public ?Activation $activation = null;

    public ?User $referralUser = null;

    public array $beneficiaries = [];

    public string $remarks = '';
    public string $employmentTypeName = '';

    public string $memberTypeName = '';

    public bool $isRejecting = false;

    public bool $gcashConvenienceFeeEnabled = false;

    public bool $paymayaConvenienceFeeEnabled = false;

    public float $totalMembershipFeesCost = 0.00;

    public string $policy_number = '';

    public string $receipt_number = '';

    public bool $policyNumberExists = false;

    public bool $receiptNumberExists = false;

    /*
    | Approve/reject/resubmit is an ADMIN decision. Members (or unprivileged
    | roles) can open the modal read-only — same rule as the satellite modal.
    */
    public bool $isPrivileged = false;

    /*
    |--------------------------------------------------------------------------
    | Open Review Modal
    |--------------------------------------------------------------------------
    */


    private function loadPaymentSettings(): void
    {
        $this->gcashConvenienceFeeEnabled = filter_var(
            Setting::where('name', 'gcash_convenience_fee_enabled')->value('value'),
            FILTER_VALIDATE_BOOLEAN
        );

        $this->paymayaConvenienceFeeEnabled = filter_var(
            Setting::where('name', 'paymaya_convenience_fee_enabled')->value('value'),
            FILTER_VALIDATE_BOOLEAN
        );

        // Calculate the fee using this activation's selected variant.
        $vid = $this->activation?->VID;

        $this->totalMembershipFeesCost = $vid
            ? (float) MembershipFee::where('VID', $vid)->sum('Cost')
            : 0.00;
    }


    #[On('open-activation-review')]
    public function openModal($UserID, $activationId = null): void
    {
        Log::info('Opening activation review modal.', ['UserID' => $UserID, 'activationId' => $activationId]);

        $this->selectedUserID = (int) $UserID;

        /*
        | Global module system (Transaction List - Activations): Super Admin
        | or a role holding ModuleID 9 with can_change_status OR can_delete.
        | Everyone else gets the details read-only (buttons hidden in blade).
        */
        $this->isPrivileged = $this->can('change_status') || $this->can('delete');

        $this->member = User::find($this->selectedUserID);

        /*
        |--------------------------------------------------------------------------
        | Find the activation to review. When an explicit activationId is given
        | (Transaction List row click) load EXACTLY that row regardless of
        | status; otherwise prefer the user's Pending request and fall back to
        | the latest Pending/Rejected one.
        |--------------------------------------------------------------------------
        */
        $this->activation = Activation::where('UserID', $this->selectedUserID)
            ->when(
                filled($activationId),
                fn ($q) => $q->where('ActivationID', (int) $activationId),
                fn ($q) => $q->whereIn('status', ['Pending', 'Rejected'])
                    ->orderByRaw("FIELD(status, 'Pending', 'Rejected')")
            )
            ->latest('ActivationID')
            ->first();

        $this->loadTypeNames();
            // Load settings and the total for this activation's VID.
        $this->loadPaymentSettings();

        /*
        |--------------------------------------------------------------------------
        | Load Policy / Receipt Numbers
        |--------------------------------------------------------------------------
        */
        $this->policy_number = (string) ($this->activation?->policy_number ?? '');
        $this->receipt_number = (string) ($this->activation?->receipt_number ?? '');

        /*
        |--------------------------------------------------------------------------
        | Load Referral User
        |--------------------------------------------------------------------------
        */
        $this->referralUser = null;

        if ($this->activation && filled($this->activation->upline_code)) {
            $this->referralUser = User::where(
                'referralCode',
                trim($this->activation->upline_code)
            )->first();
        }

        /*
        |--------------------------------------------------------------------------
        | Load Member Beneficiaries
        |--------------------------------------------------------------------------
        */
        $this->beneficiaries = Beneficiary::where('UserID', $this->selectedUserID)
            ->where('status', true)
            ->orderBy('BeneficiaryID')
            ->get()
            ->toArray();

        $this->open = true;
    }

    public function checkActivationNumberDuplicates(): void
    {
        $policyNumber = strtoupper(trim((string) $this->policy_number));
        $receiptNumber = trim((string) $this->receipt_number);

        $activationID = $this->activation?->ActivationID;

        // Check policy number in ACTIVATIONS table.
        $policyExistsInActivations = false;

        if ($policyNumber !== '') {
            $policyExistsInActivations = Activation::query()
                ->where('policy_number', $policyNumber)
                ->when(
                    $activationID,
                    fn ($query) => $query->where(
                        'ActivationID',
                        '!=',
                        $activationID
                    )
                )
                ->exists();
        }

        // Check policy number in USERS table.
        // Exclude the currently selected member.
        $policyExistsInUsers = false;

        if ($policyNumber !== '') {
            $policyExistsInUsers = User::query()
                ->where('policy_number', $policyNumber)
                ->where('UserID', '!=', $this->selectedUserID)
                ->exists();
        }

        $this->policyNumberExists =
            $policyExistsInActivations || $policyExistsInUsers;

        // Receipt numbers are checked in ACTIVATIONS only.
        $this->receiptNumberExists = false;

        if ($receiptNumber !== '') {
            $this->receiptNumberExists = Activation::query()
                ->where('receipt_number', $receiptNumber)
                ->when(
                    $activationID,
                    fn ($query) => $query->where(
                        'ActivationID',
                        '!=',
                        $activationID
                    )
                )
                ->exists();
        }
    }

    /** Live duplicate feedback while the admin types (drives the inline warnings). */
    public function updatedPolicyNumber(): void
    {
        $this->checkActivationNumberDuplicates();
    }

    public function updatedReceiptNumber(): void
    {
        $this->checkActivationNumberDuplicates();
    }

    public function generatePolicyNumber(): void
    {
        // Single source of truth on the model: scans BOTH the activations and
        // users tables and starts the series at TRI-00550-TS2026-550.
        $this->policy_number = Activation::nextPolicyNumber();

        $this->resetValidation('policy_number');

        // Refresh the inline duplicate warnings for the generated value.
        $this->checkActivationNumberDuplicates();
    }

    public function getNextPolicyNumberProperty(): string
    {
        return Activation::nextPolicyNumber();
    }

    private function getNextPolicySequence(): int
    {
        // The next sequence the series may use: one past the highest issued
        // number across activations + users, starting at 550.
        return max(
            Activation::FIRST_POLICY_SEQUENCE,
            Activation::latestPolicySequence() + 1
        );
    }

    public function getNextReceiptNumberProperty(): string
    {
        return Activation::nextReceiptNumber();
    }

    public function generateReceiptNumber(): void
    {
        $this->receipt_number = Activation::nextReceiptNumber();

        $this->resetValidation('receipt_number');

        $this->checkActivationNumberDuplicates();
    }

    private function showApprovalValidationError(string $message): void
    {
        $this->dispatch(
            'swal:error',
            title: 'Cannot Approve Activation',
            message: $message
        );
    }

    /*
    | Keep the transaction-list tab badges in sync after any status change.
    */
    private function broadcastBadgeCounts(): void
    {
        $this->dispatch('txn-badges-updated',
            activations: Activation::query()->where('status', 'Pending')->count(),
            withdrawals: Withdrawal::query()->where('status', 'Pending')->count(),
        );
    }

    public function getNextPolicySequenceForJsProperty(): int
    {
        return $this->getNextPolicySequence();
    }

    /*
    |--------------------------------------------------------------------------
    | Member Type — TRIFECTA member vs Satellite member
    |--------------------------------------------------------------------------
    | Satellite members (RoleID 8) review through the satellite modal, but a
    | mixed statusFilter like the transaction list can route a RoleID 8 user
    | here too — so the modal labels the account type explicitly.
    |--------------------------------------------------------------------------
    */

    public function getMemberTypeProperty(): string
    {
        if ((int) ($this->member?->RoleID ?? 0) === 8) {
            return 'satellite';
        }

        return 'truelife';
    }

    /*
    |--------------------------------------------------------------------------
    | Approve Activation
    |--------------------------------------------------------------------------
    | All validation gates stay here (this modal owns the policy/receipt
    | number entry UI); the atomic approval writes + notifications live in
    | ActivationApprovalService, shared with the satellite flavor and the
    | Transaction List tab.
    |--------------------------------------------------------------------------
    */

    public function approve(): void
    {
        // ==========================================================
        // GUARD CLAUSES
        // ==========================================================

        // if (! $this->isPrivileged) {
        //     Log::warning('Activation approval blocked: user lacks privileges.', [
        //         'UserID' => Auth::id(),
        //     ]);

        //     $this->showApprovalValidationError(
        //         'Only administrators can approve activations.'
        //     );

        //     return;
        // }

        if (! $this->activation || ! $this->selectedUserID) {
            Log::warning(
                'Approval failed: Activation or selected user ID is missing.'
            );

            $this->showApprovalValidationError(
                'The activation request could not be found. Please close this window and try again.'
            );

            return;
        }

        // Refresh the activation from database so we validate
        // against the latest status/data.
        $this->activation = Activation::find(
            $this->activation->ActivationID
        );

        if (! $this->activation) {
            $this->showApprovalValidationError(
                'The activation request could not be found.'
            );

            return;
        }

        if ($this->activation->status === 'Approved') {
            Log::warning(
                'Approval aborted: Activation is already approved.',
                [
                    'activation_id' => $this->activation->ActivationID,
                ]
            );

            $this->showApprovalValidationError(
                'This activation has already been approved.'
            );

            return;
        }

        $this->loadPaymentSettings();

        // ==========================================================
        // FREE MEMBER — ONE-CLICK APPROVAL
        // ==========================================================
        // Free members were provisioned when their satellite was approved:
        // no payment was collected, so no policy or receipt number applies.
        // Approval completes their Pending membership-fee ledger and pays the
        // satellite owner the normal DR / TX / OV / PF stack.

        if ($this->activation->isFree) {
            $approved = app(ActivationApprovalService::class)->approve(
                $this->activation,
                $this->selectedUserID,
                ActivationApprovalService::TYPE_FREE
            );

            if (! $approved) {
                Log::warning(
                    'Free member approval aborted: no longer pending.',
                    [
                        'activation_id' => $this->activation->ActivationID,
                    ]
                );

                $this->showApprovalValidationError(
                    'This activation has already been processed.'
                );

                return;
            }

            $this->dispatch('members-refresh');
            $this->broadcastBadgeCounts();
            $this->dispatch('swal:success', title: 'Success!', message: 'Free member activated successfully.');
            $this->closeModal();

            return;
        }

        // ==========================================================
        // NORMALIZE INPUT
        // ==========================================================

        $this->policy_number = strtoupper(
            trim($this->policy_number)
        );

        $this->receipt_number = trim(
            $this->receipt_number
        );

        // ==========================================================
        // BASIC REQUIRED / FORMAT VALIDATION
        // ==========================================================

        try {
        $this->validate([
            'policy_number' => [
                'required',
                'string',
                'max:100',
                'regex:/^TF-\d+-TF\d{4}-\d+$/',
            ],

            'receipt_number' => [
                'required',
                'string',
                'max:100',
                'regex:/^TF-\d+$/',
            ],
        ], [
            'policy_number.required' => 'The policy number is required.',

            'policy_number.regex' =>
                'The policy number must use the format TF-00001-TF' .
                now()->year . '-00001.',

            'receipt_number.required' => 'The receipt number is required.',

            'receipt_number.regex' =>
                'The receipt number must use the format TF-00001.',
        ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->all();

            $message = $errors[0]
                ?? 'Please correct the required fields before approving.';

            $this->showApprovalValidationError($message);

            return;
        }

        // ==========================================================
        // PARSE POLICY NUMBER
        // ==========================================================

        if (! preg_match(
            '/^TF-(\d+)-TF(\d{4})-(\d+)$/',
            $this->policy_number,
            $matches
        )) {
            $this->addError(
                'policy_number',
                'The policy number format is invalid.'
            );

            $this->showApprovalValidationError(
                'The policy number format is invalid.'
            );

            return;
        }

        $policyFirstSequence = (int) $matches[1];
        $policyYear = (int) $matches[2];
        $policyFinalSequence = (int) $matches[3];

        $currentYear = now()->year;

        // ==========================================================
        // POLICY YEAR
        // ==========================================================

        if ($policyYear !== $currentYear) {
            $message =
                "The policy number must use the current year {$currentYear}.";

            $this->addError(
                'policy_number',
                $message
            );

            $this->showApprovalValidationError($message);

            return;
        }

        // ==========================================================
        // POLICY SEQUENCES MUST MATCH
        // ==========================================================

        if ($policyFirstSequence !== $policyFinalSequence) {
            $message =
                'The first and last sequence numbers of the policy number must match.';

            $this->addError(
                'policy_number',
                $message
            );

            $this->showApprovalValidationError($message);

            return;
        }

        // ==========================================================
        // DUPLICATE POLICY NUMBER
        // ==========================================================

        $policyExists = Activation::query()
            ->whereRaw(
                'UPPER(TRIM(policy_number)) = ?',
                [strtoupper(trim($this->policy_number))]
            )
            ->where(
                'ActivationID',
                '!=',
                $this->activation->ActivationID
            )
            ->exists();

        if ($policyExists) {
            $message = 'This policy number has already been used.';

            $this->addError('policy_number', $message);

            $this->dispatch(
                'swal:error',
                title: 'Cannot Approve Activation',
                message: $message
            );

            return;
        }

        // ==========================================================
        // POLICY NUMBER CANNOT JUMP AHEAD
        // ==========================================================

        $nextPolicySequence = $this->getNextPolicySequence();

        if ($policyFirstSequence > $nextPolicySequence) {
            $formattedNextPolicy = str_pad(
                $nextPolicySequence,
                5,
                '0',
                STR_PAD_LEFT
            );

            $message =
                'The policy number cannot jump ahead. '.
                "The next available sequence is {$formattedNextPolicy}.";

            $this->addError(
                'policy_number',
                $message
            );

            $this->showApprovalValidationError($message);

            return;
        }

        // ==========================================================
        // RECEIPT NUMBER BASIC VALIDATION
        // ==========================================================

        preg_match(
            '/^TF-(\d+)$/',
            $this->receipt_number,
            $receiptMatches
        );

        $receiptNumber = (int) $receiptMatches[1];

        // ==========================================================
        // DUPLICATE RECEIPT NUMBER
        // ==========================================================

        $receiptExists = Activation::query()
            ->whereRaw(
                'TRIM(receipt_number) = ?',
                [trim($this->receipt_number)]
            )
            ->where(
                'ActivationID',
                '!=',
                $this->activation->ActivationID
            )
            ->exists();

        if ($receiptExists) {
            $message = 'This receipt number has already been issued.';

            $this->addError('receipt_number', $message);

            $this->dispatch(
                'swal:error',
                title: 'Cannot Approve Activation',
                message: $message
            );

            return;
        }

        // ==========================================================
        // RECEIPT NUMBER CANNOT JUMP AHEAD
        // ==========================================================

        $nextReceiptValue = Activation::nextReceiptNumber();

        preg_match(
            '/^TF-(\d+)$/',
            $nextReceiptValue,
            $nextReceiptMatches
        );

        $nextReceiptNumber = (int) $nextReceiptMatches[1];

        if ($receiptNumber > $nextReceiptNumber) {
            $formattedNextReceipt = sprintf(
                'TF-%05d',
                $nextReceiptNumber
            );

            $message =
                'The receipt number cannot jump ahead. ' .
                "The next available receipt number is {$formattedNextReceipt}.";

            $this->addError('receipt_number', $message);

            $this->showApprovalValidationError($message);

            return;
        }

        // ==========================================================
        // EVERYTHING IS VALID — SHARED APPROVAL
        // ==========================================================

        // Only after ALL validation passes do we continue with the actual
        // approval transaction (ActivationApprovalService re-checks the
        // Pending status inside the row lock).
        // Satellite activations (RoleID 8) keep their own accounting flavor —
        // a mixed statusFilter can route one through this modal too.
        $type = filled($this->activation->satelliteID)
            ? ActivationApprovalService::TYPE_SATELLITE
            : ActivationApprovalService::TYPE_REGULAR;

        $approved = app(ActivationApprovalService::class)->approve(
            $this->activation,
            $this->selectedUserID,
            $type,
            [
                'policy_number' => $this->policy_number,
                'receipt_number' => $this->receipt_number,

                'cashier_name' => Setting::where('name', 'cashier_name')->value('value'),
                'cashier_signature' => Setting::where('name', 'cashier_signature')->value('value'),
            ]
        );

        if (! $approved) {
            Log::warning(
                'Approval aborted: Activation is no longer pending.',
                [
                    'activation_id' => $this->activation->ActivationID,
                ]
            );

            $this->showApprovalValidationError(
                'This activation has already been processed.'
            );

            return;
        }

        // 4. UI Events
        $this->dispatch('members-refresh');
        $this->broadcastBadgeCounts();
        $this->dispatch('swal:success', title: 'Success!', message: 'Activation approved successfully.');
        $this->closeModal();
    }

    /*
    |--------------------------------------------------------------------------
    | Reject Activation Request
    |--------------------------------------------------------------------------
    | Both confirmations are plain browser events handled by the shared
    | sweet-alert-handler (global livewire:init script). Inline @script
    | handlers proved unreliable in this app — when the script effect fails
    | to register, the onclick/Alpine handlers throw and NO dialog appears.
    |--------------------------------------------------------------------------
    */

    public function confirmApprove(): void
    {
        // if (! $this->isPrivileged) {
        //     $this->dispatch('swal:error', [
        //         'title' => 'Not Allowed',
        //         'text' => 'Only administrators can approve activations.',
        //     ]);

        //     return;
        // }

        $activation = $this->activation ? Activation::find($this->activation->ActivationID) : null;

        if (! $activation || $activation->status !== 'Pending') {
            $this->dispatch('swal:error', [
                'title' => 'Already Processed',
                'text' => 'This activation request has already been processed.',
            ]);

            return;
        }

        $this->dispatch('txn-modal-approve-activation',
            activationId: $activation->ActivationID,
            memberName: trim(($this->member?->lastname ?? '').' '.($this->member?->firstname ?? '')) ?: 'this member',
            amount: number_format((float) $activation->amount, 2),
            modal: 'regular',
        );
    }

    public function confirmReject(): void
    {
        // if (! $this->isPrivileged) {
        //     $this->dispatch('swal:error', [
        //         'title' => 'Not Allowed',
        //         'text' => 'Only administrators can reject activations.',
        //     ]);

        //     return;
        // }

        $activation = $this->activation ? Activation::find($this->activation->ActivationID) : null;

        if (! $activation || $activation->status !== 'Pending') {
            $this->dispatch('swal:error', [
                'title' => 'Already Processed',
                'text' => 'This activation request has already been processed.',
            ]);

            return;
        }

        $this->dispatch('txn-modal-reject-activation', activationId: $activation->ActivationID, modal: 'regular');
    }

    #[On('submit-rejection')]
    public function submitRejection($remarks): void
    {
        $this->remarks = is_array($remarks) ? ($remarks['remarks'] ?? '') : $remarks;
        $this->rejectActivation();
    }

    /*
    |--------------------------------------------------------------------------
    | Resubmit a REJECTED activation — open the ActivationModal
    |--------------------------------------------------------------------------
    | Clicking Resubmit does NOT ask for confirmation here: it opens the
    | activation form modal in reactivate mode (pre-filled from the rejected
    | request, submit button "ReActivate Account"). The typed confirmation
    | happens on submit inside that modal; filing it flips the SAME rejected
    | activation row back to Pending — no duplicate request is created.
    |--------------------------------------------------------------------------
    */

    public function openResubmitForm(): void
    {
        if (! $this->isPrivileged) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'Only administrators can resubmit activations.',
            ]);

            return;
        }

        $activation = Activation::where('ActivationID', $this->activation?->ActivationID)->first();

        if (! $activation || $activation->status !== 'Rejected') {
            $this->dispatch('swal:error', [
                'title' => 'Not Available',
                'text' => 'Resubmit is only available for rejected activations.',
            ]);

            return;
        }

        $userID = (int) $this->selectedUserID;

        $this->closeModal();

        $this->dispatch('open-rejected-activation', UserID: $userID);
    }

    public function confirmResubmit(): void
    {
        // if (! $this->isPrivileged) {
        //     $this->dispatch('swal:error', [
        //         'title' => 'Not Allowed',
        //         'text' => 'Only administrators can resubmit activations.',
        //     ]);

        //     return;
        // }

        $activation = Activation::where('ActivationID', $this->activation?->ActivationID)->first();

        if (! $activation || $activation->status !== 'Rejected') {
            $this->dispatch('swal:error', [
                'title' => 'Not Available',
                'text' => 'Resubmit is only available for rejected activations.',
            ]);

            return;
        }

        $this->dispatch('txn-modal-resubmit-activation', activationId: $activation->ActivationID, modal: 'regular');
    }

    #[On('txn-resubmit-activation')]
    public function resubmitActivation(): void
    {
        // if (! $this->isPrivileged) {
        //     $this->dispatch('swal:error', [
        //         'title' => 'Not Allowed',
        //         'text' => 'Only administrators can resubmit activations.',
        //     ]);

        //     return;
        // }

        $activation = Activation::where('ActivationID', $this->activation?->ActivationID)->first();

        if (! $activation || $activation->status !== 'Rejected') {
            $this->dispatch('swal:error', [
                'title' => 'Not Available',
                'text' => 'Resubmit is only available for rejected activations.',
            ]);

            return;
        }

        $admin = Auth::user();
        $adminName = trim(implode(' ', array_filter([
            $admin->firstname ?? '',
            $admin->middlename ?? '',
            $admin->lastname ?? '',
        ])));

        DB::transaction(function () use ($activation, $adminName) {
            $activation->update([
                'status' => 'Pending',
            ]);

            /*
            | activation_histories.status is enum('Approved','Rejected') —
            | record the resubmission as 'Rejected' (the state it leaves)
            | with the resubmit intent in the remarks.
            */
            ActivationHistory::create([
                'ActivationID' => $activation->ActivationID,
                'AdminID' => Auth::id(),
                'status' => 'Rejected',
                'remarks' => "Activation resubmitted for review by {$adminName}.",
            ]);
        });

        Log::info('Activation resubmitted.', [
            'ActivationID' => $activation->ActivationID,
            'UserID' => $activation->UserID,
            'reviewed_by' => Auth::id(),
        ]);

        $this->activation->status = 'Pending';

        $this->dispatch('members-refresh');
        $this->broadcastBadgeCounts();
        $this->dispatch('swal:success', title: 'Resubmitted!', message: 'Activation is back to Pending for review.');
    }

    public function rejectActivation(): void
    {
        if ($this->isRejecting) {
            return;
        }

        $this->isRejecting = true;

        try {
            $this->validate([
                'remarks' => ['required', 'string', 'max:1000'],
            ]);

            $activation = Activation::where('ActivationID', $this->activation?->ActivationID)
                ->latest()
                ->first();

            if (! $activation) {
                return;
            }

            app(ActivationApprovalService::class)->reject(
                $activation,
                (int) $this->selectedUserID,
                $this->remarks
            );

            $this->dispatch('members-refresh');
            $this->broadcastBadgeCounts();
            $this->dispatch('swal:success', title: 'Rejected!', message: 'Activation rejected successfully.');
            $this->closeModal();
        } finally {
            $this->isRejecting = false;
        }
    }

    public function closeModal(): void
    {
        $this->open = false;

        $this->isPrivileged = false;

        $this->selectedUserID = null;
        $this->member = null;
        $this->activation = null;
        $this->referralUser = null;
        $this->beneficiaries = [];
        $this->remarks = '';

        $this->policy_number = '';
        $this->receipt_number = '';

        $this->employmentTypeName = '';
        $this->memberTypeName = '';
    }

    private function loadTypeNames(): void
    {
        $employmentId = $this->activation?->employment_type_id
            ?: $this->member?->employment_type_id;

        $memberTypeId = $this->activation?->member_type_id
            ?: $this->member?->member_type_id;

        $this->employmentTypeName = $employmentId
            ? (string) EmploymentType::where('employment_type_id', $employmentId)->value('name')
            : '';

        $this->memberTypeName = $memberTypeId
            ? (string) MemberType::where('member_type_id', $memberTypeId)->value('name')
            : '';
    }

    public function render()
    {
        return view('livewire.admin-pages.masterlist.activation-review-modal');
    }
}
