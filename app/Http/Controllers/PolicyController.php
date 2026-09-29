<?php

namespace App\Http\Controllers;

use App\Models\Activation;
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

        $activation = Activation::where('UserID', $user->UserID)
            ->where('status', 'Approved')
            ->latest('ActivationID')
            ->firstOrFail();

        $beneficiaries = Beneficiary::where(
            'UserID',
            $user->UserID
        )->get();

        return Pdf::loadView('policy.template', [
            'user' => $user,

            'beneficiaries' => $beneficiaries,

            'policyNumber' => $activation->policy_number,

            'policy_effectivity_date' =>
                $activation->policy_effectivity_date,

            'policy_expiration_date' =>
                $activation->policy_expiration_date,

            'expiryDate' => now()->addYear(),
        ])
            ->setPaper('letter')
            ->download('Policy Agreement.pdf');
    }

    public function adminDownload(int $userId)
    {
        abort_unless(
            (int) $userId === (int) Auth::id()
                || $this->callerHasReadPermission(Auth::user()),
            403
        );

        $user = User::findOrFail($userId);

        abort_unless($user->status === 'Active', 403);

        $activation = Activation::where('UserID', $user->UserID)
            ->where('status', 'Approved')
            ->latest('ActivationID')
            ->firstOrFail();

        $beneficiaries = Beneficiary::where(
            'UserID',
            $user->UserID
        )->get();

        return Pdf::loadView('policy.template', [
            'user' => $user,

            'beneficiaries' => $beneficiaries,

            'policyNumber' => $activation->policy_number,

            'policy_expiration_date' =>
                $activation->policy_expiration_date,

            'policy_effectivity_date' =>
                $activation->policy_effectivity_date,

            'expiryDate' => now()->addYear(),
        ])
            ->setPaper('letter')
            ->download(
                'Policy-Agreement-' . $user->lastname . '.pdf'
            );
    }
}

