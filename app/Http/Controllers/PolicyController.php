<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PolicyController extends Controller
{
    public function download()
    {
        $user = Auth::user();

        abort_unless($user->status === 'Active', 403);

        $beneficiaries = Beneficiary::where(
            'UserID',
            $user->UserID
        )->get();

        return Pdf::loadView('policy.template', [

            'user' => $user,

            'beneficiaries' => $beneficiaries,

            'policyNumber' => 'POL-'.date('Y').'-'.str_pad(
                $user->UserID,
                5,
                '0',
                STR_PAD_LEFT
            ),

            'effectiveDate' => now(),

            'expiryDate' => now()->addYear(),

        ])
        ->setPaper('letter')
        ->download('Policy Agreement.pdf');
    }
}
