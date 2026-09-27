<?php

namespace App\Http\Controllers;

use App\Models\Activation;
use App\Models\User;
use App\Traits\ChecksMemberPermissions;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    /**
     * The certificate artwork (certificate.jpg) already prints a CEO
     * signature line + name/title block on the LEFT and a second (COO)
     * block on the RIGHT. This resolves each signatory's account by
     * `position` and turns their stored signature file into a base64
     * data URI the PDF can overlay above the printed name.
     */
    private function signatories(): array
    {
        $resolve = function (string $position): ?array {
            $signatory = User::where('position', $position)
                ->whereNotNull('signature')
                ->where('signature', '!=', '')
                ->first();

            if (! $signatory) {
                return null;
            }

            $signature = null;

            if (Storage::disk('public')->exists($signatory->signature)) {
                $path = Storage::disk('public')->path($signatory->signature);
                $mime = mime_content_type($path);
                $signature = 'data:'.$mime.';base64,'.
                    base64_encode(file_get_contents($path));
            }

            return [
                'name' => strtoupper(trim(
                    $signatory->firstname.' '.
                    ($signatory->middlename ? substr(trim($signatory->middlename), 0, 1).'. ' : '').
                    $signatory->lastname
                )),
                'position' => $position,
                'signature' => $signature,
            ];
        };

        return [
            'ceo' => $resolve('CEO'),
            'coo' => $resolve('COO'),
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

            ...$this->signatories(),

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
            ...$this->signatories(),
        ];

        $pdf = Pdf::loadView('certificates.template', $data)
            ->setPaper('letter', 'landscape');

        return $pdf->download('Certificate-'.$user->lastname.'.pdf');
    }
}
