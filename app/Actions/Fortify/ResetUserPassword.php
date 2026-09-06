<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Mail\PasswordResetConfirmation;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  array<string, string>  $input
     */
    public function reset(User $user, array $input): void
    {
        Validator::make($input, [
            'password' => $this->passwordRules(),
        ])->validate();

        $user->forceFill([
            'password' => $input['password'],
        ])->save();

        // Send a branded confirmation email that includes the new password.
        // Failure to deliver must never block the reset itself, so any
        // mail error is logged and swallowed here.
        if (filled($user->email)) {
            try {
                Mail::to($user->email)->send(
                    new PasswordResetConfirmation($user, (string) $input['password'])
                );
            } catch (\Throwable $e) {
                Log::error('Failed to send password reset confirmation email.', [
                    'user_id' => $user->UserID,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
