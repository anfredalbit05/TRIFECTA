<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public string $newPassword;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $newPassword)
    {
        $this->user = $user;
        $this->newPassword = $newPassword;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this
            ->from(
                env('MAIL_FROM_ADDRESS'),
                env('MAIL_FROM_NAME')
            )
            ->subject('Your Password Has Been Reset - TRIFECTA')
            ->view('emails.password-reset-confirmation');
    }
}
