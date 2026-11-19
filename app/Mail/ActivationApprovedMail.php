<?php

namespace App\Mail;

use App\Models\Activation;
use App\Models\User;
use App\Services\AcknowledgementReceiptService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ActivationApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public Activation $activation;

    public float $totalAmount;

    /**
     * Path to the acknowledgement receipt image attached to this email.
     */
    public const RECEIPT_PATH = 'images/ack-receipt.jpg';

    public function __construct(User $user, Activation $activation, float $totalAmount)
    {
        $this->user = $user;
        $this->activation = $activation;
        $this->totalAmount = $totalAmount;
    }

    public function build()
    {
        $email = $this
            ->from(
                env('MAIL_FROM_ADDRESS'),
                env('MAIL_FROM_NAME')
            )
            ->subject('Acknowledgment Receipt - Activation Status')
            ->view('emails.activation-approved');

        $this->attachAcknowledgementReceipt($email);

        return $email;
    }

    /**
     * Attach the acknowledgement receipt filled with THIS member's real data
     * (policy number, date, amount, name, email, address). Falls back to the
     * blank scanned form (public/images/ack-receipt.jpg) when generation is
     * not possible. Never breaks the email either way.
     */
    private function attachAcknowledgementReceipt(Mailable $email): void
    {
        try {
            // The receipt number and cashier identity issued on THIS approval
            // (snapshot lives on the activation, cashier values fall back to settings)
            $receiptNumber = $this->activation->receipt_number ?? null;

            $generatedPath = app(AcknowledgementReceiptService::class)->generate(
                $this->user,
                $this->totalAmount,
                $receiptNumber,
                $this->activation->cashier_name ?? null,
                $this->activation->cashier_signature ?? null,
                $this->activation->created_at?->format('M j, Y'),
                $this->activation
            );

            if ($generatedPath !== null && is_file($generatedPath)) {
                $email->attach($generatedPath, [
                    'as' => 'Acknowledgement-Receipt.jpg',
                    'mime' => 'image/jpeg',
                ]);

                return;
            }

            // Fallback: attach the blank template so the member still receives the form
            $blankPath = public_path(self::RECEIPT_PATH);

            if (is_file($blankPath) && is_readable($blankPath)) {
                $email->attach($blankPath, [
                    'as' => 'Acknowledgement-Receipt.jpg',
                    'mime' => 'image/jpeg',
                ]);
            } else {
                Log::warning('Acknowledgement receipt image not found; email sent without attachment.', [
                    'expected_path' => $blankPath,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to attach acknowledgement receipt to ActivationApprovedMail.', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
