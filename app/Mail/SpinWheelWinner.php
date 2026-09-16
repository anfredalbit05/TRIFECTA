<?php

namespace App\Mail;

use App\Models\SpinDraw;
use App\Models\SpinWinner;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SpinWheelWinner extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public SpinDraw $draw,
        public SpinWinner $winner,
        public string $claimInstructions,
        public int $winNumber = 1,
        public array $company = [],
    ) {}

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
            ->subject('🎉 You won in the '.$this->draw->draw_date->format('F j, Y').' Spin the Wheel - DAYONGAN')
            ->view('emails.spin-wheel-winner');
    }
}
