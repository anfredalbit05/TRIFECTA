<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Models\Activation;
use App\Models\Beneficiary;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Ledger;
use App\Models\MembershipFee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\ActivationHistory;
use Illuminate\Support\Facades\Auth;
use App\Mail\ActivationRejectedMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\InviteeApprovedMail;

class ActivationReviewModal extends Component
{
    public bool $open = false;
    public ?int $selectedUserID = null;
    public ?User $member = null;
    public ?Activation $activation = null;
    public ?User $referralUser = null;
    public array $beneficiaries = [];
    public string $remarks = '';
    public bool $isRejecting = false;

    /*
    |--------------------------------------------------------------------------
    | Open Review Modal
    |--------------------------------------------------------------------------
    */

    #[On('open-activation-review')]
    public function openModal($UserID): void
    {
        Log::info('Opening activation review modal.', ['UserID' => $UserID]);

        $this->selectedUserID = (int) $UserID;

        $this->member = User::find($this->selectedUserID);

        /*
        |--------------------------------------------------------------------------
        | Find Pending Activation by UserID
        |--------------------------------------------------------------------------
        */
        $this->activation = Activation::where('UserID', $this->selectedUserID)
            ->where('status', 'Pending')
            ->latest('ActivationID')
            ->first();

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

    /*
    |--------------------------------------------------------------------------
    | Approve Activation
    |--------------------------------------------------------------------------
    */

    public function approve(): void
    {
        if (!$this->activation || !$this->selectedUserID) {
            Log::warning('Approval failed: activation or selected user missing.');
            return;
        }

        DB::transaction(function () {

            Log::info('Starting approval transaction for ActivationID: ' . $this->activation->ActivationID);

            // 1. Update Activation Status
            $this->activation->update([
                'status' => 'Approved',
            ]);

            // Get CC Admins
            $adminEmails = User::join('roles', 'users.RoleID', '=', 'roles.RoleID')
                ->whereIn('roles.rolename', ['Super Administrator', 'Administrator'])
                ->whereNotNull('users.email')
                ->pluck('users.email')
                ->toArray();

            // 2. Activate Member User Account
            User::where('UserID', $this->selectedUserID)->update([
                'status' => 'Active',
            ]);

            $activatedUser = User::find($this->selectedUserID);

            /*
            |--------------------------------------------------------------------------
            | Record Membership Fees (MF) breakdown into Ledger for the Activated User
            |--------------------------------------------------------------------------
            */
            $membershipFees = MembershipFee::all();

            foreach ($membershipFees as $fee) {
                Ledger::create([
                    'RefID'        => $fee->FeeID,
                    'amount'       => $fee->Cost,
                    'Source'       => 'MF', // Membership Fee
                    'ActivationID' => $this->activation->ActivationID,
                    'status'       => 'Completed',
                ]);

                Log::info("Membership Fee ledger entry [{$fee->FeeName}] written with RefID: {$fee->FeeID} and amount: {$fee->Cost}");
            }

            /*
            |--------------------------------------------------------------------------
            | Multi-Tier Referral Commission Structure with Dynamic Sponsoring Count
            |--------------------------------------------------------------------------
            */
            $currentUplineCode = $this->activation->upline_code;
            $level = 1;
            $maxLevels = 6;

            $drFee = MembershipFee::where('FeeName', 'Direct Referral')->value('Cost') ?? 400.00;

            // Standard hierarchy level rates (fallback if needed)
            $commissionRates = [
                1 => (float) $drFee,
                2 => 50.00,
                3 => 40.00,
                4 => 30.00,
                5 => 20.00,
                6 => 10.00,
            ];

            Log::info('Initial Upline Code for Commission Loop: ' . ($currentUplineCode ?? 'NONE'));

            while ($currentUplineCode && $level <= $maxLevels) {
                $uplineUser = User::where('referralCode', $currentUplineCode)->first();

                if (!$uplineUser) {
                    Log::warning("Upline user not found for referral code: {$currentUplineCode} at Level {$level}");
                    break;
                }

                $commissionAmount = 0.00;
                $sourceType = ($level === 1) ? 'DR' : 'OV';

                if ($level === 1) {
                    // Level 1 always gets the Direct Referral amount
                    $commissionAmount = $commissionRates[1];
                } else {
                    // For higher levels (e.g., Level 2 like User 1), check how many direct invitees
                    // the immediate sponsor (e.g., User 2) has successfully registered up to now.
                    $sponsorUserID = User::where('referralCode', $this->activation->upline_code)->value('UserID');

                    // Count how many approved activations belong to this sponsor's direct referral code
                    $directInviteCount = Activation::where('status', 'Approved')
                        ->where('upline_code', $this->activation->upline_code)
                        ->count();

                    // Map direct invite sequence count to decreasing commission rates:
                    // 1st invite = 50, 2nd = 40, 3rd = 30, 4th = 20, 5th = 10, 6th+ = 0
                    $dynamicOverrideRates = [
                        1 => 50.00,
                        2 => 40.00,
                        3 => 30.00,
                        4 => 20.00,
                        5 => 10.00,
                    ];

                    $commissionAmount = $dynamicOverrideRates[$directInviteCount] ?? 0.00;
                }

                Log::info("Processing Level {$level}: Upline UserID {$uplineUser->UserID}, Commission: {$commissionAmount}");

                if ($commissionAmount > 0) {
                    // Create Wallet Entry for Upline
                    Wallet::create([
                        'UserID'       => $uplineUser->UserID,
                        'ActivationID' => $this->activation->ActivationID,
                        'amount'       => $commissionAmount,
                        'source'       => $sourceType,
                        'status'       => 'Active',
                    ]);

                    // Create Ledger Entry for Upline Commission/Override
                    Ledger::create([
                        'RefID'        => $uplineUser->UserID,
                        'amount'       => $commissionAmount,
                        'Source'       => $sourceType,
                        'ActivationID' => $this->activation->ActivationID,
                        'status'       => 'Completed',
                    ]);

                    Log::info("Ledger & Wallet successfully written for UserID: {$uplineUser->UserID} at Level {$level} as type [{$sourceType}] with amount: {$commissionAmount}");
                } else {
                    Log::info("No commission generated for UserID: {$uplineUser->UserID} at Level {$level} due to invite threshold limit.");
                }

                $currentUplineCode = $uplineUser->uplineCode ?? null;
                $level++;
            }

            // Send Email to Activated User
            if ($activatedUser && filled($activatedUser->email)) {
                try {
                    Mail::raw(
                        "Congratulations!\n\nYour account has been approved successfully.\nYou can now log in and access all features.",
                        function ($message) use ($activatedUser, $adminEmails) {
                            $message->to($activatedUser->email)
                                ->cc($adminEmails)
                                ->subject('Account Approved');
                        }
                    );
                } catch (\Throwable $e) {
                    Log::error('Failed to send approval email to activated user.', ['error' => $e->getMessage()]);
                }
            }

            // Send Email to Upline / Referral User
            if ($this->referralUser && filled($this->referralUser->email)) {
                try {
                    Mail::to($this->referralUser->email)
                        ->cc($adminEmails)
                        ->send(new InviteeApprovedMail($this->referralUser, $activatedUser, $this->activation));
                } catch (\Throwable $e) {
                    Log::error('Failed to send approval email to upline.', ['error' => $e->getMessage()]);
                }
            }
        });

        $this->dispatch('members-refresh');
        $this->dispatch('swal:success', title: 'Success!', message: 'Activation approved successfully.');
        $this->closeModal();
    }

    /*
    |--------------------------------------------------------------------------
    | Reject Activation Request
    |--------------------------------------------------------------------------
    */

    public function confirmReject(): void
    {
        $this->dispatch('swal:reject-confirm', [
            'title' => 'Reject activation?',
            'text' => 'Please provide the reason for rejection.',
        ]);
    }

    #[On('submit-rejection')]
    public function submitRejection($remarks): void
    {
        $this->remarks = is_array($remarks) ? ($remarks['remarks'] ?? '') : $remarks;
        $this->rejectActivation();
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

            if (!$activation) {
                return;
            }

            DB::transaction(function () use ($activation) {
                $activation->update([
                    'status' => 'Rejected',
                ]);

                ActivationHistory::create([
                    'ActivationID' => $activation->ActivationID,
                    'AdminID' => Auth::id(),
                    'status' => 'Rejected',
                    'remarks' => $this->remarks,
                ]);

                User::where('UserID', $this->selectedUserID)->update([
                    'status' => 'Inactive',
                ]);

                $user = User::find($this->selectedUserID);

                if ($user && filled($user->email)) {
                    Mail::to($user->email)->send(
                        new ActivationRejectedMail(
                            $user,
                            $activation,
                            $this->beneficiaries,
                            $this->remarks
                        )
                    );
                }
            });

            $this->dispatch('swal:success', title: 'Rejected!', message: 'Activation rejected successfully.');
            $this->closeModal();

        } finally {
            $this->isRejecting = false;
        }
    }

    public function closeModal(): void
    {
        $this->open = false;
        $this->selectedUserID = null;
        $this->member = null;
        $this->activation = null;
        $this->referralUser = null;
        $this->beneficiaries = [];
        $this->remarks = '';
    }

    public function render()
    {
        return view('livewire.admin-pages.masterlist.activation-review-modal');
    }
}
