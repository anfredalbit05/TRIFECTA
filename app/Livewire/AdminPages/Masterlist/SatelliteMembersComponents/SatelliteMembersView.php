<?php

namespace App\Livewire\AdminPages\Masterlist\SatelliteMembersComponents;

use App\Models\Activation;
use App\Models\ActivationHistory;
use App\Models\Privilege;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class SatelliteMembersView extends Component
{
    public bool $open = false;

    public ?int $UserID = null;

    public ?string $status = null;

    /*
    | True when the signed-in user is the record owner or holds ModuleID 2
    | read privilege — only then are the Policy/Certificate download links
    | rendered in the modal footer.
    */
    public bool $canDownload = false;

    /**
     * Mirrors ChecksMemberPermissions::callerHasReadPermission — kept local so
     * the trait's other members never leak into this component.
     */
    protected function callerHasReadPermission($caller): bool
    {
        if (! $caller) {
            return false;
        }

        if ((int) $caller->RoleID === 1) {
            return true;
        }

        return Privilege::where('RoleID', $caller->RoleID)
            ->where('ModuleID', 2)
            ->where('can_read', 1)
            ->exists();
    }

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
    #[On('open-satellite-member-view')]
    public function open($UserID): void
    {
        /*
        |--------------------------------------------------------------------------
        | Role guard
        |--------------------------------------------------------------------------
        | Admins may only open satellite members (RoleID 8). The signed-in member
        | may always open their OWN row — the member Satellite page reuses this
        | modal, and their account is not RoleID 8.
        |--------------------------------------------------------------------------
        */

        $isOwnerViewing = (int) $UserID === (int) Auth::id();

        $user = User::where('UserID', $UserID)
            ->when(! $isOwnerViewing, fn ($q) => $q->where('RoleID', 8))
            ->with('beneficiaries')
            ->first();
        if (! $user) {
            return;
        }

        $this->UserID = $user->UserID;
        $this->status = $user->status;

        /*
        | Downloads: the owner always can; admins need ModuleID 2 read.
        */
        $this->canDownload = (int) $user->UserID === (int) Auth::id()
            || $this->callerHasReadPermission(Auth::user());

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
            ->latest('ActivationID')
            ->first();

        if (! $activation) {
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
        $this->canDownload = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */
    public function render()
    {
        return view('livewire.admin-pages.masterlist.satellite-members-components.satellite-members-view');
    }
}
