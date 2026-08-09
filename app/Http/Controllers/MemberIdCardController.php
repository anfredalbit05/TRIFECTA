<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class MemberIdCardController extends Controller
{
    public function download()
    {
        $user = User::with('beneficiaries')->findOrFail(Auth::id());

        // Manager signature
        $managerSignature = null;

        $pdf = Pdf::loadView(
            'livewire.member-pages.my-profile.id-card-pdf-component',
            [
                'user' => $user,
                'managerSignature' => $managerSignature,
            ]
        );

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download(
            'True-Life-Dayongan-ID-' .
            str_pad($user->id, 5, '0', STR_PAD_LEFT) .
            '.pdf'
        );
    }
}
