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



    public function updated($property, $value)
    {
        // 1. Mobile Number Formatting rules
        if ($property === 'mobile') {
            // Replace +63 or 63 prefix with 09
            if (str_starts_with($value, '+63')) {
                $value = '09' . substr($value, 3);
            } elseif (str_starts_with($value, '63') && strlen($value) === 12) {
                $value = '0' . substr($value, 2);
            }

            // Keep digits only
            $value = preg_replace('/\D/', '', $value);

            // Ensure it starts with '09' if user starts typing something else
            if (strlen($value) > 0 && !str_starts_with($value, '0')) {
                $value = '0' . $value;
            }
            if (strlen($value) > 1 && $value[1] !== '9') {
                $value = '09' . substr($value, 2);
            }

            // Limit strictly to 11 digits
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
    | Open Modal
    |--------------------------------------------------------------------------
    */

    #[On('open-application-modal')]
    public function open()
    {
        $this->open = true;
    }





    /*
    |--------------------------------------------------------------------------
    | Close Modal
    |--------------------------------------------------------------------------
    */

    public function close()
    {
        $this->reset();
        $this->resetValidation();
        $this->open = false;
    }





    /*
    |--------------------------------------------------------------------------
    | Validate and Prompt Confirmation
    |--------------------------------------------------------------------------
    */

    public function submit()
    {
        try {

            $this->validate([
                'surname' => 'required|string|max:255',
                'first_name' => 'required|string|max:255',
                'gender' => 'required',
                'civil_status' => 'required',
                'birthdate' => 'required|date',
                'address' => 'required|string|max:500',

                'beneficiaries.*.fullname' => 'nullable|string|max:150',
                'beneficiaries.*.birthdate' => 'nullable|date',
                'beneficiaries.*.relationship' => 'nullable|string|max:50',

                'mobile' => 'required|digits:11',
                'email' => 'nullable|email|unique:users,email',
                'username' => 'required|string|max:100|unique:users,username',
                'password' => 'required|min:8|confirmed',
                'agree' => 'accepted',
            ]);

        } catch (ValidationException $e) {

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

            do {
                $referralCode = $this->generateUplineCode();
                $exists = User::where('referralCode', $referralCode)->exists();
            } while ($exists);

            $user = User::create([
                'username' => $this->username,
                'password' => $this->password,
                'referralCode' => $referralCode,
                'lastname' => $this->surname,
                'firstname' => $this->first_name,
                'middlename' => $this->middle_name,
                'gender' => $this->gender,
                'civilstatus' => $this->civil_status,
                'birthdate' => $this->birthdate,
                'contactno' => $this->mobile,
                'email' => filled($this->email) ? $this->email : null,
                'address' => $this->address,
                'RoleID' => 5,
                'status' => 'Inactive',
            ]);

            foreach ($this->beneficiaries as $beneficiary) {
                if (filled($beneficiary['fullname']) && filled($beneficiary['birthdate'])) {
                    Beneficiary::create([
                        'BenefitID' => 1,
                        'UserID' => $user->UserID,
                        'fullname' => $beneficiary['fullname'],
                        'birthdate' => $beneficiary['birthdate'],
                        'relationship' => $beneficiary['relationship'],
                        'status' => true,
                    ]);
                }
            }

            DB::commit();

            // Prepare Beneficiaries details text for the email body
            $beneficiariesText = "";
            foreach ($this->beneficiaries as $index => $beneficiary) {
                if (filled($beneficiary['fullname']) || filled($beneficiary['birthdate']) || filled($beneficiary['relationship'])) {
                    $num = $index + 1;
                    $beneficiariesText .= "  - Beneficiary {$num}:\n";
                    $beneficiariesText .= "    Fullname: " . ($beneficiary['fullname'] ?: 'N/A') . "\n";
                    $beneficiariesText .= "    Birthdate: " . ($beneficiary['birthdate'] ?: 'N/A') . "\n";
                    $beneficiariesText .= "    Relationship: " . ($beneficiary['relationship'] ?: 'N/A') . "\n";
                }
            }
            if (empty($beneficiariesText)) {
                $beneficiariesText = "  None provided\n";
            }

            // Send Notification Email to Admin with all information
            try {
                Mail::raw(
                    "A new user registration has been submitted.\n\n" .
                    "=== APPLICANT INFORMATION ===\n" .
                    "Name: {$this->first_name} {$this->middle_name} {$this->surname}\n" .
                    "Gender: {$this->gender}\n" .
                    "Civil Status: {$this->civil_status}\n" .
                    "Birthdate: {$this->birthdate}\n" .
                    "Address: {$this->address}\n\n" .
                    "=== SECURITY & CONTACT ===\n" .
                    "Username: {$this->username}\n" .
                    "Mobile: {$this->mobile}\n" .
                    "Email: " . ($this->email ?: 'N/A') . "\n" .
                    "Referral Code: {$referralCode}\n\n" .
                    "=== BENEFICIARIES ===\n" .
                    $beneficiariesText,
                    function ($message) {
                        $message->to('anfredalbit110525@gmail.com')
                                ->subject('New User Registration: ' . $this->first_name . ' ' . $this->surname);

                        if (filled($this->email)) {
                            $message->replyTo($this->email, "{$this->first_name} {$this->surname}");
                        }
                    }
                );
            } catch (\Throwable $mailEx) {
                Log::error('Registration notification email failed: ' . $mailEx->getMessage());
            }

            Log::info('Application created', [
                'UserID' => $user->UserID,
                'referralCode' => $referralCode
            ]);

            $this->close();

            $this->dispatch(
                'toast',
                type: 'success',
                message: 'Application submitted successfully!'
            );

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Application failed', [
                'error' => $e->getMessage()
            ]);

            $this->dispatch(
                'toast',
                type: 'error',
                message: 'Something went wrong. Please try again.'
            );
        }
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
}
