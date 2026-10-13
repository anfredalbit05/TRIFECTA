<?php

namespace App\Http\Controllers;

use App\Models\Activation;
use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MemberIdCardController extends Controller
{
    public function download()
    {
        $user = User::with('beneficiaries')->findOrFail(Auth::id());

        $beneficiaries = $user->beneficiaries;

        /*
        |--------------------------------------------------------------------------
        | PROFILE PICTURE
        |--------------------------------------------------------------------------
        |
        | The database should contain something like:
        |
        | profilepics/example.jpg
        |
        | The actual file is located at:
        |
        | storage/app/public/profilepics/example.jpg
        |
        */

        $profilePicBase64 = null;

        if (
            ! empty($user->profilepic) &&
            Storage::disk('public')->exists($user->profilepic)
        ) {
            $profilePicPath = Storage::disk('public')->path(
                $user->profilepic
            );

            $mimeType = mime_content_type($profilePicPath);

            $profilePicBase64 =
                'data:'.$mimeType.';base64,'.
                base64_encode(
                    file_get_contents($profilePicPath)
                );
        }

        /*
        |--------------------------------------------------------------------------
        | MEMBER SIGNATURE
        |--------------------------------------------------------------------------
        |
        | The database should contain something like:
        |
        | signatures/example.png
        |
        */

        $signatureBase64 = null;

        if (
            ! empty($user->signature) &&
            Storage::disk('public')->exists($user->signature)
        ) {
            $signaturePath = Storage::disk('public')->path(
                $user->signature
            );

            $mimeType = mime_content_type($signaturePath);

            $signatureBase64 =
                'data:'.$mimeType.';base64,'.
                base64_encode(
                    file_get_contents($signaturePath)
                );
        }

        /*
        |--------------------------------------------------------------------------
        | GENERATE QR CODE
        |--------------------------------------------------------------------------
        */

        $renderer = new ImageRenderer(
            new RendererStyle(80),
            new SvgImageBackEnd
        );

        $writer = new Writer($renderer);

        $referralUrl = url(
            '/?ref='.($user->referral_code ?? '')
        );

        $qrCode = $writer->writeString($referralUrl);

        /*
        |--------------------------------------------------------------------------
        | MANAGER SIGNATURE (CEO)
        |--------------------------------------------------------------------------
        |
        | Look up the user whose position is "CEO" and grab their signature.
        | If no CEO is found, or the CEO has no signature file, this stays null.
        |
        */

        $managerSignature = null;

        $ceo = User::where('position', 'CEO')->first();

        if (
            $ceo &&
            ! empty($ceo->signature) &&
            Storage::disk('public')->exists($ceo->signature)
        ) {
            $ceoSignaturePath = Storage::disk('public')->path($ceo->signature);

            $mimeType = mime_content_type($ceoSignaturePath);

            $managerSignature =
                'data:'.$mimeType.';base64,'.
                base64_encode(
                    file_get_contents($ceoSignaturePath)
                );
        }

        /*
        |--------------------------------------------------------------------------
        | ID DATA — membership no, date of issue, valid until
        |--------------------------------------------------------------------------
        |
        | Membership no: TL + current year + zero-padded UserID (TL2026001).
        | Date of issue: policy effectivity date, falling back to the
        | approved activation's approval date. Valid until: policy
        | expiration date.
        |
        */


/*
|--------------------------------------------------------------------------
| ID DATA — membership no, date of issue, valid until
|--------------------------------------------------------------------------
*/

$membershipNo = 'TL'
    . now()->year
    . str_pad(
        (string) $user->UserID,
        3,
        '0',
        STR_PAD_LEFT
    );

// Get the latest approved activation for this member.
$approvedActivation = Activation::where('UserID', $user->UserID)
    ->where('status', 'Approved')
    ->orderByDesc('ActivationID')
    ->first();

// Date of issue comes from the activation's policy effectivity date.
$dateOfIssue = $approvedActivation?->policy_effectivity_date
    ? Carbon::parse(
        $approvedActivation->policy_effectivity_date
    )->format('F d, Y')
    : (
        $approvedActivation?->updated_at
            ? Carbon::parse(
                $approvedActivation->updated_at
            )->format('F d, Y')
            : 'N/A'
    );

// Valid until comes from the activation's policy expiration date.
$validUntil = $approvedActivation?->policy_expiration_date
    ? Carbon::parse(
        $approvedActivation->policy_expiration_date
    )->format('F d, Y')
    : 'N/A';


        /*
        |--------------------------------------------------------------------------
        | GENERATE PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'livewire.member-pages.my-profile.id-card-pdf-component',
            [
                'user' => $user,
                'beneficiaries' => $beneficiaries,
                'qrCode' => $qrCode,

                // Member images
                'profilePicBase64' => $profilePicBase64,
                'signatureBase64' => $signatureBase64,

                // Manager
                'managerSignature' => $managerSignature,

                // ID data
                'membershipNo' => $membershipNo,
                'dateOfIssue' => $dateOfIssue,
                'validUntil' => $validUntil,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | HALF LETTER PAPER (PORTRAIT), TWO CARDS
        |--------------------------------------------------------------------------
        |
        | One page of HALF letter (short band) paper in PORTRAIT:
        | 5.5in wide × 8.5in high (139.7mm × 215.9mm). The FRONT card
        | sits on the left and the BACK card on the right, each exactly
        | 55.5mm × 75mm — matching the CSS layout in id-card.css.
        |
        | DomPDF uses points:
        | 1mm = 2.83464567pt
        |--------------------------------------------------------------------------
        */

        $width = 139.7 * 2.83464567;
        $height = 215.9 * 2.83464567;

        $pdf->setPaper([
            0,
            0,
            $width,
            $height,
        ], 'portrait');

        /*
        |--------------------------------------------------------------------------
        | DOWNLOAD
        |--------------------------------------------------------------------------
        */

        return $pdf->download(
            'True-Life-Dayongan-ID-'.
            str_pad((string) $user->UserID, 5, '0', STR_PAD_LEFT).
            '.pdf'
        );
    }
}
