<?php

namespace App\Livewire\AdminPages\Masterlist\SatelliteMembersComponents;

use App\Mail\ActivationApprovedMail;
use App\Mail\ActivationRejectedMail;
use App\Mail\InviteeApprovedMail;
use App\Models\Activation;
use App\Models\ActivationHistory;
use App\Models\Beneficiary;
use App\Models\Ledger;
use App\Models\MembershipFee;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Setting;
use App\Models\Satellite;

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

    #[On('open-satellite-activation-review')]
    public function openModal(int $UserID): void
    {
        Log::info('Opening satellite activation review modal.', [
            'UserID' => $UserID,
        ]);

        // =====================================================
        // ONLY ROLE ID 8 IS ALLOWED
        // =====================================================

        $this->member = User::where('UserID', $UserID)
            ->where('RoleID', 8)
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

        // =====================================================
        // SET SELECTED USER
        // =====================================================

        $this->selectedUserID = (int) $this->member->UserID;

        // =====================================================
        // LOAD PAYMENT SETTINGS
        // =====================================================

        $this->loadPaymentSettings();

        // =====================================================
        // FIND PENDING ACTIVATION
        // =====================================================

        $this->activation = Activation::where(
            'UserID',
            $this->selectedUserID
        )
            ->where('status', 'Pending')
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
                'title' => 'No Pending Activation',
                'text' => 'This satellite member does not have a pending activation request.',
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
    */

    /*
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

          // Get the currently authenticated admin user
            $admin = Auth::user();

            // Build the full name, safely handling any nullable middle name
            $adminName = trim(implode(' ', [
                $admin->firstname ?? '',
                $admin->middlename ?? '',
                $admin->lastname ?? ''
            ]));

            // Log the approval history with the admin's full name
            ActivationHistory::create([
                'ActivationID' => $this->activation->ActivationID,
                'AdminID' => Auth::id(),
                'status' => 'Approved',
                'remarks' => "Activation approved by {$adminName}.",
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


            // |--------------------------------------------------------------------------
            // | Record Membership Fees (MF) breakdown into Ledger for the Activated User
            // |--------------------------------------------------------------------------

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


            // |--------------------------------------------------------------------------
            // | Add PF (50.00) to the Direct Upline / Referral User Wallet & Ledger
            // |--------------------------------------------------------------------------

            if ($this->referralUser) {
                Wallet::create([
                    'UserID'       => $this->referralUser->UserID,
                    'ActivationID' => $this->activation->ActivationID,
                    'amount'       => 50.00,
                    'source'       => 'PF',
                    'status'       => 'Active',
                ]);

                Ledger::create([
                    'RefID'        => $this->referralUser->UserID,
                    'amount'       => -50.00,
                    'Source'       => 'PF',
                    'ActivationID' => $this->activation->ActivationID,
                    'status'       => 'Completed',
                ]);

                Log::info("PF entry (50.00) written for referral UserID: {$this->referralUser->UserID}");
            }


            // |--------------------------------------------------------------------------
            // | Multi-Tier Referral Commission Structure with Dynamic Sponsoring Count
            // |--------------------------------------------------------------------------

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

                    $negativeCommission = -$commissionAmount;

                    // Create Ledger Entry for Upline Commission/Override
                    Ledger::create([
                        'RefID'        => $uplineUser->UserID,
                        'amount'       => $negativeCommission,
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

            // Send Email to Activated User (Without CCing admins to prevent spam flagging)
            if ($activatedUser && filled($activatedUser->email)) {
                try {
                    $totalAmount = MembershipFee::sum('Cost');

                    Mail::to($activatedUser->email)
                        ->send(new ActivationApprovedMail($activatedUser, $this->activation, (float) $totalAmount));
                } catch (\Throwable $e) {
                    Log::error('Failed to send approval email to activated user.', ['error' => $e->getMessage()]);
                }
            }

            //    // Send Email to Activated User
            // if ($activatedUser && filled($activatedUser->email)) {
            //     try {
            //         $totalAmount = MembershipFee::sum('Cost');

            //         Mail::to($activatedUser->email)
            //             ->cc($adminEmails)
            //             ->send(new ActivationApprovedMail($activatedUser, $this->activation, $totalAmount));

            //     } catch (\Throwable $e) {
            //         Log::error('Failed to send approval email to activated user.', ['error' => $e->getMessage()]);
            //     }
            // }

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
    */

    public function approve(): void
    {
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

        /** @var User|null $activatedUser */
        $activatedUser = null;

        //Total Membership Fees (MF) Cost for the Activation
        // $totalAmount = (float) MembershipFee::sum('Cost');

        // 2. Atomic Database Operations (Database Changes Only)
        DB::transaction(function () use (&$activatedUser) {

            Log::info('Starting approval transaction for ActivationID: '.$this->activation->ActivationID);

            // Update Activation Status
            $this->activation->update([
                'status' => 'Approved',
            ]);

            // Build Admin Name for Log History
            $admin = Auth::user();
            $adminName = trim(implode(' ', array_filter([
                $admin->firstname ?? '',
                $admin->middlename ?? '',
                $admin->lastname ?? '',
            ])));

            // Log Approval History
            ActivationHistory::create([
                'ActivationID' => $this->activation->ActivationID,
                'AdminID' => Auth::id(),
                'status' => 'Approved',
                'remarks' => "Activation approved by {$adminName}.",
            ]);

            // Activate User Account
            User::where('UserID', $this->selectedUserID)->update([
                'status' => 'Active',
            ]);

            $activatedUser = User::find($this->selectedUserID);

            /*
            |--------------------------------------------------------------------------
            | Membership Fees (MF) Ledger Breakdown
            |--------------------------------------------------------------------------
            */
            $membershipFees = MembershipFee::all();

            foreach ($membershipFees as $fee) {
                Ledger::create([
                    'RefID' => $fee->FeeID,
                    'amount' => $fee->Cost,
                    'Source' => 'MF',
                    'ActivationID' => $this->activation->ActivationID,
                    'status' => 'Completed',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | GCash Convenience Fee (CF)
            |--------------------------------------------------------------------------
            | Only applies when payment_method is Gcash.
            | CF = 1% of Total Cost Membership = 19.99
            |--------------------------------------------------------------------------
            */

            if (
                (strtolower(trim($this->activation->payment_method)) === 'gcash'
                && $this->gcashConvenienceFeeEnabled) || strtolower(trim($this->activation->payment_method)) === 'paymaya'
                && $this->paymayaConvenienceFeeEnabled
            ) {

                $totalAmount = (float) MembershipFee::sum('Cost');
                $cfAmount = round($totalAmount * 0.01, 2);

                Ledger::create([
                    'RefID'        => $this->selectedUserID,
                    'amount'       => $cfAmount,
                    'Source'       => 'CF',
                    'ActivationID' => $this->activation->ActivationID,
                    'status'       => 'Completed',
                ]);

                Log::info('GCash | Paymaya CF ledger entry created.', [
                    'UserID' => $this->selectedUserID,
                    'ActivationID' => $this->activation->ActivationID,
                    'payment_method' => $this->activation->payment_method,
                    'amount' => $cfAmount,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Provident Fund (PF: 50.00) to Direct Recruiter / Upline
            |--------------------------------------------------------------------------
            */
            if ($this->referralUser) {
                Wallet::create([
                    'UserID' => $this->referralUser->UserID,
                    'ActivationID' => $this->activation->ActivationID,
                    'amount' => 50.00,
                    'source' => 'PF',
                    'status' => 'Active',
                ]);

                Ledger::create([
                    'RefID' => $this->referralUser->UserID,
                    'amount' => -50.00,
                    'Source' => 'PF',
                    'ActivationID' => $this->activation->ActivationID,
                    'status' => 'Completed',
                ]);

                Log::info("PF entry (50.00) written for referral UserID: {$this->referralUser->UserID}");
            }

            /*
            |--------------------------------------------------------------------------
            | Multi-Tier Commission Hierarchy (Strictly Matched to Diagram Flow)
            |--------------------------------------------------------------------------
            | Level 1: Direct Referral (400)
            | Level 2: 1st Level Override (50)
            | Level 3: 2nd Level Override (40)
            | Level 4: 3rd Level Override (30)
            | Level 5: 4th Level Override (20)
            | Level 6: 5th Level Override (10)
            |--------------------------------------------------------------------------
            */
            $drFee = MembershipFee::where('FeeName', 'Direct Referral')->value('Cost') ?? 400.00;

            $levelCommissionRates = [
                1 => (float) $drFee, // Level 1 - Direct Referral
                2 => 50.00,          // Level 2 - 1st Level Override
                3 => 40.00,          // Level 3 - 2nd Level Override
                4 => 30.00,          // Level 4 - 3rd Level Override
                5 => 20.00,          // Level 5 - 4th Level Override
                6 => 10.00,          // Level 6 - 5th Level Override
            ];

            $currentUplineCode = $this->activation->upline_code;
            $level = 1;
            $maxLevels = 6;

            while ($currentUplineCode && $level <= $maxLevels) {
                $uplineUser = User::where('referralCode', $currentUplineCode)->first();

                if (! $uplineUser) {
                    Log::warning("Upline user not found for code: {$currentUplineCode} at Level {$level}");
                    break;
                }

                $sourceType = ($level === 1) ? 'DR' : 'OV';
                $commissionAmount = $levelCommissionRates[$level] ?? 0.00;

                if ($commissionAmount > 0) {
                    Wallet::create([
                        'UserID' => $uplineUser->UserID,
                        'ActivationID' => $this->activation->ActivationID,
                        'amount' => $commissionAmount,
                        'source' => $sourceType,
                        'status' => 'Active',
                    ]);

                    Ledger::create([
                        'RefID' => $uplineUser->UserID,
                        'amount' => -$commissionAmount,
                        'Source' => $sourceType,
                        'ActivationID' => $this->activation->ActivationID,
                        'status' => 'Completed',
                    ]);

                    Log::info("Commission written for UserID: {$uplineUser->UserID} at Level {$level} [{$sourceType}]: {$commissionAmount}");
                }

                // Traverse up to next upline level
                $currentUplineCode = $uplineUser->uplineCode ?? null;
                $level++;
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Send Emails OUTSIDE Database Transaction (Prevents SMTP Latency Delays)
        |--------------------------------------------------------------------------
        */
        // Send email to the newly activated user
        if ($activatedUser && filled($activatedUser->email)) {
            try {

                Mail::to($activatedUser->email)
                    ->send(new ActivationApprovedMail($activatedUser, $this->activation,  $this->totalMembershipFeesCost));
            } catch (\Throwable $e) {
                Log::error('Failed to send approval email to user.', ['error' => $e->getMessage()]);
            }
        }

        // Send email to direct recruiter/upline
        if ($this->referralUser && filled($this->referralUser->email)) {
            try {
                Mail::to($this->referralUser->email)
                    ->send(new InviteeApprovedMail($this->referralUser, $activatedUser, $this->activation));
            } catch (\Throwable $e) {
                Log::error('Failed to send approval email to upline.', ['error' => $e->getMessage()]);
            }
        }

        // 4. UI Events
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

            if (! $activation) {
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
