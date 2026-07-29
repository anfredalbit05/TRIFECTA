<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Models\Activation;
use App\Models\Beneficiary;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class ActivationReviewModal extends Component
{
    public bool $open = false;

    public ?int $selectedUserID = null;

    public ?User $member = null;

    public ?Activation $activation = null;

    public ?User $referralUser = null;

    public array $beneficiaries = [];


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
            !$this->activation
            ||
            !$this->selectedUserID
        ) {
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
            )

            ->update([

                'status' => 'Approved',

            ]);


            /*
            |--------------------------------------------------------------------------
            | Activate Member
            |--------------------------------------------------------------------------
            */

            User::where(
                'UserID',
                $this->selectedUserID
            )

            ->update([

                'status' => 'Active',

            ]);

        });


        Log::info(
            'Activation approved.',
            [
                'UserID' =>
                    $this->selectedUserID,

                'ActivationID' =>
                    $this->activation->ActivationID,
            ]
        );


        $this->dispatch(
            'activation-reviewed',
            type: 'success',
            message: 'Activation approved successfully.'
        );


        $this->closeModal();
    }


    /*
    |--------------------------------------------------------------------------
    | Reject Activation
    |--------------------------------------------------------------------------
    */

    public function reject(): void
    {
        if (
            !$this->activation
            ||
            !$this->selectedUserID
        ) {
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
            )

            ->update([

                'status' => 'Rejected',

            ]);


            /*
            |--------------------------------------------------------------------------
            | Keep Member Inactive
            |--------------------------------------------------------------------------
            */

            User::where(
                'UserID',
                $this->selectedUserID
            )

            ->update([

                'status' => 'Inactive',

            ]);

        });


        Log::info(
            'Activation rejected.',
            [
                'UserID' =>
                    $this->selectedUserID,

                'ActivationID' =>
                    $this->activation->ActivationID,
            ]
        );


        $this->dispatch(
            'activation-reviewed',
            type: 'success',
            message: 'Activation rejected.'
        );


        $this->closeModal();
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
