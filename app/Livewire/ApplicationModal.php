<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\User;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
    | Submit Application
    |--------------------------------------------------------------------------
    */

    public function submit()
    {


        try {


            $this->validate([


                /*
                | Applicant
                */

                'surname' => 'required|string|max:255',

                'first_name' => 'required|string|max:255',

                'gender' => 'required',

                'civil_status' => 'required',

                'birthdate' => 'required|date',

                'address' => 'required|string|max:500',



                /*
                | Beneficiaries
                */

                'beneficiaries.*.fullname'
                    => 'nullable|string|max:150',


                'beneficiaries.*.birthdate'
                    => 'nullable|date',


                'beneficiaries.*.relationship'
                    => 'nullable|string|max:50',




                /*
                | Security
                */

                'mobile'
                    => 'required|string|max:20',


                'email'
                    => 'nullable|email|unique:users,email',


                'username'
                    => 'required|string|max:100|unique:users,username',


                'password'
                    => 'required|min:8|confirmed',



                /*
                | Terms
                */

                'agree'
                    => 'accepted',


            ]);



        } catch(ValidationException $e) {


            $errors = $e->validator
                ->errors()
                ->all();



            $html = '<ul style="text-align:left;">';



            foreach($errors as $error)
            {

                $html .= "
                    <li style='margin-bottom:8px'>
                        {$error}
                    </li>
                ";

            }


            $html .= '</ul>';



            $this->dispatch(
                'validation-error',
                title:'Please complete the form',
                html:$html
            );


            return;

        }

        DB::beginTransaction();


        try {



            /*
            |--------------------------------------------------------------------------
            | Generate Upline Code
            |--------------------------------------------------------------------------
            */


            do {


                $uplineCode =
                    $this->generateUplineCode();


                $exists =
                    User::where(
                        'uplineCode',
                        $uplineCode
                    )->exists();



            } while($exists);





            /*
            |--------------------------------------------------------------------------
            | Create User
            |--------------------------------------------------------------------------
            */


            $user = User::create([


                'username'
                    => $this->username,


                'password'
                    => $this->password,


                'uplineCode'
                    => $uplineCode,



                'lastname'
                    => $this->surname,


                'firstname'
                    => $this->first_name,


                'middlename'
                    => $this->middle_name,



                'gender'
                    => $this->gender,


                'civilstatus'
                    => $this->civil_status,


                'birthdate'
                    => $this->birthdate,


                'contactno'
                    => $this->mobile,


                'email'
                    => filled($this->email)
                        ? $this->email
                        : null,


                'address'
                    => $this->address,



                'RoleID'
                    => 5,


                'status'
                    => 'Inactive',


            ]);


            /*
            |--------------------------------------------------------------------------
            | Save Beneficiaries
            |--------------------------------------------------------------------------
            */


            foreach($this->beneficiaries as $beneficiary)
            {



                if(
                    filled($beneficiary['fullname'])
                    &&
                    filled($beneficiary['birthdate'])
                )
                {


                    Beneficiary::create([


                        /*
                        | Change this according
                        | to your benefits table
                        */

                        'BenefitID'
                            =>1,


                        'UserID'
                            => $user->UserID,


                        'fullname'
                            => $beneficiary['fullname'],


                        'birthdate'
                            => $beneficiary['birthdate'],


                        'relationship'
                            => $beneficiary['relationship'],



                        'status'
                            => true,


                    ]);

                }


            }





            DB::commit();





            Log::info(
                'Application created',
                [
                    'UserID'=>$user->UserID,
                    'uplineCode'=>$uplineCode
                ]
            );






            $this->close();




            $this->dispatch(
                'toast',
                type:'success',
                message:
                'Application submitted successfully!'
            );






        } catch(\Throwable $e) {



            DB::rollBack();



            Log::error(
                'Application failed',
                [
                    'error'=>$e->getMessage()
                ]
            );



            $this->dispatch(
                'toast',
                type:'error',
                message:
                'Something went wrong. Please try again.'
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

        return view(
            'livewire.application-modal'
        );

    }

}
