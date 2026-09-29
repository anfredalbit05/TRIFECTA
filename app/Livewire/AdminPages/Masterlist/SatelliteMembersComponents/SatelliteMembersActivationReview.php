<?php

namespace App\Livewire\AdminPages\Masterlist\SatelliteMembersComponents;

use App\Models\Activation;
use App\Models\ActivationHistory;
use App\Models\Beneficiary;
use App\Models\MembershipFee;
use App\Models\Privilege;
use App\Models\Satellite;
use App\Models\Setting;
use App\Models\User;
use App\Models\Withdrawal;
use App\Services\ActivationApprovalService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class SatelliteMembersActivationReview extends Component
{
    public bool $open = false;

    public ?int $selectedUserID = null;

    public ?User $member = null;

    public ?Satellite $satellite = null;

    public ?Activation $activation = null;

    public ?User $referralUser = null;

    public array $beneficiaries = [];

    public string $remarks = '';

    public bool $isRejecting = false;

    /*
    | True only when the signed-in user holds satellite-members update/delete
    | privileges (admins). Members reviewing their OWN request can view the
    | modal but never see or trigger the approve/reject actions.
    */
    public bool $isPrivileged = false;

    public bool $gcashConvenienceFeeEnabled = false;

    public bool $paymayaConvenienceFeeEnabled = false;

    public float $totalMembershipFeesCost = 0.00;

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

        $this->totalMembershipFeesCost = (float) MembershipFee::sum('Cost');
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

    #[On('open-satellite-activation-review')]
    public function openModal(int $UserID, $activationId = null): void
    {
        Log::info('Opening satellite activation review modal.', [
            'UserID' => $UserID,
            'activationId' => $activationId,
        ]);

        // =====================================================
        // ONLY ROLE ID 8 — OR THE OWNER REVIEWING THEIR OWN REQUEST —
        // AND ADMINS ONLY FROM THE TRANSACTION LIST / MEMBER TABLE
        // =====================================================

        $isOwnerViewing = (int) $UserID === (int) Auth::id();

        if (! $isOwnerViewing) {
            $hasUpdatePrivilege = Privilege::where('RoleID', Auth::user()?->RoleID)
                ->where('ModuleID', 2)
                ->where(fn ($q) => $q->where('can_update', 1)->orWhere('can_delete', 1))
                ->exists();

            $isSuperAdmin = (int) (Auth::user()?->RoleID ?? 0) === 1;

            if (! ($isSuperAdmin || $hasUpdatePrivilege)) {
                Log::warning('Satellite activation review blocked: caller lacks privileges.', [
                    'UserID' => $UserID,
                    'caller' => Auth::id(),
                ]);

                $this->dispatch('swal:error', [
                    'title' => 'Not Allowed',
                    'text' => 'You do not have permission to review activation requests.',
                ]);

                return;
            }
        }

        $this->member = User::where('UserID', $UserID)
            ->when(! $isOwnerViewing, fn ($q) => $q->where('RoleID', 8))
            ->first();

        if (! $this->member) {
            Log::warning(
                'Satellite activation review blocked: invalid satellite member.',
                [
                    'UserID' => $UserID,
                ]
            );

            $this->selectedUserID = null;
            $this->member = null;
            $this->satellite = null;

            $this->dispatch('swal:error', [
                'title' => 'Invalid Member',
                'text' => 'The selected user is not a satellite member.',
            ]);

            return;
        }

        /*
        | Approve/reject is an ADMIN decision on a SATELLITE MEMBER (RoleID 8).
        | The owner (member) reviewing their own request gets a read-only modal;
        | the buttons are also hidden in the blade.
        */
        $this->isPrivileged = (int) $this->member->RoleID === 8
            && Auth::user()
            && ((int) Auth::user()->RoleID === 1
                || Privilege::where('RoleID', Auth::user()->RoleID)
                    ->where('ModuleID', 2)
                    ->where(fn ($q) => $q->where('can_update', 1)->orWhere('can_delete', 1))
                    ->exists());

        // =====================================================
        // SET SELECTED USER
        // =====================================================

        $this->selectedUserID = (int) $this->member->UserID;

        // =====================================================
        // LOAD PAYMENT SETTINGS
        // =====================================================

        $this->loadPaymentSettings();

        // =====================================================
        // FIND ACTIVATION TO REVIEW. An explicit activationId (Transaction
        // List row click) loads EXACTLY that row; otherwise Pending first,
        // else the latest Pending/Rejected request.
        // =====================================================

        $this->activation = Activation::where(
            'UserID',
            $this->selectedUserID
        )
            ->when(
                filled($activationId),
                fn ($q) => $q->where('ActivationID', (int) $activationId),
                fn ($q) => $q->whereIn('status', ['Pending', 'Rejected'])
                    ->orderByRaw("FIELD(status, 'Pending', 'Rejected')")
            )
            ->latest('ActivationID')
            ->first();

        if (! $this->activation) {
            Log::warning(
                'No pending activation found for satellite member.',
                [
                    'UserID' => $this->selectedUserID,
                ]
            );

            $this->selectedUserID = null;
            $this->member = null;
            $this->satellite = null;
            $this->activation = null;

            $this->dispatch('swal:error', [
                'title' => 'No Activation Request',
                'text' => 'This satellite member does not have a pending or rejected activation request.',
            ]);

            return;
        }

        // =====================================================
        // LOAD SATELLITE USING ACTIVATION'S satelliteID
        // =====================================================

        $this->satellite = Satellite::where(
            'satelliteID',
            $this->activation->satelliteID
        )->first();

        if (! $this->satellite) {
            Log::warning(
                'Satellite activation review failed: satellite record not found.',
                [
                    'UserID' => $this->selectedUserID,
                    'ActivationID' => $this->activation->ActivationID,
                    'satelliteID' => $this->activation->satelliteID,
                ]
            );

            $this->selectedUserID = null;
            $this->member = null;
            $this->satellite = null;
            $this->activation = null;

            $this->dispatch('swal:error', [
                'title' => 'Satellite Not Found',
                'text' => 'No satellite record was found for this activation.',
            ]);

            return;
        }

        // =====================================================
        // LOAD REFERRAL / UPLINE
        // =====================================================

        $this->referralUser = null;

        if (filled($this->activation->upline_code)) {
            $this->referralUser = User::where(
                'referralCode',
                trim($this->activation->upline_code)
            )->first();
        }

        // =====================================================
        // LOAD MEMBER BENEFICIARIES
        // =====================================================

        $this->beneficiaries = Beneficiary::where('UserID', $this->selectedUserID)
            ->where('status', true)
            ->orderBy('BeneficiaryID')
            ->get()
            ->toArray();

        // =====================================================
        // RESET MODAL STATE
        // =====================================================

        $this->remarks = '';
        $this->isRejecting = false;
        $this->open = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Approve Activation
    |--------------------------------------------------------------------------
    | Privilege gates stay here (the modal owns the UI); the atomic approval
    | writes + notifications live in ActivationApprovalService, shared with
    | the regular flavor and the Transaction List tab.
    |--------------------------------------------------------------------------
    */

    public function approve(): void
    {
        /*
        | Approve/reject is admin-only. A member reviewing their own request
        | (opened via the member Satellite page) can never trigger this.
        */
        if (! $this->isPrivileged) {
            Log::warning('Satellite approval blocked: user lacks satellite-members privileges.', [
                'UserID' => Auth::id(),
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'Only administrators can approve satellite activations.',
            ]);

            return;
        }

        if (! $this->activation || ! $this->selectedUserID) {
            Log::warning(
                'Satellite approval failed: activation or selected user ID is missing.'
            );

            return;
        }

        // =====================================================
        // STRICT SATELLITE MEMBER CHECK
        // =====================================================

        $member = User::where('UserID', $this->selectedUserID)
            ->where('RoleID', 8)
            ->first();

        if (! $member) {
            Log::warning(
                'Satellite approval blocked: invalid satellite member.',
                [
                    'UserID' => $this->selectedUserID,
                ]
            );

            $this->dispatch('swal:error', [
                'title' => 'Invalid Member',
                'text' => 'This user is not a satellite member.',
            ]);

            return;
        }

        if ($this->activation->status !== 'Pending') {
            Log::warning(
                'Satellite approval aborted: activation is no longer pending.',
                [
                    'ActivationID' => $this->activation->ActivationID,
                    'status' => $this->activation->status,
                ]
            );

            $this->dispatch('swal:error', [
                'title' => 'Already Processed',
                'text' => 'This activation request has already been processed.',
            ]);

            return;
        }

        $this->loadPaymentSettings();

        // =====================================================
        // SHARED APPROVAL (satellite commission flavor)
        // =====================================================

        $approved = app(ActivationApprovalService::class)->approve(
            $this->activation,
            $this->selectedUserID,
            ActivationApprovalService::TYPE_SATELLITE
        );

        if (! $approved) {
            Log::warning(
                'Satellite approval aborted: activation is no longer pending.',
                [
                    'ActivationID' => $this->activation->ActivationID,
                ]
            );

            $this->dispatch('swal:error', [
                'title' => 'Already Processed',
                'text' => 'This activation request has already been processed.',
            ]);

            return;
        }

        // UI Events
        $this->dispatch('members-refresh');
        $this->broadcastBadgeCounts();
        $this->dispatch('swal:success', title: 'Success!', message: 'Activation approved successfully.');
        $this->closeModal();
    }

    /*
    |--------------------------------------------------------------------------
    | Resubmit a REJECTED activation (back to Pending)
    |--------------------------------------------------------------------------
    | Same rule as the regular modal: reopening the same row keeps the full
    | history trail instead of creating a duplicate request.
    |--------------------------------------------------------------------------
    */

    public function confirmResubmit(): void
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

        $this->dispatch('txn-modal-resubmit-activation', activationId: $activation->ActivationID, modal: 'satellite');
    }

    #[On('txn-resubmit-activation')]
    public function resubmitActivation(): void
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

        Log::info('Satellite activation resubmitted.', [
            'ActivationID' => $activation->ActivationID,
            'UserID' => $activation->UserID,
            'reviewed_by' => Auth::id(),
        ]);

        $this->activation->status = 'Pending';

        $this->dispatch('members-refresh');
        $this->broadcastBadgeCounts();
        $this->dispatch('swal:success', title: 'Resubmitted!', message: 'Activation is back to Pending for review.');
    }

    /*
    |--------------------------------------------------------------------------
    | Reject Activation Request
    |--------------------------------------------------------------------------
    | Both confirmations are plain browser events handled by the shared
    | sweet-alert-handler (global livewire:init script). Inline @script
    | handlers proved unreliable in this app — when the script effect fails
    | to register, onclick="confirmApprove()" throws and NO dialog appears.
    |--------------------------------------------------------------------------
    */

    public function confirmApprove(): void
    {
        if (! $this->isPrivileged) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'Only administrators can approve satellite activations.',
            ]);

            return;
        }

        if (! $this->activation || $this->activation->status !== 'Pending') {
            $this->dispatch('swal:error', [
                'title' => 'Already Processed',
                'text' => 'This activation request has already been processed.',
            ]);

            return;
        }

        $this->dispatch('txn-modal-approve-activation',
            activationId: $this->activation->ActivationID,
            memberName: trim(($this->member?->lastname ?? '').' '.($this->member?->firstname ?? '')) ?: 'this member',
            amount: number_format((float) $this->activation->amount, 2),
            modal: 'satellite',
        );
    }

    public function confirmReject(): void
    {
        if (! $this->isPrivileged) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'Only administrators can reject satellite activations.',
            ]);

            return;
        }

        if (! $this->activation || $this->activation->status !== 'Pending') {
            $this->dispatch('swal:error', [
                'title' => 'Already Processed',
                'text' => 'This activation request has already been processed.',
            ]);

            return;
        }

        $this->dispatch('txn-modal-reject-activation', activationId: $this->activation->ActivationID, modal: 'satellite');
    }

    #[On('submit-rejection')]
    public function submitRejection($remarks): void
    {
        $this->remarks = is_array($remarks) ? ($remarks['remarks'] ?? '') : $remarks;
        $this->rejectActivation();
    }

    public function rejectActivation(): void
    {
        /*
        | Same admin-only gate as approve() — owners are read-only reviewers.
        */
        if (! $this->isPrivileged) {
            Log::warning('Satellite rejection blocked: user lacks satellite-members privileges.', [
                'UserID' => Auth::id(),
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'Only administrators can reject satellite activations.',
            ]);

            return;
        }

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

        $this->satellite = null;

        $this->activation = null;

        $this->referralUser = null;

        $this->beneficiaries = [];

        $this->remarks = '';

        $this->isRejecting = false;
    }

    public function render()
    {
        return view('livewire.admin-pages.masterlist.satellite-members-components.satellite-members-activation-review');
    }
}
