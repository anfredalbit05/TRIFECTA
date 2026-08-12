<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Activation;
use App\Models\ActivationHistory;

class ViewMemberModal extends Component
{
    public bool $open = false;

    public ?int $UserID = null;
    public ?string $status = null;

    public $surname = '';
    public $first_name = '';
    public $middle_name = '';

    public $gender = '';
    public $civil_status = '';
    public $birthdate = '';

    public $address = '';
    public $latitude = null;
    public $longitude = null;

    public $mobile = '';
    public $email = '';
    public $username = '';
    public $referralCode = '';
    public $uplineCode = '';

   public ?string $activationStatus = null;

    public ?string $activationDate = null;

    public ?string $activationLabel = null;

    public array $beneficiaries = [];

    #[On('open-view-member')]
    public function open($UserID): void
    {
        $user = User::with('beneficiaries')->findOrFail($UserID);

        $this->UserID = $user->UserID;
        $this->status = $user->status;

        $this->surname = $user->lastname;
        $this->first_name = $user->firstname;
        $this->middle_name = $user->middlename;

        $this->gender = $user->gender;
        $this->civil_status = $user->civilstatus;
        $this->birthdate = $user->birthdate;

        $this->address = $user->address;
        $this->latitude = $user->latitude;
        $this->longitude = $user->longitude;

        $this->mobile = $user->contactno;
        $this->email = $user->email;
        $this->username = $user->username;

        $this->referralCode = $user->referralCode;
        $this->uplineCode = $user->uplineCode;

        $activation = Activation::where('UserID', $UserID)
            ->latest()
            ->first();

        if ($activation) {

            $this->activationStatus = $activation->status;

            switch ($activation->status) {

                case 'Approved':

                    $history = ActivationHistory::where(
                        'ActivationID',
                        $activation->ActivationID
                    )
                    ->where('status', 'Approved')
                    ->latest()
                    ->first();

                    $this->activationLabel = 'Approved Date';

                    $this->activationDate = $history
                        ? $history->created_at->format('F d, Y h:i A')
                        : null;

                    break;

                case 'Rejected':

                    $history = ActivationHistory::where(
                        'ActivationID',
                        $activation->ActivationID
                    )
                    ->where('status', 'Rejected')
                    ->latest()
                    ->first();

                    $this->activationLabel = 'Rejected Date';

                    $this->activationDate = $history
                        ? $history->created_at->format('F d, Y h:i A')
                        : null;

                    break;

                case 'Pending':

                    $this->activationLabel = 'Submitted Date';

                    $this->activationDate = $activation->created_at
                        ? $activation->created_at->format('F d, Y h:i A')
                        : null;

                    break;

                default:

                    $this->activationLabel = null;
                    $this->activationDate = null;

                    break;
            }

     } else {

            $this->activationStatus = null;
            $this->activationDate = null;
            $this->activationLabel = null;
        }

        $this->beneficiaries = $user->beneficiaries
            ->map(fn ($beneficiary) => [
                'fullname' => $beneficiary->fullname,
                'birthdate' => $beneficiary->birthdate,
                'relationship' => $beneficiary->relationship,
            ])
            ->toArray();

        $this->open = true;
    }

    public function close(): void
    {
        $this->reset();

        $this->open = false;
    }

    public function render()
    {
        return view('livewire.view-member-modal');
    }
}
