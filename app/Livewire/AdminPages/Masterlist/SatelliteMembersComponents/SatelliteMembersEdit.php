<?php

namespace App\Livewire\AdminPages\Masterlist\SatelliteMembersComponents;

use Livewire\Component;
use Livewire\Attributes\On;

use App\Models\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SatelliteMembersEdit extends Component
{
    public bool $open = false;

    /*
    |--------------------------------------------------------------------------
    | Applicant Information
    |--------------------------------------------------------------------------
    */

    public $surname = '';
    public $first_name = '';
    public $middle_name = '';
    public $occupation = '';
    public $gender = '';
    public $civil_status = '';
    public $birthdate = '';
    public $address = '';

    public ?float $latitude = null;
    public ?float $longitude = null;

    public bool $showMapModal = false;
    public bool $isSubmitting = false;
    public bool $isViewMode = false;
    public bool $isEditMode = false;

    public ?int $userId = null;

    /*
    |--------------------------------------------------------------------------
    | Policy Information
    |--------------------------------------------------------------------------
    */

    public $policy_number = null;
    public $policy_effectivity_date = null;
    public $policy_expiration_date = null;

    public $upline = '';

    /*
    |--------------------------------------------------------------------------
    | Security Information
    |--------------------------------------------------------------------------
    */

    public $mobile = '';
    public $email = '';
    public $username = '';

    /*
    |--------------------------------------------------------------------------
    | Original Data
    |--------------------------------------------------------------------------
    |
    | Used to build the "what changed" diff before saving.
    |
    */

    public array $originalData = [];

    /*
    |--------------------------------------------------------------------------
    | Open Satellite Member Edit Modal
    |--------------------------------------------------------------------------
    |
    | Only users with RoleID = 8 are allowed.
    |
    */

    #[On('open-satellite-member-edit')]
    public function openEdit(int $UserID): void
    {
        $user = User::where('UserID', $UserID)
            ->where('RoleID', 8)
            ->first();

        if (! $user) {
            Log::warning(
                'Satellite member edit blocked: invalid member.',
                [
                    'UserID' => $UserID,
                    'RoleID' => $user?->RoleID,
                ]
            );

            $this->dispatch(
                'swal:error',
                title: 'Not Allowed',
                text: 'The selected user is not a satellite member.'
            );

            return;
        }

        $this->fillUserData($user);

        $this->userId = $user->UserID;
        $this->isViewMode = false;
        $this->isEditMode = true;
        $this->isSubmitting = false;
        $this->open = true;

        $this->resetErrorBag();
        $this->resetValidation();

        Log::info(
            'Satellite member edit modal opened.',
            [
                'UserID' => $user->UserID,
                'opened_by' => Auth::id(),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Fill Form With Satellite Member Data
    |--------------------------------------------------------------------------
    */

    private function fillUserData(User $user): void
    {
        $this->surname = $user->lastname ?? '';
        $this->first_name = $user->firstname ?? '';
        $this->middle_name = $user->middlename ?? '';
        $this->occupation = $user->occupation ?? '';

        $this->gender = $user->gender ?? '';
        $this->civil_status = $user->civilstatus ?? '';

        $this->birthdate = $user->birthdate
            ? \Carbon\Carbon::parse($user->birthdate)->format('Y-m-d')
            : '';

        $this->address = $user->address ?? '';

        $this->latitude = $user->latitude;
        $this->longitude = $user->longitude;

        $this->mobile = $user->contactno ?? '';
        $this->email = $user->email ?? '';
        $this->username = $user->username ?? '';

        /*
        |--------------------------------------------------------------------------
        | Policy Fields
        |--------------------------------------------------------------------------
        */

        $this->policy_number = $user->policy_number ?: null;

        $this->policy_effectivity_date = $user->policy_effectivity_date
            ? \Carbon\Carbon::parse(
                $user->policy_effectivity_date
            )->format('Y-m-d')
            : null;

        $this->policy_expiration_date = $user->policy_expiration_date
            ? \Carbon\Carbon::parse(
                $user->policy_expiration_date
            )->format('Y-m-d')
            : null;

        /*
        |--------------------------------------------------------------------------
        | Snapshot Original Data
        |--------------------------------------------------------------------------
        */

        $this->originalData = [
            'surname' => $this->surname,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'occupation' => $this->occupation,
            'gender' => $this->gender,
            'civil_status' => $this->civil_status,
            'birthdate' => $this->birthdate,
            'address' => $this->address,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'username' => $this->username,
            'policy_number' => $this->policy_number,
            'policy_effectivity_date' => $this->policy_effectivity_date,
            'policy_expiration_date' => $this->policy_expiration_date,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Real-Time Formatting
    |--------------------------------------------------------------------------
    */

    public function updated($property, $value): void
    {
        /*
        |--------------------------------------------------------------------------
        | Mobile Number
        |--------------------------------------------------------------------------
        */

        if ($property === 'mobile') {

            if (str_starts_with($value, '+63')) {
                $value = '09' . substr($value, 3);
            } elseif (
                str_starts_with($value, '63') &&
                strlen($value) === 12
            ) {
                $value = '0' . substr($value, 2);
            }

            $value = preg_replace('/\D/', '', $value);

            if (
                strlen($value) > 0 &&
                !str_starts_with($value, '0')
            ) {
                $value = '0' . $value;
            }

            if (
                strlen($value) > 1 &&
                $value[1] !== '9'
            ) {
                $value = '09' . substr($value, 2);
            }

            $this->mobile = substr($value, 0, 11);
        }

        /*
        |--------------------------------------------------------------------------
        | Name / Address Formatting
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $property,
                [
                    'surname',
                    'first_name',
                    'middle_name',
                    'occupation',
                    'address',
                ]
            )
        ) {
            $this->{$property} = ucwords(strtolower($value));
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Close Modal
    |--------------------------------------------------------------------------
    */

    public function close(): void
    {
        $this->dispatch('destroy-map');

        $this->resetValidation();

        $this->reset([
            'surname',
            'first_name',
            'middle_name',
            'occupation',
            'gender',
            'civil_status',
            'birthdate',
            'address',
            'mobile',
            'email',
            'username',
            'originalData',
            'userId',
            'latitude',
            'longitude',
            'policy_number',
            'policy_effectivity_date',
            'policy_expiration_date',
            'upline',
        ]);

        $this->showMapModal = false;
        $this->isEditMode = false;
        $this->isViewMode = false;
        $this->isSubmitting = false;
        $this->open = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Confirm Update
    |--------------------------------------------------------------------------
    |
    | Validate the satellite member and display the changed fields.
    |
    */

    public function confirmUpdate(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Make Sure User ID Exists
        |--------------------------------------------------------------------------
        */

        if (!$this->userId) {
            $this->dispatch(
                'validation-error',
                title: 'Invalid Member',
                html: 'No satellite member was selected.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT:
        | Verify that the selected user is RoleID = 8.
        |--------------------------------------------------------------------------
        */

        $user = User::where('UserID', $this->userId)
            ->where('RoleID', 8)
            ->first();

        if (!$user) {
            $this->dispatch(
                'swal:error',
                title: 'Not Allowed',
                text: 'This member is not a satellite member.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Normalize Policy Fields
        |--------------------------------------------------------------------------
        */

        $this->policy_number = blank($this->policy_number)
            ? null
            : trim($this->policy_number);

        $this->policy_effectivity_date = blank(
            $this->policy_effectivity_date
        )
            ? null
            : trim($this->policy_effectivity_date);

        $this->policy_expiration_date = blank(
            $this->policy_expiration_date
        )
            ? null
            : trim($this->policy_expiration_date);

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        try {

            $this->validate([
                'surname' => 'required|string|max:255',

                'first_name' => 'required|string|max:255',

                'middle_name' => 'nullable|string|max:255',

                'gender' => 'required',

                'civil_status' => 'required',

                'birthdate' => 'required|date',

                'occupation' => 'nullable|string|max:255',

                'address' => 'required|string|max:500',

                'mobile' => 'required|digits:11',

                'email' =>
                    'nullable|email|unique:users,email,'
                    . $this->userId
                    . ',UserID',

                'username' =>
                    'required|string|max:100|unique:users,username,'
                    . $this->userId
                    . ',UserID',

                'policy_number' =>
                    'nullable|string|max:100',

                'policy_effectivity_date' =>
                    'nullable|date',

                'policy_expiration_date' =>
                    'nullable|date',
            ]);

        } catch (ValidationException $e) {

            $errors = $e->validator->errors()->all();

            $html =
                '<ul style="text-align:left; list-style-type:disc; padding-left:20px;">';

            foreach ($errors as $error) {
                $html .=
                    "<li style='margin-bottom:6px'>"
                    . e($error)
                    . '</li>';
            }

            $html .= '</ul>';

            $this->dispatch(
                'validation-error',
                title: 'Missing or Invalid Fields',
                html: $html
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Find Changed Fields
        |--------------------------------------------------------------------------
        */

        $changes = $this->getChangedFields();

        if (empty($changes)) {

            $this->dispatch(
                'validation-error',
                title: 'No Changes',
                html: 'There are no changes to save.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Ask Frontend For Confirmation
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'swal-confirm-update',
            changes: $changes
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Get Changed Fields
    |--------------------------------------------------------------------------
    */

    private function getChangedFields(): array
    {
        $labels = [

            'surname' =>
                'Surname',

            'first_name' =>
                'First Name',

            'middle_name' =>
                'Middle Name',

            'occupation' =>
                'Occupation',

            'gender' =>
                'Gender',

            'civil_status' =>
                'Civil Status',

            'birthdate' =>
                'Birthdate',

            'address' =>
                'Address',

            'mobile' =>
                'Mobile Number',

            'email' =>
                'Email',

            'username' =>
                'Username',

            'policy_number' =>
                'Policy Number',

            'policy_effectivity_date' =>
                'Policy Effectivity Date',

            'policy_expiration_date' =>
                'Policy Expiration Date',
        ];

        $changes = [];

        foreach ($labels as $field => $label) {

            $old = (string) (
                $this->originalData[$field] ?? ''
            );

            $new = (string) (
                $this->{$field} ?? ''
            );

            if ($old !== $new) {

                $changes[] = [

                    'field' => $label,

                    'old' =>
                        $old !== ''
                            ? $old
                            : '—',

                    'new' =>
                        $new !== ''
                            ? $new
                            : '—',
                ];
            }
        }

        return $changes;
    }

    /*
    |--------------------------------------------------------------------------
    | Update Satellite Member
    |--------------------------------------------------------------------------
    |
    | Called after the SweetAlert confirmation.
    |
    */

    #[On('confirmed-update-member')]
    public function updateMember(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Validate User ID
        |--------------------------------------------------------------------------
        */

        if (!$this->userId) {

            $this->dispatch(
                'validation-error',
                title: 'Invalid Member',
                html: 'No satellite member was selected.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CRITICAL ROLE CHECK
        |--------------------------------------------------------------------------
        |
        | Never update a user unless RoleID = 8.
        |
        */

        $user = User::where('UserID', $this->userId)
            ->where('RoleID', 8)
            ->first();

        if (!$user) {

            Log::warning(
                'Attempted to update non-satellite member',
                [
                    'UserID' => $this->userId,
                ]
            );

            $this->dispatch(
                'swal:error',
                title: 'Not Allowed',
                text: 'Only satellite members can be updated here.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Normalize Policy Fields
        |--------------------------------------------------------------------------
        */

        $this->policy_number = blank($this->policy_number)
            ? null
            : trim($this->policy_number);

        $this->policy_effectivity_date = blank(
            $this->policy_effectivity_date
        )
            ? null
            : trim($this->policy_effectivity_date);

        $this->policy_expiration_date = blank(
            $this->policy_expiration_date
        )
            ? null
            : trim($this->policy_expiration_date);

        /*
        |--------------------------------------------------------------------------
        | Validate Again
        |--------------------------------------------------------------------------
        |
        | Validation is repeated here because Livewire events can be triggered
        | independently of confirmUpdate().
        |
        */

        try {

            $this->validate([
                'surname' =>
                    'required|string|max:255',

                'first_name' =>
                    'required|string|max:255',

                'middle_name' =>
                    'nullable|string|max:255',

                'gender' =>
                    'required',

                'civil_status' =>
                    'required',

                'birthdate' =>
                    'required|date',

                'occupation' =>
                    'nullable|string|max:255',

                'address' =>
                    'required|string|max:500',

                'mobile' =>
                    'required|digits:11',

                'email' =>
                    'nullable|email|unique:users,email,'
                    . $this->userId
                    . ',UserID',

                'username' =>
                    'required|string|max:100|unique:users,username,'
                    . $this->userId
                    . ',UserID',

                'policy_number' =>
                    'nullable|string|max:100',

                'policy_effectivity_date' =>
                    'nullable|date',

                'policy_expiration_date' =>
                    'nullable|date',
            ]);

        } catch (ValidationException $e) {

            $errors = $e->validator->errors()->all();

            $html =
                '<ul style="text-align:left; list-style-type:disc; padding-left:20px;">';

            foreach ($errors as $error) {

                $html .=
                    "<li style='margin-bottom:6px'>"
                    . e($error)
                    . '</li>';
            }

            $html .= '</ul>';

            $this->dispatch(
                'validation-error',
                title: 'Missing or Invalid Fields',
                html: $html
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Update Database
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Re-check Role Inside Transaction
            |--------------------------------------------------------------------------
            */

            $user = User::where('UserID', $this->userId)
                ->where('RoleID', 8)
                ->lockForUpdate()
                ->first();

            if (!$user) {

                throw new \RuntimeException(
                    'The selected user is no longer a satellite member.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Update Member
            |--------------------------------------------------------------------------
            |
            | Notice:
            | RoleID is intentionally NOT included here.
            |
            | Therefore an existing satellite member remains RoleID = 8.
            |
            */

            $user->update([

                'lastname' =>
                    $this->surname,

                'firstname' =>
                    $this->first_name,

                'middlename' =>
                    $this->middle_name,

                'gender' =>
                    $this->gender,

                'civilstatus' =>
                    $this->civil_status,

                'birthdate' =>
                    $this->birthdate,

                'occupation' =>
                    $this->occupation,

                'contactno' =>
                    $this->mobile,

                'email' =>
                    filled($this->email)
                        ? $this->email
                        : null,

                'username' =>
                    $this->username,

                'address' =>
                    $this->address,

                'latitude' =>
                    $this->latitude,

                'longitude' =>
                    $this->longitude,

                'policy_number' =>
                    $this->policy_number,

                'policy_effectivity_date' =>
                    $this->policy_effectivity_date,

                'policy_expiration_date' =>
                    $this->policy_expiration_date,
            ]);

            DB::commit();

            Log::info(
                'Satellite member updated successfully',
                [
                    'UserID' => $user->UserID,
                    'RoleID' => $user->RoleID,
                    'updated_by' => Auth::id(),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Success Messages
            |--------------------------------------------------------------------------
            */

            $this->dispatch('swal-close');

            $this->dispatch(
                'show-success-after-close',
                title: 'Updated!',
                text: 'Satellite member updated successfully.'
            );

            $this->dispatch(
                'show-toast',
                title: 'Satellite member updated successfully!',
                icon: 'success'
            );

            /*
            |--------------------------------------------------------------------------
            | Refresh Satellite Members Table
            |--------------------------------------------------------------------------
            */

            $this->dispatch('members-refresh');

            /*
            |--------------------------------------------------------------------------
            | Close Modal
            |--------------------------------------------------------------------------
            */

            $this->close();

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error(
                'Satellite member update failed',
                [
                    'UserID' => $this->userId,
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                ]
            );

            $this->dispatch('swal-close');

            $this->dispatch(
                'validation-error',
                title: 'Error',
                html: 'Failed to update satellite member.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Backward Compatibility
    |--------------------------------------------------------------------------
    */

    public function confirmUpdateLegacyAlias(): void
    {
        $this->confirmUpdate();
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Map
    |--------------------------------------------------------------------------
    */

    public function toggleMap(): void
    {
        $this->showMapModal = !$this->showMapModal;

        if ($this->showMapModal) {

            $this->dispatch(
                'map-opened',
                latitude: $this->latitude,
                longitude: $this->longitude
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Set Coordinates
    |--------------------------------------------------------------------------
    */

    #[On('set-coordinates')]
    public function setCoordinates(
        $lat,
        $lng,
        $address = null
    ): void {

        $this->latitude = (float) $lat;
        $this->longitude = (float) $lng;

        if (!empty($address)) {

            $this->address =
                ucwords(
                    strtolower($address)
                );
        }

        Log::info(
            'Satellite member map coordinates updated',
            [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'address' => $this->address,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view('livewire.admin-pages.masterlist.satellite-members-components.satellite-members-edit');
    }
}
