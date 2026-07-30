<?php

namespace App\Mail;

use App\Models\Activation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActivationRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Activation $activation;
    public array $beneficiaries;
    public string $remarks;

    public function __construct(
        User $user,
        Activation $activation,
        array $beneficiaries,
        string $remarks
    ) {
        $this->user = $user;
        $this->activation = $activation;
        $this->beneficiaries = $beneficiaries;
        $this->remarks = $remarks;
    }

    public function build()
    {
        return $this
            ->from(
                env('MAIL_FROM_ADDRESS'),
                env('MAIL_FROM_NAME')
            )
            ->subject('Activation Request Rejected')
            ->view('emails.activation-rejected');
    }
}
