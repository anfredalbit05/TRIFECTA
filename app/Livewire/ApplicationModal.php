<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class ApplicationModal extends Component
{
    public bool $open = false;

    public $surname = '';
    public $first_name = '';
    public $middle_name = '';
    public $birthdate = '';
    public $mobile = '';
    public $email = '';
    public $address = '';

    public $beneficiaries = [
        [
            'name' => '',
            'birthdate' => '',
        ],
        [
            'name' => '',
            'birthdate' => '',
        ],
        [
            'name' => '',
            'birthdate' => '',
        ],
    ];

    public bool $agree = false;

    #[On('open-application-modal')]
    public function open()
    {
        $this->open = true;
    }

    public function close()
    {
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.application-modal');
    }
}
