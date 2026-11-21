<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\MemberIdCardImageService;
use App\Traits\ChecksMemberPermissions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MemberIdCardController extends Controller
{
    use ChecksMemberPermissions;

    /**
     * Download the signed-in member's ID card as an image (JPG).
     */
    public function download(): StreamedResponse
    {
        abort_unless(Auth::check(), 403);

        return $this->streamIdImage(User::findOrFail(Auth::id()));
    }

    /**
     * Download any member's ID card as an image (admin / staff view).
     */
    public function adminDownload(int $userId): StreamedResponse
    {
        abort_unless(
            (int) $userId === (int) Auth::id()
                || $this->callerHasReadPermission(Auth::user()),
            403
        );

        return $this->streamIdImage(User::findOrFail($userId));
    }

    /**
     * Render the ID card JPEG and stream it to the browser.
     */
    private function streamIdImage(User $user): StreamedResponse
    {
        $path = app(MemberIdCardImageService::class)->generate($user);

        if ($path === null || ! is_file($path)) {
            abort(500, 'Could not generate the ID card image.');
        }

        Log::info('Downloaded ID card image.', [
            'member_UserID' => $user->UserID,
            'downloaded_by' => Auth::id(),
        ]);

        $filename = 'TRIFECTA-ID-'.str_pad(
            (string) $user->UserID,
            5,
            '0',
            STR_PAD_LEFT
        ).'.jpg';

        return response()->streamDownload(function () use ($path) {
            $stream = fopen($path, 'rb');

            if ($stream !== false) {
                fpassthru($stream);
                fclose($stream);
            }
        }, $filename, [
            'Content-Type' => 'image/jpeg',
        ]);
    }
}
