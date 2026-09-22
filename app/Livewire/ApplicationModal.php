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
    public $policy_number = null;
    public $policy_effectivity_date = null;
    public $policy_expiration_date = null;

    public $upline = '';

    public $beneficiaryToDelete = null;

    /*
    |--------------------------------------------------------------------------
    | Beneficiary Information
    |--------------------------------------------------------------------------
    */
    public array $beneficiaries = [
        ['fullname' => '', 'birthdate' => '', 'relationship' => ''],
        ['fullname' => '', 'birthdate' => '', 'relationship' => ''],
        ['fullname' => '', 'birthdate' => '', 'relationship' => ''],
    ];

    /**
     * Tracks the DB id of each beneficiary slot (null = not yet saved).
     * Used to decide whether a slot's action button is "Delete" (saved,
     * removes from DB immediately) or "Clear" (just empties the fields).
     */
    public array $beneficiaryIds = [null, null, null];

    /**
     * Snapshot of the record's original values when opened in edit mode,
     * used to build the "what changed" diff before saving an update.
     */
    public array $originalData = [];

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

            if (empty($fullname) && empty($birthdate) && empty($relationship)) {
                continue;
            }

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

        if (in_array($property, ['surname', 'first_name', 'middle_name', 'occupation', 'address'])) {
            $this->{$property} = ucwords(strtolower($value));
        }

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
    | Beneficiary Delete/Clear Confirmation
    |--------------------------------------------------------------------------
    */
    public function confirmDeleteBeneficiary($index)
    {
        $beneficiary = $this->beneficiaries[$index] ?? [];
        $hasData =
            !empty(trim($beneficiary['fullname'] ?? '')) ||
            !empty($beneficiary['birthdate'] ?? '') ||
            !empty(trim($beneficiary['relationship'] ?? ''));

        // Nothing typed and nothing saved — no need to confirm anything.
        if (!$hasData) {
            return;
        }

        $isPersisted = !empty($this->beneficiaryIds[$index]);

        $this->dispatch(
            'confirm-delete-beneficiary',
            index: $index + 1,
            persisted: $isPersisted
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete/Clear Beneficiary
    |--------------------------------------------------------------------------
    | - If this slot has a saved beneficiary (beneficiaryIds[$index] is set),
    |   delete the row from the database immediately.
    | - Either way, clear the local fields for that slot.
    */
    public function deleteBeneficiary($index)
    {
        if (!isset($this->beneficiaries[$index])) {
            return;
        }

        $wasPersisted = !empty($this->beneficiaryIds[$index]);

        if ($wasPersisted) {
            Beneficiary::destroy($this->beneficiaryIds[$index]);
            $this->beneficiaryIds[$index] = null;
        }

        $this->beneficiaries[$index] = [
            'fullname' => '',
            'birthdate' => '',
            'relationship' => '',
        ];

        $this->dispatch(
            'beneficiary-deleted',
            index: $index + 1,
            persisted: $wasPersisted
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
            'occupation',
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
            'beneficiaryIds',
            'originalData',
            'userId',
            'latitude',
            'longitude',
            'policy_number',
            'policy_effectivity_date',
            'policy_expiration_date'
        ]);

        $this->showMapModal = false;
        $this->isEditMode = false;
        $this->isViewMode = false;
        $this->open = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Validate and Prompt Confirmation (New Application)
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
                'occupation' => 'nullable|string|max:255',

                'policy_number' => 'nullable|string|max:100',
                'policy_effectivity_date' => 'nullable|date',
                'policy_expiration_date' => 'nullable|date',

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

        Log::info('=== DISPATCHING SWAL CONFIRMATION ===');
        $this->dispatch('swal-confirm-submission');
    }

    /*
    |--------------------------------------------------------------------------
    | Save Application (Called after confirmation)
    |--------------------------------------------------------------------------
    */
    #[On('confirmed-save-application')]
    /*
|--------------------------------------------------------------------------
| Save Application (Called after confirmation)
|--------------------------------------------------------------------------
*/
#[On('confirmed-save-application')]
public function saveApplication()
{
    try {

        /*
        |--------------------------------------------------------------------------
        | START DATABASE TRANSACTION
        |--------------------------------------------------------------------------
        */
        DB::beginTransaction();

        Log::info('=== SAVE APPLICATION STARTED ===', [
            'username' => $this->username,
            'email' => $this->email,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Generate Unique Referral Code
        |--------------------------------------------------------------------------
        */
        do {
            $referralCode = $this->generateUplineCode();
            $exists = User::where('referralCode', $referralCode)->exists();
        } while ($exists);

        /*
        |--------------------------------------------------------------------------
        | Create User
        |--------------------------------------------------------------------------
        */
        $user = User::create([
            'username' => $this->username,
            'password' => bcrypt($this->password),
            'referralCode' => $referralCode,
            'uplineCode' => $this->upline,
            'lastname' => $this->surname,
            'firstname' => $this->first_name,
            'middlename' => $this->middle_name,
            'gender' => $this->gender,
            'civilstatus' => $this->civil_status,
            'birthdate' => $this->birthdate,
            'occupation' => $this->occupation,
            'policy_number' => $this->policy_number ?: null,
            'policy_effectivity_date' => $this->policy_effectivity_date ?: null,
            'policy_expiration_date' => $this->policy_expiration_date ?: null,
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

        /*
        |--------------------------------------------------------------------------
        | Save Beneficiaries
        |--------------------------------------------------------------------------
        */
        Log::info('Saving beneficiaries');

        foreach ($this->beneficiaries as $beneficiary) {

            Log::info('Beneficiary data', $beneficiary);

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

        /*
        |--------------------------------------------------------------------------
        | Prepare Application Details
        |--------------------------------------------------------------------------
        */
        $applicationDetails = [
            'surname' => $this->surname,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'occupation' => $this->occupation,
            'gender' => $this->gender,
            'civil_status' => $this->civil_status,
            'birthdate' => $this->birthdate,
            'policy_number' => $this->policy_number,
            'policy_effectivity_date' => $this->policy_effectivity_date,
            'policy_expiration_date' => $this->policy_expiration_date,
            'address' => $this->address,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'username' => $this->username,
            'beneficiaries' => $this->beneficiaries,
        ];

        /*
        |--------------------------------------------------------------------------
        | COMMIT DATABASE FIRST
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Do NOT put Mail::send() before this commit.
        |
        */
        DB::commit();

        Log::info('=== DATABASE TRANSACTION COMMITTED ===', [
            'UserID' => $user->UserID,
        ]);

        /*
        |--------------------------------------------------------------------------
        | SEND EMAILS AFTER DATABASE COMMIT
        |--------------------------------------------------------------------------
        |
        | Email failure will NOT rollback the saved application.
        |
        */

        $emailErrors = [];

        /*
        |--------------------------------------------------------------------------
        | Send Admin Notification Emails
        |--------------------------------------------------------------------------
        */
        try {

            $admins = User::whereIn('RoleID', [1])
                ->whereNotNull('email')
                ->get();

            foreach ($admins as $admin) {

                try {

                    $adminData = array_merge($applicationDetails, [
                        'title' => 'New Member Application Notice (Admin)',
                        'includeCredentials' => false,
                    ]);

                    Mail::send(
                        'emails.new-application',
                        $adminData,
                        function ($message) use ($admin) {

                            $message->to($admin->email)
                                ->subject('New Member Application Submitted');
                        }
                    );

                    Log::info('Admin application email sent successfully', [
                        'admin_email' => $admin->email,
                        'UserID' => $user->UserID,
                    ]);

                } catch (\Throwable $e) {

                    $emailErrors[] = [
                        'type' => 'admin',
                        'email' => $admin->email,
                        'error' => $e->getMessage(),
                    ];

                    Log::error('Failed to send admin application email', [
                        'admin_email' => $admin->email,
                        'UserID' => $user->UserID,
                        'message' => $e->getMessage(),
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | IMPORTANT:
                    | Continue processing instead of throwing the exception.
                    |--------------------------------------------------------------------------
                    */
                    continue;
                }
            }

        } catch (\Throwable $e) {

            Log::error('Admin email process failed', [
                'UserID' => $user->UserID,
                'message' => $e->getMessage(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Send Applicant Email
        |--------------------------------------------------------------------------
        */
        if (filled($this->email)) {

            try {

                $userData = array_merge($applicationDetails, [
                    'title' => 'Welcome to True Life - Application Details',
                    'password' => $this->password,
                    'includeCredentials' => true,
                ]);

                Mail::send(
                    'emails.new-application',
                    $userData,
                    function ($message) use ($userData) {

                        $message->to($userData['email'])
                            ->subject('Your Application Details & Credentials');

                        /*
                        |--------------------------------------------------------------------------
                        | Attach Benefits Image
                        |--------------------------------------------------------------------------
                        */
                        $benefitsPath = public_path('images/benefits.jpeg');

                        if (file_exists($benefitsPath)) {

                            $message->attach($benefitsPath, [
                                'as' => 'benefits.png',
                                'mime' => 'image/png',
                            ]);
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Attach Second Image
                        |--------------------------------------------------------------------------
                        */
                        $secondPath = public_path('images/second-image.jpeg');

                        if (file_exists($secondPath)) {

                            $message->attach($secondPath, [
                                'as' => 'second-image.png',
                                'mime' => 'image/png',
                            ]);
                        }
                    }
                );

                Log::info('Applicant email sent successfully', [
                    'email' => $this->email,
                    'UserID' => $user->UserID,
                ]);

            } catch (\Throwable $e) {

                $emailErrors[] = [
                    'type' => 'applicant',
                    'email' => $this->email,
                    'error' => $e->getMessage(),
                ];

                Log::error('Failed to send applicant email', [
                    'email' => $this->email,
                    'UserID' => $user->UserID,
                    'message' => $e->getMessage(),
                ]);

                /*
                |--------------------------------------------------------------------------
                | DO NOT THROW THE ERROR
                |--------------------------------------------------------------------------
                |
                | The application has already been successfully saved.
                |
                */
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Application Successfully Saved
        |--------------------------------------------------------------------------
        */

        Log::info('=== SAVE APPLICATION FINISHED ===', [
            'UserID' => $user->UserID,
            'email_errors' => count($emailErrors),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Close Confirmation Dialog
        |--------------------------------------------------------------------------
        */
        $this->dispatch('swal-close');

        /*
        |--------------------------------------------------------------------------
        | Show Success Message
        |--------------------------------------------------------------------------
        */
        $this->dispatch(
            'show-success-after-close',
            title: 'Success!',
            text: 'Application submitted successfully.'
        );

        /*
        |--------------------------------------------------------------------------
        | Toast Notification
        |--------------------------------------------------------------------------
        */
        $this->dispatch(
            'show-toast',
            title: 'Application submitted successfully!',
            icon: 'success'
        );

        /*
        |--------------------------------------------------------------------------
        | Refresh Members
        |--------------------------------------------------------------------------
        */
        $this->dispatch('refresh-members');

        /*
        |--------------------------------------------------------------------------
        | Close Application Form
        |--------------------------------------------------------------------------
        */
        $this->close();

    } catch (\Throwable $e) {

        /*
        |--------------------------------------------------------------------------
        | ONLY DATABASE ERRORS SHOULD REACH THIS BLOCK
        |--------------------------------------------------------------------------
        */

        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        Log::error('APPLICATION DATABASE FAILED', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => $e->getTraceAsString(),
        ]);

        $this->dispatch('swal-close');

        $this->dispatch(
            'validation-error',
            title: 'Error',
            html: 'Something went wrong while saving the application. Please try again.'
        );
    }
}

    // public function saveApplication()
    // {
    //     DB::beginTransaction();

    //     try {
    //         Log::info('=== SAVE APPLICATION STARTED ===', [
    //             'username' => $this->username,
    //             'email' => $this->email,
    //         ]);

    //         do {
    //             $referralCode = $this->generateUplineCode();
    //             $exists = User::where('referralCode', $referralCode)->exists();
    //         } while ($exists);

    //         $user = User::create([
    //             'username' => $this->username,
    //             'password' => bcrypt($this->password),
    //             'referralCode' => $referralCode,
    //             'uplineCode' => $this->upline,
    //             'lastname' => $this->surname,
    //             'firstname' => $this->first_name,
    //             'middlename' => $this->middle_name,
    //             'gender' => $this->gender,
    //             'civilstatus' => $this->civil_status,
    //             'birthdate' => $this->birthdate,
    //             'occupation' => $this->occupation,
    //             'policy_number' => $this->policy_number ?: null,
    //             'policy_effectivity_date' => $this->policy_effectivity_date ?: null,
    //             'policy_expiration_date' => $this->policy_expiration_date ?: null,
    //             'contactno' => $this->mobile,
    //             'email' => filled($this->email) ? $this->email : null,
    //             'address' => $this->address,
    //             'latitude' => $this->latitude,
    //             'longitude' => $this->longitude,
    //             'RoleID' => 4,
    //             'status' => 'Inactive',
    //         ]);

    //         Log::info('User created successfully', [
    //             'UserID' => $user->UserID,
    //         ]);

    //         Log::info('Saving beneficiaries');

    //         foreach ($this->beneficiaries as $beneficiary) {
    //             Log::info('Beneficiary data', $beneficiary);
    //         }

    //         Log::info('Sending emails');

    //         foreach ($this->beneficiaries as $beneficiary) {
    //             if (!empty($beneficiary['fullname']) && !empty($beneficiary['birthdate'])) {
    //                 Beneficiary::create([
    //                     'UserID' => $user->UserID,
    //                     'fullname' => $beneficiary['fullname'],
    //                     'birthdate' => $beneficiary['birthdate'],
    //                     'relationship' => $beneficiary['relationship'] ?? null,
    //                     'status' => true,
    //                 ]);
    //             }
    //         }

    //         $applicationDetails = [
    //             'surname' => $this->surname,
    //             'first_name' => $this->first_name,
    //             'middle_name' => $this->middle_name,
    //             'occupation' => $this->occupation,
    //             'gender' => $this->gender,
    //             'civil_status' => $this->civil_status,
    //             'birthdate' => $this->birthdate,
    //             'policy_number' => $this->policy_number,
    //             'policy_effectivity_date' => $this->policy_effectivity_date,
    //             'policy_expiration_date' => $this->policy_expiration_date,
    //             'address' => $this->address,
    //             'mobile' => $this->mobile,
    //             'email' => $this->email,
    //             'username' => $this->username,
    //             'beneficiaries' => $this->beneficiaries,
    //         ];

    //         $admins = User::whereIn('RoleID', [1])
    //             ->whereNotNull('email')
    //             ->get();

    //         foreach ($admins as $admin) {
    //             $adminData = array_merge($applicationDetails, [
    //                 'title' => 'New Member Application Notice (Admin)',
    //                 'includeCredentials' => false,
    //             ]);

    //             Mail::send('emails.new-application', $adminData, function ($message) use ($admin) {
    //                 $message->to($admin->email)
    //                         ->subject('New Member Application Submitted');
    //             });
    //         }

    //         if (filled($this->email)) {
    //             $userData = array_merge($applicationDetails, [
    //                 'title' => 'Welcome to True Life - Application Details',
    //                 'password' => $this->password,
    //                 'includeCredentials' => true,
    //             ]);

    //             Mail::send('emails.new-application', $userData, function ($message) use ($userData) {
    //                 $message->to($userData['email'])
    //                         ->subject('Your Application Details & Credentials');

    //                 $benefitsPath = public_path('images/benefits.jpeg');
    //                 if (file_exists($benefitsPath)) {
    //                     $message->attach($benefitsPath, [
    //                         'as' => 'benefits.png',
    //                         'mime' => 'image/png',
    //                     ]);
    //                 }

    //                 $secondPath = public_path('images/second-image.jpeg');
    //                 if (file_exists($secondPath)) {
    //                     $message->attach($secondPath, [
    //                         'as' => 'benefits.png',
    //                         'mime' => 'image/png',
    //                     ]);
    //                 }
    //             });
    //         }

    //         DB::commit();

    //         Log::info('=== SAVE APPLICATION FINISHED ===');

    //         $this->dispatch('swal-close');

    //         $this->dispatch(
    //             'show-success-after-close',
    //             title: 'Success!',
    //             text: 'Application submitted successfully.'
    //         );

    //         $this->dispatch(
    //             'show-toast',
    //             title: 'Application submitted successfully!',
    //             icon: 'success'
    //         );

    //         $this->dispatch('refresh-members');
    //         $this->close();

    //     } catch (\Throwable $e) {
    //         DB::rollBack();

    //         Log::error('APPLICATION FAILED', [
    //             'message' => $e->getMessage(),
    //             'line' => $e->getLine(),
    //             'file' => $e->getFile(),
    //             'trace' => $e->getTraceAsString(),
    //         ]);

    //         $this->dispatch('swal-close');

    //         $this->dispatch('validation-error',
    //             title: 'Error',
    //             html: 'Something went wrong: ' . htmlspecialchars($e->getMessage())
    //         );
    //     }
    // }

    /*
    |--------------------------------------------------------------------------
    | Confirm Update — build the "what changed" diff, then ask JS to show it
    |--------------------------------------------------------------------------
    */
    public function confirmUpdate()
    {
        if (!$this->validateBeneficiaries()) {
            return;
        }

        // Normalize optional policy fields
        $this->policy_number = blank($this->policy_number)
            ? null
            : trim($this->policy_number);

        $this->policy_effectivity_date = blank($this->policy_effectivity_date)
            ? null
            : trim($this->policy_effectivity_date);

        $this->policy_expiration_date = blank($this->policy_expiration_date)
            ? null
            : trim($this->policy_expiration_date);


        try {
            $this->validate([
                'surname' => 'required|string|max:255',
                'first_name' => 'required|string|max:255',
                'gender' => 'required',
                'civil_status' => 'required',
                'birthdate' => 'required|date',
                'occupation' => 'nullable|string|max:255',
                'address' => 'required|string|max:500',

                'mobile' => 'required|digits:11',

                'email' => 'nullable|email|unique:users,email,' . $this->userId . ',UserID',

                'username' => 'required|string|max:100|unique:users,username,' . $this->userId . ',UserID',

                'policy_number' => 'nullable|string|max:100',
                'policy_effectivity_date' => 'nullable|date',
                'policy_expiration_date' => 'nullable|date',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->all();

            $html = '<ul style="text-align:left; list-style-type:disc; padding-left:20px;">';

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

        $changes = $this->getChangedFields();

        if (empty($changes)) {
            $this->dispatch(
                'validation-error',
                title: 'No Changes',
                html: 'There are no changes to save.'
            );

            return;
        }

        $this->dispatch(
            'swal-confirm-update',
            changes: $changes
        );
    }

    /**
     * Compare current field values against the snapshot taken when the
     * record was loaded for editing, and return a list of changed fields
     * in a shape the SweetAlert diff table can render directly.
     */
    private function getChangedFields(): array
    {
        $labels = [
            'surname' => 'Surname',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'occupation' => 'Occupation',
            'gender' => 'Gender',
            'civil_status' => 'Civil Status',
            'birthdate' => 'Birthdate',
            'address' => 'Address',
            'mobile' => 'Mobile Number',
            'email' => 'Email',
            'username' => 'Username',
            'policy_number' => 'Policy Number',
            'policy_effectivity_date' => 'Policy Effectivity Date',
            'policy_expiration_date' => 'Policy Expiration Date',
        ];

        $changes = [];

        foreach ($labels as $field => $label) {
            $old = (string) ($this->originalData[$field] ?? '');
            $new = (string) ($this->{$field} ?? '');

            if ($old !== $new) {
                $changes[] = [
                    'field' => $label,
                    'old' => $old !== '' ? $old : '—',
                    'new' => $new !== '' ? $new : '—',
                ];
            }
        }

        $oldBeneficiaries = $this->originalData['beneficiaries'] ?? [];

        $formatBeneficiary = function ($b) {
            if (empty($b['fullname']) && empty($b['birthdate']) && empty($b['relationship'])) {
                return '—';
            }
            return trim(($b['fullname'] ?? '') . ' – ' . ($b['relationship'] ?? '') . ' (' . ($b['birthdate'] ?? '') . ')');
        };

        foreach ($this->beneficiaries as $i => $beneficiary) {
            $old = $oldBeneficiaries[$i] ?? ['fullname' => '', 'birthdate' => '', 'relationship' => ''];

            $unchanged =
                ($old['fullname'] ?? '') === ($beneficiary['fullname'] ?? '') &&
                ($old['birthdate'] ?? '') === ($beneficiary['birthdate'] ?? '') &&
                ($old['relationship'] ?? '') === ($beneficiary['relationship'] ?? '');

            if ($unchanged) {
                continue;
            }

            $changes[] = [
                'field' => 'Beneficiary ' . ($i + 1),
                'old' => $formatBeneficiary($old),
                'new' => $formatBeneficiary($beneficiary),
            ];
        }

        return $changes;
    }

    /*
    |--------------------------------------------------------------------------
    | Update Member (Called after diff confirmation)
    |--------------------------------------------------------------------------
    */
    #[On('confirmed-update-member')]
    public function updateMember(): void
    {

        $this->policy_number = blank($this->policy_number)
        ? null
        : trim($this->policy_number);

        $this->policy_effectivity_date = blank($this->policy_effectivity_date)
        ? null
        : trim($this->policy_effectivity_date);

        $this->policy_expiration_date = blank($this->policy_expiration_date)
        ? null
        : trim($this->policy_expiration_date);


        $this->validate([
            'surname' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'gender' => 'required',
            'civil_status' => 'required',
            'birthdate' => 'required|date',
            'occupation' => 'nullable|string|max:255',
            'address' => 'required|string|max:500',

            'beneficiaries.*.fullname' => 'required_with:beneficiaries.*.birthdate|nullable|string|max:150',
            'beneficiaries.*.birthdate' => 'required_with:beneficiaries.*.fullname|nullable|date',
            'beneficiaries.*.relationship' => 'nullable|string|max:50',

            'mobile' => 'required|digits:11',
            'email' => 'nullable|email|unique:users,email,' . $this->userId . ',UserID',
            'username' => 'required|string|max:100|unique:users,username,' . $this->userId . ',UserID',
            'policy_number' => 'nullable|string|max:100',
            'policy_effectivity_date' => 'nullable|date',
            'policy_expiration_date' => 'nullable|date',
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
                'occupation' => $this->occupation,
                'contactno' => $this->mobile,
                'email' => filled($this->email) ? $this->email : null,
                'username' => $this->username,
                'address' => $this->address,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'policy_number' => $this->policy_number,
                'policy_effectivity_date' => $this->policy_effectivity_date,
                'policy_expiration_date' => $this->policy_expiration_date,
            ]);

            // Delete old beneficiaries and re-create from the current form state.
            Beneficiary::where('UserID', $user->UserID)->delete();

            foreach ($this->beneficiaries as $beneficiary) {

                $fullname = trim($beneficiary['fullname'] ?? '');
                $birthdate = $beneficiary['birthdate'] ?? '';
                $relationship = trim($beneficiary['relationship'] ?? '');

                if (empty($fullname) && empty($birthdate) && empty($relationship)) {
                    continue;
                }

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

            $this->dispatch(
                'show-toast',
                title: 'Member updated successfully!',
                icon: 'success'
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

    public function confirmUpdateLegacyAlias()
    {
        // kept for backward compatibility if referenced elsewhere; not used directly
        $this->confirmUpdate();
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Upline Code
    |--------------------------------------------------------------------------
    */
    private function generateUplineCode(): string
    {
        return strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
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
        // Policy fields
        $this->policy_number = $user->policy_number ?: null;

        $this->policy_effectivity_date = $user->policy_effectivity_date
            ? \Carbon\Carbon::parse($user->policy_effectivity_date)->format('Y-m-d')
            : null;

        $this->policy_expiration_date = $user->policy_expiration_date
            ? \Carbon\Carbon::parse($user->policy_expiration_date)->format('Y-m-d')
            : null;


        // Reset both the form slots and the "which slot is a saved DB row" tracker.
        $this->beneficiaries = [
            ['fullname' => '', 'birthdate' => '', 'relationship' => ''],
            ['fullname' => '', 'birthdate' => '', 'relationship' => ''],
            ['fullname' => '', 'birthdate' => '', 'relationship' => ''],
        ];
        $this->beneficiaryIds = [null, null, null];

        foreach ($user->beneficiaries->take(3)->values() as $index => $beneficiary) {

            $this->beneficiaryIds[$index] = $beneficiary->getKey();

            $this->beneficiaries[$index] = [
                'fullname' => $beneficiary->fullname ?? '',
                'birthdate' => $beneficiary->birthdate
                    ? \Carbon\Carbon::parse($beneficiary->birthdate)->format('Y-m-d')
                    : '',
                'relationship' => $beneficiary->relationship ?? '',
            ];
        }

        // Snapshot for the "what changed" diff shown before Update.
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
            'beneficiaries' => $this->beneficiaries,
        ];
    }
}
