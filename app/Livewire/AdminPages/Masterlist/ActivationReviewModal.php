<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Models\Activation;
use App\Models\Beneficiary;
use App\Models\User;
use App\Models\Wallet;
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
        | Find Pending Activation by UserID (Fixed from ProcessedBy)
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
            | Multi-Tier Referral Commission Structure (6 Levels)
            |--------------------------------------------------------------------------
            | - Level 1: 400
            | - Level 2: 50
            | - Level 3: 40
            | - Level 4: 30
            | - Level 5: 20
            | - Level 6: 10
            | - Level 7+: 0 (Loop stops completely)
            */
            $currentUplineCode = $this->activation->upline_code;
            $level = 1;
            $maxLevels = 6;

            $commissionRates = [
                1 => 400.00,
                2 => 50.00,
                3 => 40.00,
                4 => 30.00,
                5 => 20.00,
                6 => 10.00,
            ];

            while ($currentUplineCode && $level <= $maxLevels) {
                $uplineUser = User::where('referralCode', $currentUplineCode)->first();

                if (!$uplineUser) {
                    break;
                }

                $commissionAmount = $commissionRates[$level] ?? 0.00;

                if ($commissionAmount > 0) {
                    Wallet::create([
                        'UserID'       => $uplineUser->UserID,
                        'ActivationID' => $this->activation->ActivationID,
                        'amount'       => $commissionAmount,
                        'status'       => 'Active',
                    ]);

                    Log::info("Admin Review: Level {$level} commission of {$commissionAmount} awarded to User ID: {$uplineUser->UserID}");
                }

                $currentUplineCode = $uplineUser->upline_code ?? null;
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
