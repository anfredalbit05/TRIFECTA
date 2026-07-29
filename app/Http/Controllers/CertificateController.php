<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function download(Request $request)
    {
        $user = Auth::user();

        abort_unless($user->status === 'Active', 403);

        $certificateNumber =
            'CERT-' .
            now()->format('Y') .
            '-' .
            str_pad($user->id, 5, '0', STR_PAD_LEFT);

        $data = [

            'user' => $user,

            'fullname' => strtoupper(trim(
                $user->firstname . ' ' .
                $user->middlename . ' ' .
                $user->lastname
            )),

            'certificateNumber' => $certificateNumber,

            'issuedDate' => now()->format('F d, Y'),

        ];

        $pdf = Pdf::loadView('certificates.template', $data)
            ->setPaper('letter', 'landscape');

        return $pdf->download(
            'Certificate-'.$user->lastname.'.pdf'
        );
    }
}
