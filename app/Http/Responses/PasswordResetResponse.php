<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\PasswordResetResponse as PasswordResetResponseContract;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;

class PasswordResetResponse implements PasswordResetResponseContract
{
    /**
     * The response status language key.
     */
    protected string $status;

    /**
     * Create a new response instance.
     */
    public function __construct(string $status)
    {
        $this->status = $status;
    }

    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse($request): JsonResponse|Response
    {
        if ($request->wantsJson()) {
            return new JsonResponse(['message' => trans($this->status)], 200);
        }

        return redirect(Fortify::redirects('password-reset', route('secret.login')))
            ->with('status', 'We have emailed a confirmation containing your new password. Sign in with it to continue.')
            ->with('password_reset_success', true);
    }
}
