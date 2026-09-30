<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendActivationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $email,
        public string $subject,
        public string $title,
        public string $message,
        public array $mailData = []
    ) {}

    public function handle(): void
    {
        $data = array_merge($this->mailData, [
            'emailTitle' => $this->title,
            'introMessage' => $this->message,
        ]);

        Mail::send(
            'emails.activation-request',
            $data,
            function ($mail) {
                $mail->to($this->email)
                    ->subject($this->subject);
            }
        );
    }
}
