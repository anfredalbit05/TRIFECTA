<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\User;
use App\Traits\ChecksMemberPermissions;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

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

            'policy_effectivity_date' => $user->policy_effectivity_date,

            'policy_expiration_date' => $user->policy_expiration_date,

            'expiryDate' => now()->addYear(),

        ])
            ->setPaper('letter')
            ->download('Policy Agreement.pdf');
    }

    public function adminDownload(int $userId)
    {
        /*
        | The member's own record is always downloadable by the member
        | themselves (the shared satellite View modal links here); for
        | everyone else the caller needs ModuleID 2 read privilege.
        */
        abort_unless(
            (int) $userId === (int) Auth::id()
                || $this->callerHasReadPermission(Auth::user()),
            403
        );

        $user = User::findOrFail($userId);

        abort_unless($user->status === 'Active', 403);

        $beneficiaries = Beneficiary::where('UserID', $user->UserID)->get();

        return Pdf::loadView('policy.template', [
            'user' => $user,
            'beneficiaries' => $beneficiaries,
            'policyNumber' => $user->policy_number,
            'policy_expiration_date' => $user->policy_expiration_date,
            'policy_effectivity_date' => $user->policy_effectivity_date,
            'expiryDate' => now()->addYear(),
        ])
            ->setPaper('letter')
            ->download('Policy-Agreement-'.$user->lastname.'.pdf');
    }
}
