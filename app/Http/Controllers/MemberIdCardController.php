<?php

namespace App\Http\Controllers;

use App\Models\Activation;
use App\Models\User;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MemberIdCardController extends Controller
{
    public function download()
    {
        $user = User::with('beneficiaries')
            ->findOrFail(Auth::id());

        /*
        |--------------------------------------------------------------------------
        | IMAGE TO BASE64 HELPER
        |--------------------------------------------------------------------------
        */

        $toBase64 = function ($path) {
            if (!$path || !is_file($path)) {
                return null;
            }

            $mimeType = mime_content_type($path);

            return 'data:' . $mimeType . ';base64,' .
                base64_encode(file_get_contents($path));
        };

        /*
        |--------------------------------------------------------------------------
        | FRONT AND BACK ID TEMPLATES
        |--------------------------------------------------------------------------
        */

        $frontTemplateBase64 = $toBase64(
            public_path('images/certificate/trifecta-id.jpg')
        );

        /*
        |--------------------------------------------------------------------------
        | MEMBER PROFILE PICTURE
        |--------------------------------------------------------------------------
        */

        $profilePicBase64 = null;

        if (
            !empty($user->profilepic) &&
            Storage::disk('public')->exists($user->profilepic)
        ) {
            $profilePicBase64 = $toBase64(
                Storage::disk('public')->path($user->profilepic)
            );
        }

        /*
        |--------------------------------------------------------------------------
        | MEMBER SIGNATURE
        |--------------------------------------------------------------------------
        */

        $signatureBase64 = null;

        if (
            !empty($user->signature) &&
            Storage::disk('public')->exists($user->signature)
        ) {
            $signatureBase64 = $toBase64(
                Storage::disk('public')->path($user->signature)
            );
        }

        /*
        |--------------------------------------------------------------------------
        | MANAGER / CEO SIGNATURE
        |--------------------------------------------------------------------------
        */

        $managerSignature = null;

        $ceo = User::where('position', 'CEO')->first();

        if (
            $ceo &&
            !empty($ceo->signature) &&
            Storage::disk('public')->exists($ceo->signature)
        ) {
            $managerSignature = $toBase64(
                Storage::disk('public')->path($ceo->signature)
            );
        }

        /*
        |--------------------------------------------------------------------------
        | QR CODE
        |--------------------------------------------------------------------------
        */

        // Same referral / join link the Account page shows and copies.
        $referralUrl = rtrim(config('app.url'), '/')
            . '/?ref=' . urlencode($user->referralCode ?? '');

        /*
        |------------------------------------------------------------------
        | The QR must be a raster image: dompdf ignores inline SVG, which
        | left the printed ID without a scannable code.
        |------------------------------------------------------------------
        */

        $qrCodePng = $this->qrCodePng($referralUrl);

        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP INFORMATION
        |--------------------------------------------------------------------------
        */

        $approvedActivation = Activation::where(
            'UserID',
            $user->UserID
        )
            ->where('status', 'Approved')
            ->orderByDesc('ActivationID')
            ->first();

        // Same value the live ID preview prints: the approved activation's
        // policy number, falling back to the generated membership number.
        $membershipNo = $approvedActivation?->policy_number
            ?: 'TL' . now()->year . str_pad(
                (string) $user->UserID,
                3,
                '0',
                STR_PAD_LEFT
            );

        $dateOfIssue = 'N/A';

        if ($approvedActivation) {
            if ($approvedActivation->policy_effectivity_date) {
                $dateOfIssue = Carbon::parse(
                    $approvedActivation->policy_effectivity_date
                )->format('F d, Y');
            } elseif ($approvedActivation->updated_at) {
                $dateOfIssue = Carbon::parse(
                    $approvedActivation->updated_at
                )->format('F d, Y');
            }
        }

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
                'beneficiaries' => $user->beneficiaries,

                // ID templates
                'frontTemplateBase64' => $frontTemplateBase64,

                // QR code (PNG data URI — encoded referral / join link)
                'qrCodePng' => $qrCodePng,

                // Member images
                'profilePicBase64' => $profilePicBase64,
                'signatureBase64' => $signatureBase64,

                // Manager signature
                'managerSignature' => $managerSignature,

                // Membership information
                'membershipNo' => $membershipNo,
                'dateOfIssue' => $dateOfIssue,
                'validUntil' => $validUntil,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | HALF-LETTER LANDSCAPE PAPER
        |--------------------------------------------------------------------------
        |
        | Paper size: 8.5 × 5.5 inches
        | Width: 215.9 mm
        | Height: 139.7 mm
        |
        | Each ID card uses the 583:370 template ratio.
        | Target card dimensions: 85.6 × 54.3 mm.
        |
        */

        $width = 215.9 * 2.83464567;
        $height = 139.7 * 2.83464567;

        $pdf->setPaper([
            0,
            0,
            $width,
            $height,
        ]);

        /*
        |--------------------------------------------------------------------------
        | DOWNLOAD PDF
        |--------------------------------------------------------------------------
        */

        return $pdf->download(
            'TRIFECTA-ID-' .
            str_pad(
                (string) $user->UserID,
                5,
                '0',
                STR_PAD_LEFT
            ) .
            '.pdf'
        );
    }

    /**
     * Render the referral link as a PNG data URI so dompdf can print it.
     */
    private function qrCodePng(string $content, int $scale = 8, int $margin = 2): string
    {
        $matrix = Encoder::encode(
            $content,
            ErrorCorrectionLevel::M()
        )->getMatrix();

        $modules = $matrix->getWidth();
        $size = ($modules + ($margin * 2)) * $scale;

        $image = imagecreatetruecolor($size, $size);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 12, 44, 107);

        imagefilledrectangle($image, 0, 0, $size, $size, $white);

        for ($y = 0; $y < $modules; $y++) {
            for ($x = 0; $x < $modules; $x++) {
                if ($matrix->get($x, $y) !== 1) {
                    continue;
                }

                $left = ($x + $margin) * $scale;
                $top = ($y + $margin) * $scale;

                imagefilledrectangle(
                    $image,
                    $left,
                    $top,
                    $left + $scale - 1,
                    $top + $scale - 1,
                    $black
                );
            }
        }

        ob_start();
        imagepng($image);
        $png = (string) ob_get_clean();

        return 'data:image/png;base64,' . base64_encode($png);
    }
}
