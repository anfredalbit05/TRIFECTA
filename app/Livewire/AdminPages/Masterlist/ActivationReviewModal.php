<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Models\Activation;
use App\Models\Beneficiary;
use App\Models\User;
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
        Log::info(
            'Opening activation review modal.',
            [
                'UserID' => $UserID,
            ]
        );


        $this->selectedUserID = (int) $UserID;


        /*
        |--------------------------------------------------------------------------
        | Load Member
        |--------------------------------------------------------------------------
        */

        $this->member = User::find(
            $this->selectedUserID
        );


        /*
        |--------------------------------------------------------------------------
        | Find Pending Activation
        |--------------------------------------------------------------------------
        |
        | The member's UserID must match
        | activations.ProcessedBy.
        |
        */

        $this->activation = Activation::where(
            'ProcessedBy',
            $this->selectedUserID
        )

        ->where(
            'status',
            'Pending'
        )

        ->latest(
            'ActivationID'
        )

        ->first();


        /*
        |--------------------------------------------------------------------------
        | Load Referral User
        |--------------------------------------------------------------------------
        */

        $this->referralUser = null;


        if ($this->activation) {

            $this->referralUser = User::find(
                $this->activation->UserID
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Load Member Beneficiaries
        |--------------------------------------------------------------------------
        */

        $this->beneficiaries = Beneficiary::where(
            'UserID',
            $this->selectedUserID
        )

        ->where(
            'status',
            true
        )

        ->orderBy(
            'BeneficiaryID'
        )

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
    
    if (
        !$this->activation ||
        !$this->selectedUserID
    ) {
        Log::warning('Approval failed: activation or selected user missing.', [
            'selectedUserID' => $this->selectedUserID,
            'activation' => $this->activation?->ActivationID,
        ]);

        return;
    }

    DB::transaction(function () {

        /*
        |--------------------------------------------------------------------------
        | Update Activation
        |--------------------------------------------------------------------------
        */

        Activation::where(
            'ActivationID',
            $this->activation->ActivationID
        )->update([
            'status' => 'Approved',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Get CC recipients (Super Administrator + Administrator roles)
    |--------------------------------------------------------------------------
    */

    $adminEmails = User::join('roles', 'users.RoleID', '=', 'roles.RoleID')
        ->whereIn('roles.rolename', [
            'Super Administrator',
            'Administrator',
        ])
        ->whereNotNull('users.email')
        ->pluck('users.email')
        ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Activate Member
        |--------------------------------------------------------------------------
        */

        User::where(
            'UserID',
            $this->selectedUserID
        )->update([
            'status' => 'Active',
        ]);

        $activatedUser = User::find($this->selectedUserID);

        Log::info('Activated user found.', [
            'user_id' => $activatedUser?->UserID,
            'username' => $activatedUser?->username,
            'email' => $activatedUser?->email,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Send email to activated user
        |--------------------------------------------------------------------------
        */

        if ($activatedUser && filled($activatedUser->email)) {

            try {

                Mail::raw(
                    "Congratulations!\n\n" .
                    "Your account has been approved successfully.\n\n" .
                    "You can now log in and access all features.",
                    function ($message) use ($activatedUser, $adminEmails) {
                        $message->to($activatedUser->email)
                            ->cc($adminEmails)
                            ->subject('Account Approved');
                    }
                );

                Log::info('Approval email sent to activated user.', [
                    'email' => $activatedUser->email,
                ]);

            } catch (\Throwable $e) {

                Log::error('Failed to send approval email to activated user.', [
                    'user_id' => $activatedUser->UserID,
                    'email' => $activatedUser->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Send email to upline
        |--------------------------------------------------------------------------
        */

        $referralUser = User::where(
            'referralCode',
            $this->activation->upline_code
        )->first();

        Log::info('Referral user found.', [
            'user_id' => $referralUser?->UserID,
            'email' => $referralUser?->email,
        ]);

        if ($referralUser && filled($referralUser->email)) {

            try {

                Mail::to($referralUser->email)
                ->cc($adminEmails)
                ->send(

                    new InviteeApprovedMail(
                        $referralUser,
                        $activatedUser,
                        $this->activation
                    )

                );

                Log::info('Approval email sent to upline.', [
                    'email' => $referralUser->email,
                ]);

            } catch (\Throwable $e) {

                Log::error('Failed to send approval email to upline.', [
                    'user_id' => $referralUser->UserID,
                    'email' => $referralUser->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    });

    Log::info(
        'Activation approved.',
        [
            'UserID' => $this->selectedUserID,
            'ActivationID' => $this->activation->ActivationID,
        ]
    );

    $this->dispatch('members-refresh');

    $this->dispatch(
        'swal:success',
        title: 'Success!',
        message: 'Activation approved successfully.'
    );

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
    $this->remarks = $remarks;

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

        $activation = Activation::where(
            'UserID',
            $this->selectedUserID
        )->latest()->first();

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

            User::where(
                'UserID',
                $this->selectedUserID
            )->update([
                'status' => 'Inactive',
            ]);

            $user = User::find($this->selectedUserID);

            Mail::to($user->email)->send(
                new ActivationRejectedMail(
                    $user,
                    $activation,
                    $this->beneficiaries,
                    $this->remarks
                )
            );
        });

            $this->dispatch(
            'swal:success',
            title: 'Rejected!',
            message: 'Activation rejected successfully.'
        );

        $this->closeModal();

    } finally {

        $this->isRejecting = false;
    }
}

    /*
    |--------------------------------------------------------------------------
    | Close Modal
    |--------------------------------------------------------------------------
    */

    public function closeModal(): void
    {
        $this->open = false;

        $this->selectedUserID = null;

        $this->member = null;

        $this->activation = null;

        $this->referralUser = null;

        $this->beneficiaries = [];
    }


    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view(
            'livewire.admin-pages.masterlist.activation-review-modal'
        );
    }
}
