<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Traits\ChecksMemberPermissions;

class PolicyController extends Controller
{
     use ChecksMemberPermissions;

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

            'policyNumber' => $user->policy_number,

            'policy_expiration_date' => $user->policy_expiration_date,

            'effectiveDate' => now(),

            'expiryDate' => now()->addYear(),

        ])
        ->setPaper('letter')
        ->download('Policy Agreement.pdf');
    }

    public function adminDownload(int $userId)
{
    abort_unless($this->callerHasReadPermission(Auth::user()), 403);

    $user = \App\Models\User::findOrFail($userId);

    abort_unless($user->status === 'Active', 403);

    $beneficiaries = Beneficiary::where('UserID', $user->UserID)->get();

    return Pdf::loadView('policy.template', [
        'user' => $user,
        'beneficiaries' => $beneficiaries,
        'policyNumber' => $user->policy_number,
        'policy_expiration_date' => $user->policy_expiration_date,
        'effectiveDate' => now(),
        'expiryDate' => now()->addYear(),
    ])
    ->setPaper('letter')
    ->download('Policy-Agreement-' . $user->lastname . '.pdf');
}
}
