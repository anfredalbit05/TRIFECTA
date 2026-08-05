<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Activation;

class ActivationApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Activation $activation;
    public float $totalAmount;

    public function __construct(User $user, Activation $activation, float $totalAmount)
    {
        $this->user = $user;
        $this->activation = $activation;
        $this->totalAmount = $totalAmount;
    }

    public function build()
    {
        return $this
            ->from(
                env('MAIL_FROM_ADDRESS'),
                env('MAIL_FROM_NAME')
            )
            ->subject('Acknowledgment Receipt - Activation Status')
            ->view('emails.activation-approved');
    }
}
