<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Activation;
use Illuminate\Mail\Mailable;

class InviteeApprovedMail extends Mailable
{
    public $referralUser;
    public $activatedUser;
    public $activation;

    public function __construct(
        User $referralUser,
        User $activatedUser,
        Activation $activation
    ) {
        $this->referralUser = $referralUser;
        $this->activatedUser = $activatedUser;
        $this->activation = $activation;
    }

    public function build()
    {
        return $this
            ->subject('Your invitee has been approved')
            ->view('emails.invitee-approved');
    }
}
