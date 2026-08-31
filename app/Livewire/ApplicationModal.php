<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\User;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ApplicationModal extends Component
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
    public $policy_number = '';

    public $upline = '';

    public $beneficiaryToDelete = null;

    /*
    |--------------------------------------------------------------------------
    | Beneficiary Information
    |--------------------------------------------------------------------------
    */
    public array $beneficiaries = [
        [
            'fullname' => '',
            'birthdate' => '',
            'relationship' => '',
        ],
        [
            'fullname' => '',
            'birthdate' => '',
            'relationship' => '',
        ],
        [
            'fullname' => '',
            'birthdate' => '',
            'relationship' => '',
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | Security Information
    |--------------------------------------------------------------------------
    */
    public $mobile = '';
    public $email = '';
    public $username = '';
    public $password = '';
    public $password_confirmation = '';

    /*
    |--------------------------------------------------------------------------
    | Terms
    |--------------------------------------------------------------------------
    */
    public bool $agree = false;

    /*
    |--------------------------------------------------------------------------
    | Real-time Format Hook
    |--------------------------------------------------------------------------
    */
    #[On('delete-beneficiary')]
    public function deleteBeneficiaryEvent($index)
    {
        $this->deleteBeneficiary($index);
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Beneficiaries
    |--------------------------------------------------------------------------
    */
    private function validateBeneficiaries()
    {
        foreach ($this->beneficiaries as $index => $beneficiary) {

            $number = $index + 1;

            $fullname = trim($beneficiary['fullname'] ?? '');
            $birthdate = trim($beneficiary['birthdate'] ?? '');
            $relationship = trim($beneficiary['relationship'] ?? '');

            /*
            |--------------------------------------------------------------------------
            | CHECK IF BENEFICIARY IS COMPLETELY EMPTY
            |--------------------------------------------------------------------------
            */
            if (
                empty($fullname) &&
                empty($birthdate) &&
                empty($relationship)
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | FULL NAME REQUIRED
            |--------------------------------------------------------------------------
            */
            if (empty($fullname)) {

                $this->dispatch(
                    'validation-error',
                    title: "Beneficiary {$number} Required",
                    html: "
                        <div style='text-align:left'>
                            <p><b>Beneficiary {$number} Full Name is required.</b></p>
                            <br>
                            <p>Please enter the full name of the beneficiary.</p>
                        </div>
                    "
                );

                return false;
            }

            /*
            |--------------------------------------------------------------------------
            | BIRTHDATE REQUIRED
            |--------------------------------------------------------------------------
            */
            if (empty($birthdate)) {

                $this->dispatch(
                    'validation-error',
                    title: "Beneficiary {$number} Birthdate Required",
                    html: "
                        <div style='text-align:left'>
                            <p><b>Birthdate is required for Beneficiary {$number}.</b></p>
                            <br>
                            <p>Please select the beneficiary's date of birth.</p>
                        </div>
                    "
                );

                return false;
            }

            /*
            |--------------------------------------------------------------------------
            | RELATIONSHIP REQUIRED
            |--------------------------------------------------------------------------
            */
            if (empty($relationship)) {

                $this->dispatch(
                    'validation-error',
                    title: "Beneficiary {$number} Relationship Required",
                    html: "
                        <div style='text-align:left'>
                            <p><b>Relationship is required for Beneficiary {$number}.</b></p>
                            <br>
                            <p>Example: Father, Mother, Spouse, Son, Daughter, etc.</p>
                        </div>
                    "
                );

                return false;
            }
        }

        return true;
    }

    public function updated($property, $value)
    {
        // 1. Mobile Number Formatting rules
        if ($property === 'mobile') {
            if (str_starts_with($value, '+63')) {
                $value = '09' . substr($value, 3);
            } elseif (str_starts_with($value, '63') && strlen($value) === 12) {
                $value = '0' . substr($value, 2);
            }

            $value = preg_replace('/\D/', '', $value);

            if (strlen($value) > 0 && !str_starts_with($value, '0')) {
                $value = '0' . $value;
            }
            if (strlen($value) > 1 && $value[1] !== '9') {
                $value = '09' . substr($value, 2);
            }

            $this->mobile = substr($value, 0, 11);
        }

        // 2. Title Case Formatting for Applicant Names & Address
        if (in_array($property, ['surname', 'first_name', 'middle_name', 'address'])) {
            $this->{$property} = ucwords(strtolower($value));
        }

        // 3. Title Case Formatting for Beneficiary Fullname & Relationship
        if (str_starts_with($property, 'beneficiaries.')) {
            $parts = explode('.', $property);
            if (isset($parts[2]) && in_array($parts[2], ['fullname', 'relationship'])) {
                $index = $parts[1];
                $field = $parts[2];
                $this->beneficiaries[$index][$field] = ucwords(strtolower($value));
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Beneficiary Delete Confirmation
    |--------------------------------------------------------------------------
    */
    public function confirmDeleteBeneficiary($index)
    {
        // Check if beneficiary actually has data
        $beneficiary = $this->beneficiaries[$index] ?? [];
        $hasData =
            !empty(trim($beneficiary['fullname'] ?? '')) ||
            !empty($beneficiary['birthdate'] ?? '') ||
            !empty(trim($beneficiary['relationship'] ?? ''));

        // If completely empty, no need confirmation
        if (!$hasData) {
            return;
        }

        $this->dispatch('confirm-delete-beneficiary', index: $index + 1);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Beneficiary
    |--------------------------------------------------------------------------
    */
    public function deleteBeneficiary($index)
    {
        if (!isset($this->beneficiaries[$index])) {
            return;
        }

        // Clear beneficiary fields
        $this->beneficiaries[$index] = [
            'fullname' => '',
            'birthdate' => '',
            'relationship' => '',
        ];

        $this->dispatch(
            'beneficiary-deleted',
            index: $index + 1
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Open Modal
    |--------------------------------------------------------------------------
    */
    #[On('open-application-modal')]
    public function open($upline = null)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $this->open = true;

        if ($upline) {
            $this->upline = $upline;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Close Modal
    |--------------------------------------------------------------------------
    */
    public function close()
    {
        $this->dispatch('destroy-map');

        $this->resetValidation();

        $this->reset([
            'surname',
            'first_name',
            'middle_name',
            'gender',
            'civil_status',
            'birthdate',
            'address',
            'mobile',
            'email',
            'username',
            'password',
            'password_confirmation',
            'beneficiaries',
            'userId',
            'latitude',
            'longitude',
            'policy_number'
        ]);

        $this->showMapModal = false;
        $this->isEditMode = false;
        $this->isViewMode = false;
        $this->open = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Validate and Prompt Confirmation
    |--------------------------------------------------------------------------
    */
    public function submit()
    {
        if (!$this->validateBeneficiaries()) {
            return;
        }

        Log::info('=== SUBMIT ATTEMPT STARTED ===', [
            'username' => $this->username,
            'email' => $this->email,
        ]);

        try {
            $this->validate([
                'surname' => 'required|string|max:255',
                'first_name' => 'required|string|max:255',
                'gender' => 'required',
                'civil_status' => 'required',
                'birthdate' => 'required|date',
                'address' => 'required|string|max:500',

                'beneficiaries.*.fullname' => 'required_with:beneficiaries.*.birthdate|nullable|string|max:150',
                'beneficiaries.*.birthdate' => 'required_with:beneficiaries.*.fullname|nullable|date',
                'beneficiaries.*.relationship' => 'nullable|string|max:50',

                'mobile' => 'required|digits:11',
                'email' => 'nullable|email|unique:users,email',
                'username' => 'required|string|max:100|unique:users,username',
                'password' => 'required|min:8|confirmed',
                'agree' => 'accepted',
            ]);

            Log::info('=== VALIDATION PASSED SUCCESSFULLY ===');

        } catch (ValidationException $e) {
            // Log the exact validation errors that occurred
            Log::warning('VALIDATION FAILED', [
                'errors' => $e->errors()
            ]);

            $errors = $e->validator->errors()->all();
            $html = '<ul style="text-align:left; list-style-type: disc; padding-left: 20px;">';

            foreach ($errors as $error) {
                $html .= "<li style='margin-bottom:6px'>{$error}</li>";
            }

            $html .= '</ul>';

            $this->dispatch(
                'validation-error',
                title: 'Missing or Invalid Fields',
                html: $html
            );

            return;
        }

        // Trigger SweetAlert Confirmation if validation passes
        Log::info('=== DISPATCHING SWAL CONFIRMATION ===');
        $this->dispatch('swal-confirm-submission');
    }

    /*
    |--------------------------------------------------------------------------
    | Save Application (Called after confirmation)
    |--------------------------------------------------------------------------
    */
    #[On('confirmed-save-application')]
    public function saveApplication()
    {
        DB::beginTransaction();

        try {
            Log::info('=== SAVE APPLICATION STARTED ===', [
                'username' => $this->username,
                'email' => $this->email,
            ]);

            do {
                $referralCode = $this->generateUplineCode();
                $exists = User::where('referralCode', $referralCode)->exists();
            } while ($exists);

            $user = User::create([
                'username' => $this->username,
                'password' => bcrypt($this->password),
                'referralCode' => $referralCode,
                'uplineCode' => $this->upline, // ✅ Correct
                'lastname' => $this->surname,
                'firstname' => $this->first_name,
                'middlename' => $this->middle_name,
                'gender' => $this->gender,
                'civilstatus' => $this->civil_status,
                'birthdate' => $this->birthdate,
                'contactno' => $this->mobile,
                'email' => filled($this->email) ? $this->email : null,
                'address' => $this->address,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'RoleID' => 4,
                'status' => 'Inactive',
            ]);

            Log::info('User created successfully', [
                'UserID' => $user->UserID,
            ]);

            Log::info('Saving beneficiaries');

            foreach ($this->beneficiaries as $beneficiary) {
                Log::info('Beneficiary data', $beneficiary);
            }

            Log::info('Sending emails');

            foreach ($this->beneficiaries as $beneficiary) {
                if (
                    !empty($beneficiary['fullname']) &&
                    !empty($beneficiary['birthdate'])
                ) {
                    Beneficiary::create([
                        'UserID' => $user->UserID,
                        'fullname' => $beneficiary['fullname'],
                        'birthdate' => $beneficiary['birthdate'],
                        'relationship' => $beneficiary['relationship'] ?? null,
                        'status' => true,
                    ]);
                }
            }

            // Prepare base details array for emails
            $applicationDetails = [
                'surname' => $this->surname,
                'first_name' => $this->first_name,
                'middle_name' => $this->middle_name,
                'gender' => $this->gender,
                'civil_status' => $this->civil_status,
                'birthdate' => $this->birthdate,
                'address' => $this->address,
                'mobile' => $this->mobile,
                'email' => $this->email,
                'username' => $this->username,
                'beneficiaries' => $this->beneficiaries,
            ];

            // Get all users with RoleID 1, 2, or 3
            $admins = User::whereIn('RoleID', [1])
                ->whereNotNull('email')
                ->get();

            foreach ($admins as $admin) {

                $adminData = array_merge($applicationDetails, [
                    'title' => 'New Member Application Notice (Admin)',
                    'includeCredentials' => false,
                ]);

                Mail::send('emails.new-application', $adminData, function ($message) use ($admin) {
                    $message->to($admin->email)
                            ->subject('New Member Application Submitted');
                });
            }

            // 2. Send Email to User (Includes password and optional profile photo/attachment)
            if (filled($this->email)) {
                $userData = array_merge($applicationDetails, [
                    'title' => 'Welcome to True Life - Application Details',
                    'password' => $this->password, // Plain text password for their reference upon signup
                    'includeCredentials' => true,
                ]);

                Mail::send('emails.new-application', $userData, function ($message) use ($userData) {
                    $message->to($userData['email'])
                            ->subject('Your Application Details & Credentials');

                    // Attach benefits image
                    $benefitsPath = public_path('images/benefits.jpeg');
                    if (file_exists($benefitsPath)) {
                        $message->attach($benefitsPath, [
                            'as' => 'benefits.png',
                            'mime' => 'image/png',
                        ]);
                    }

                    // Attach second image (e.g. profile photo or another asset)
                    $secondPath = public_path('images/second-image.jpeg');
                    if (file_exists($secondPath)) {
                        $message->attach($secondPath, [
                            'as' => 'benefits.png',
                            'mime' => 'image/png',
                        ]);
                    }
                });
            }

            DB::commit();

            Log::info('=== SAVE APPLICATION FINISHED ===');

            $this->dispatch('swal-close');

            $this->dispatch(
                'show-success-after-close',
                title: 'Success!',
                text: 'Application submitted successfully.'
            );

            $this->dispatch('refresh-members');
            $this->close();

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('APPLICATION FAILED', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('swal-close');

            $this->dispatch('validation-error',
                title: 'Error',
                html: 'Something went wrong: ' . htmlspecialchars($e->getMessage())
            );
        }
    }

    #[On('confirmed-update-member')]
    public function updateMember(): void
    {
        $this->validate([
            'surname' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'gender' => 'required',
            'civil_status' => 'required',
            'birthdate' => 'required|date',
            'address' => 'required|string|max:500',

            'beneficiaries.*.fullname' => 'required_with:beneficiaries.*.birthdate|nullable|string|max:150',
            'beneficiaries.*.birthdate' => 'required_with:beneficiaries.*.fullname|nullable|date',
            'beneficiaries.*.relationship' => 'nullable|string|max:50',

            'mobile' => 'required|digits:11',
            'email' => 'nullable|email|unique:users,email,' . $this->userId . ',UserID',
            'username' => 'required|string|max:100|unique:users,username,' . $this->userId . ',UserID',
            'policy_number' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($this->userId);

            $user->update([
                'lastname' => $this->surname,
                'firstname' => $this->first_name,
                'middlename' => $this->middle_name,
                'gender' => $this->gender,
                'civilstatus' => $this->civil_status,
                'birthdate' => $this->birthdate,
                'contactno' => $this->mobile,
                'email' => filled($this->email) ? $this->email : null,
                'username' => $this->username,
                'address' => $this->address,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'policy_number' => $this->policy_number, // <-- add this
            ]);

            // Delete old beneficiaries
            Beneficiary::where('UserID', $user->UserID)->delete();

            // Save updated beneficiaries
            foreach ($this->beneficiaries as $beneficiary) {

                $fullname = trim($beneficiary['fullname'] ?? '');
                $birthdate = $beneficiary['birthdate'] ?? '';
                $relationship = trim($beneficiary['relationship'] ?? '');

                // Skip completely empty beneficiary slots
                if (empty($fullname) && empty($birthdate) && empty($relationship)) {
                    continue;
                }

                // Only save valid beneficiary
                if (!empty($fullname) && !empty($birthdate)) {

                    Beneficiary::create([
                        'UserID' => $user->UserID,
                        'fullname' => $fullname,
                        'birthdate' => $birthdate,
                        'relationship' => $relationship ?: null,
                        'status' => true,
                    ]);
                }
            }

            DB::commit();

            $this->dispatch('swal-close');

            $this->dispatch(
                'show-success-after-close',
                title: 'Updated!',
                text: 'Member updated successfully.'
            );

            $this->dispatch('refresh-members');
            $this->close();

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Update failed', [
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('swal-close');

            $this->dispatch(
                'validation-error',
                title: 'Error',
                html: 'Failed to update member.'
            );
        }
    }

    public function confirmUpdate()
    {
        if (!$this->validateBeneficiaries()) {
            return;
        }

        $this->dispatch('swal-confirm-update');
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Upline Code
    |--------------------------------------------------------------------------
    */
    private function generateUplineCode(): string
    {
        return strtoupper(
            substr(
                md5(
                    uniqid(
                        mt_rand(),
                        true
                    )
                ),
                0,
                6
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */
    public function render()
    {
        return view('livewire.application-modal');
    }

    public function toggleMap(): void
    {
        $this->showMapModal = ! $this->showMapModal;

        if ($this->showMapModal) {
            $this->dispatch(
                'map-opened',
                latitude: $this->latitude,
                longitude: $this->longitude
            );
        }
    }

    #[On('set-coordinates')]
    public function setCoordinates($lat, $lng, $address = null): void
    {
        $this->latitude = (float) $lat;
        $this->longitude = (float) $lng;

        // If the geocoder returned a place name, auto-fill the address field
        if (!empty($address)) {
            $this->address = ucwords(strtolower($address));
        }

        logger([
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $this->address,
        ]);
    }

    #[On('open-update-member')]
    public function openEdit($UserID): void
    {
        $user = User::with('beneficiaries')->findOrFail($UserID);

        $this->fillUserData($user);

        $this->userId = $user->UserID;
        $this->isViewMode = false;
        $this->isEditMode = true;
        $this->open = true;
    }

    private function fillUserData(User $user): void
    {
        $this->surname = $user->lastname ?? '';
        $this->first_name = $user->firstname ?? '';
        $this->middle_name = $user->middlename ?? '';
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
        $this->policy_number = $user->policy_number ?? '';

        /*
        |--------------------------------------------------------------------------
        | ALWAYS CREATE 3 BENEFICIARY SLOTS
        |--------------------------------------------------------------------------
        */
        $this->beneficiaries = [
            [
                'fullname' => '',
                'birthdate' => '',
                'relationship' => '',
            ],
            [
                'fullname' => '',
                'birthdate' => '',
                'relationship' => '',
            ],
            [
                'fullname' => '',
                'birthdate' => '',
                'relationship' => '',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | FILL EXISTING BENEFICIARIES
        |--------------------------------------------------------------------------
        */
        foreach ($user->beneficiaries->take(3)->values() as $index => $beneficiary) {

            $this->beneficiaries[$index] = [
                'fullname' => $beneficiary->fullname ?? '',

                'birthdate' => $beneficiary->birthdate
                    ? \Carbon\Carbon::parse($beneficiary->birthdate)->format('Y-m-d')
                    : '',

                'relationship' => $beneficiary->relationship ?? '',
            ];
        }
    }
}
