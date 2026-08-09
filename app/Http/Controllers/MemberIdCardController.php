<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

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
            !empty($user->profilepic) &&
            Storage::disk('public')->exists($user->profilepic)
        ) {
            $profilePicPath = Storage::disk('public')->path(
                $user->profilepic
            );

            $mimeType = mime_content_type($profilePicPath);

            $profilePicBase64 =
                'data:' . $mimeType . ';base64,' .
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
            !empty($user->signature) &&
            Storage::disk('public')->exists($user->signature)
        ) {
            $signaturePath = Storage::disk('public')->path(
                $user->signature
            );

            $mimeType = mime_content_type($signaturePath);

            $signatureBase64 =
                'data:' . $mimeType . ';base64,' .
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
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        $referralUrl = url(
            '/?ref=' . ($user->referral_code ?? '')
        );

        $qrCode = $writer->writeString($referralUrl);


        /*
        |--------------------------------------------------------------------------
        | MANAGER SIGNATURE
        |--------------------------------------------------------------------------
        */

        $managerSignature = null;


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
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | CR80 ID CARD SIZE
        |--------------------------------------------------------------------------
        |
        | 85.6mm × 54mm
        |
        | DomPDF uses points:
        | 1mm = 2.83464567pt
        |--------------------------------------------------------------------------
        */

        $width = 85.6 * 2.83464567;
        $height = 54 * 2.83464567;

        $pdf->setPaper([
            0,
            0,
            $width,
            $height
        ]);


        /*
        |--------------------------------------------------------------------------
        | DOWNLOAD
        |--------------------------------------------------------------------------
        */

        return $pdf->download(
            'True-Life-Dayongan-ID-' .
            str_pad($user->id, 5, '0', STR_PAD_LEFT) .
            '.pdf'
        );
    }
}

