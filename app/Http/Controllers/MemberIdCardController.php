<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
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

        // Generate QR Code
        $renderer = new ImageRenderer(
            new RendererStyle(80),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        $referralUrl = url('/?ref=' . ($user->referral_code ?? ''));

        $qrCode = $writer->writeString($referralUrl);

        // Manager signature
        $managerSignature = null;

        $pdf = Pdf::loadView(
            'livewire.member-pages.my-profile.id-card-pdf-component',
            [
                'user' => $user,
                'beneficiaries' => $beneficiaries,
                'qrCode' => $qrCode,
                'managerSignature' => $managerSignature,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | CR80 ID Card Size
        |--------------------------------------------------------------------------
        | 85.6mm × 54mm
        |
        | DomPDF uses points:
        | 1mm = 2.83464567pt
        |
        | 85.6mm = 242.645669pt
        | 54mm   = 153.070866pt
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

        return $pdf->download(
            'True-Life-Dayongan-ID-' .
            str_pad($user->id, 5, '0', STR_PAD_LEFT) .
            '.pdf'
        );
    }
}