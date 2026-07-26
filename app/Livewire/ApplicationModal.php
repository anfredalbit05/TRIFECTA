<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\User;

class ApplicationModal extends Component
{
    public bool $open = false;


    // Applicant Information
    public $surname = '';
    public $first_name = '';
    public $middle_name = '';
    public $gender = '';
    public $civil_status = '';
    public $birthdate = '';
    public $address = '';


    // Security Information
    public $mobile = '';
    public $email = '';
    public $username = '';
    public $password = '';
    public $password_confirmation = '';


    public bool $agree = false;

    #[On('open-application-modal')]

    public function open()
    {
        $this->open = true;
    }


    public function close()
    {
        $this->reset();
        $this->open = false;
    }



    public function submit()
    {
        $this->validate([

            // Applicant
            'surname' => 'required',
            'first_name' => 'required',
            'gender' => 'required',
            'civil_status' => 'required',
            'birthdate' => 'required',
            'address' => 'required',

            // Security
            'mobile' => 'required',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:8|confirmed',


        ]);



        User::create([

            // Account
            'username' => $this->username,
            'password' => $this->password,


            // Personal Information
            'lastname' => $this->surname,
            'firstname' => $this->first_name,
            'middlename' => $this->middle_name,

            'gender' => $this->gender,
            'civilstatus' => $this->civil_status,

            'birthdate' => $this->birthdate,

            'contactno' => $this->mobile,

            'email' => $this->email,

            'address' => $this->address,


            // Default values
            'RoleID' => 5,
            'status' => 'Inactive',

        ]);


        session()->flash(
            'success',
            'Application submitted successfully.'
        );


        $this->close();
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.application-modal');
    }
}
