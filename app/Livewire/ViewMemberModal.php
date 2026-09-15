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

    /*
    |--------------------------------------------------------------------------
    | Personal Information
    |--------------------------------------------------------------------------
    */
    public $surname = '';
    public $first_name = '';
    public $middle_name = '';
    public $occupation = '';
    public $gender = '';
    public $civil_status = '';
    public $birthdate = '';
    public $referralCode = '';
    public $uplineCode = '';

    /*
    |--------------------------------------------------------------------------
    | Policy Information
    |--------------------------------------------------------------------------
    */
    public $policy_number = '';
    public $policy_expiration_date = '';
    public $policy_effectivity_date = '';

    /*
    |--------------------------------------------------------------------------
    | Address & Location
    |--------------------------------------------------------------------------
    */
    public $address = '';
    public $latitude = null;
    public $longitude = null;

    /*
    |--------------------------------------------------------------------------
    | Contact Information
    |--------------------------------------------------------------------------
    */
    public $mobile = '';
    public $email = '';
    public $username = '';

    /*
    |--------------------------------------------------------------------------
    | Activation Information
    |--------------------------------------------------------------------------
    */
    public ?string $activationStatus = null;
    public ?string $activationDate = null;
    public ?string $activationLabel = null;

    /*
    |--------------------------------------------------------------------------
    | Beneficiaries
    |--------------------------------------------------------------------------
    */
    public array $beneficiaries = [];

    /*
    |--------------------------------------------------------------------------
    | Open Modal
    |--------------------------------------------------------------------------
    */
    #[On('open-view-member')]
    public function open($UserID): void
    {
        $user = User::with('beneficiaries')->findOrFail($UserID);

        $this->UserID = $user->UserID;
        $this->status = $user->status;

        $this->fillPersonalInfo($user);
        $this->fillPolicyInfo($user);
        $this->fillAddressInfo($user);
        $this->fillContactInfo($user);
        $this->fillActivationInfo($user);
        $this->fillBeneficiaries($user);

        $this->open = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Personal Information
    |--------------------------------------------------------------------------
    */
    private function fillPersonalInfo(User $user): void
    {
        $this->surname = $user->lastname;
        $this->first_name = $user->firstname;
        $this->middle_name = $user->middlename;
        $this->occupation = $user->occupation;
        $this->gender = $user->gender;
        $this->civil_status = $user->civilstatus;
        $this->birthdate = $user->birthdate;
        $this->referralCode = $user->referralCode;
        $this->uplineCode = $user->uplineCode;
    }

    /*
    |--------------------------------------------------------------------------
    | Policy Information
    |--------------------------------------------------------------------------
    */
    private function fillPolicyInfo(User $user): void
    {
        $this->policy_number = $user->policy_number;
        $this->policy_expiration_date = $user->policy_expiration_date;
        $this->policy_effectivity_date = $user->policy_effectivity_date;
    }

    /*
    |--------------------------------------------------------------------------
    | Address & Location
    |--------------------------------------------------------------------------
    */
    private function fillAddressInfo(User $user): void
    {
        $this->address = $user->address;
        $this->latitude = $user->latitude;
        $this->longitude = $user->longitude;
    }

    /*
    |--------------------------------------------------------------------------
    | Contact Information
    |--------------------------------------------------------------------------
    */
    private function fillContactInfo(User $user): void
    {
        $this->mobile = $user->contactno;
        $this->email = $user->email;
        $this->username = $user->username;
    }

    /*
    |--------------------------------------------------------------------------
    | Activation Information
    |--------------------------------------------------------------------------
    */
    private function fillActivationInfo(User $user): void
    {
        $activation = Activation::where('UserID', $user->UserID)
            ->latest()
            ->first();

        if (!$activation) {
            $this->activationStatus = null;
            $this->activationDate = null;
            $this->activationLabel = null;
            return;
        }

        $this->activationStatus = $activation->status;

        switch ($activation->status) {

            case 'Approved':
                $history = ActivationHistory::where('ActivationID', $activation->ActivationID)
                    ->where('status', 'Approved')
                    ->latest()
                    ->first();

                $this->activationLabel = 'Approved Date';
                $this->activationDate = $history
                    ? $history->created_at->format('F d, Y h:i A')
                    : null;
                break;

            case 'Rejected':
                $history = ActivationHistory::where('ActivationID', $activation->ActivationID)
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
    }

    /*
    |--------------------------------------------------------------------------
    | Beneficiaries
    |--------------------------------------------------------------------------
    */
    private function fillBeneficiaries(User $user): void
    {
        $this->beneficiaries = $user->beneficiaries
            ->map(fn ($beneficiary) => [
                'fullname' => $beneficiary->fullname,
                'birthdate' => $beneficiary->birthdate,
                'relationship' => $beneficiary->relationship,
            ])
            ->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Close Modal
    |--------------------------------------------------------------------------
    */
    public function close(): void
    {
        $this->reset();

        $this->open = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */
    public function render()
    {
        return view('livewire.view-member-modal');
    }
}
