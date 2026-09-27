<?php

namespace App\Http\Controllers;

use App\Models\Activation;
use App\Models\User;
use App\Traits\ChecksMemberPermissions;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    use ChecksMemberPermissions;

    /**
     * Policy details printed on the certificate come from the member's
     * latest approved activation: the policy number + cocogen policy
     * number live there, and the issue/valid dates mirror the policy
     * effectivity/expiration dates recorded on the same row.
     */
    private function certificateFields(int $userId): array
    {
        $activation = Activation::where('UserID', $userId)
            ->where('status', 'Approved')
            ->latest('ActivationID')
            ->first();

        return [
            'policyNumber' => $activation?->policy_number
                ?: ($activation?->cocogen_policy_number ?: 'N/A'),

            'dateOfIssue' => $activation?->policy_effectivity_date
                ? Carbon::parse($activation->policy_effectivity_date)->format('F d, Y')
                : 'N/A',

            'coverageType' => 'Primary Policy Holder',

            'validUntil' => $activation?->policy_expiration_date
                ? Carbon::parse($activation->policy_expiration_date)->format('F d, Y')
                : 'N/A',
        ];
    }

    public function download(Request $request)
    {
        $user = Auth::user();

        abort_unless($user->status === 'Active', 403);
        $certificateNumber =
        'CERT-'.
        now()->format('Y').
        '-'.
        str_pad((string) $user->UserID, 5, '0', STR_PAD_LEFT);

        $data = [

            'user' => $user,

            'fullname' => strtoupper(trim(
                $user->firstname.' '.
                $user->middlename.' '.
                $user->lastname
            )),

            'certificateNumber' => $certificateNumber,

            'issuedDate' => now()->format('F d, Y'),

            ...$this->certificateFields($user->UserID),

        ];

        $pdf = Pdf::loadView('certificates.template', $data)
            ->setPaper('letter', 'landscape');

        return $pdf->download(
            'Certificate-'.$user->lastname.'.pdf'
        );
    }

    public function adminDownload(Request $request, int $userId)
    {

        abort_unless($this->callerHasReadPermission(Auth::user()), 403);

        $user = User::findOrFail($userId);

        abort_unless($user->status === 'Active', 403);
        $certificateNumber =
        'CERT-'.
        now()->format('Y').
        '-'.
        str_pad((string) $user->UserID, 5, '0', STR_PAD_LEFT);

        $data = [
            'user' => $user,
            'fullname' => strtoupper(trim(
                $user->firstname.' '.
                $user->middlename.' '.
                $user->lastname
            )),
            'certificateNumber' => $certificateNumber,
            'issuedDate' => now()->format('F d, Y'),
            ...$this->certificateFields($userId),
        ];

        $pdf = Pdf::loadView('certificates.template', $data)
            ->setPaper('letter', 'landscape');

        return $pdf->download('Certificate-'.$user->lastname.'.pdf');
    }
}
