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

            'coverageType' => 'HMO, life and Accident Insurance',

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
    /**
     * Convert a signature image with a white background into a transparent PNG.
     */
    private function signatureToTransparentDataUri(string $path): ?string
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        $source = @imagecreatefromstring($contents);

        if ($source === false) {
            return null;
        }

        $width = imagesx($source);
        $height = imagesy($source);

        // Create transparent PNG canvas
        $output = imagecreatetruecolor($width, $height);

        imagealphablending($output, false);
        imagesavealpha($output, true);

        $transparent = imagecolorallocatealpha(
            $output,
            255,
            255,
            255,
            127
        );

        imagefill($output, 0, 0, $transparent);

        /*
        * Remove white / near-white background.
        *
        * Pure white becomes fully transparent.
        * Near-white pixels become partially transparent so
        * anti-aliased edges of the signature remain smooth.
        */
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {

                $rgba = imagecolorat($source, $x, $y);

                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;

                // Calculate brightness.
                $brightness = ($r + $g + $b) / 3;

                if ($brightness >= 245) {
                    // White / almost-white = transparent
                    $alpha = 127;
                } else {
                    /*
                    * Convert darker pixels into more opaque pixels.
                    * Black = opaque.
                    * Gray = partially transparent.
                    */
                    $alpha = (int) round(
                        (($brightness / 245) * 127)
                    );

                    $alpha = max(0, min(127, $alpha));
                }

                $color = imagecolorallocatealpha(
                    $output,
                    $r,
                    $g,
                    $b,
                    $alpha
                );

                imagesetpixel(
                    $output,
                    $x,
                    $y,
                    $color
                );
            }
        }

        imagedestroy($source);

        // Capture PNG directly into memory.
        ob_start();

        imagepng($output);

        $png = ob_get_clean();

        imagedestroy($output);

        if ($png === false || $png === '') {
            return null;
        }

        return 'data:image/png;base64,'.base64_encode($png);
    }

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
                $path = Storage::disk('public')->path(
                    $signatory->signature
                );

                $signature = $this->signatureToTransparentDataUri($path);
            }

            return [
                'name' => strtoupper(trim(
                    $signatory->firstname.' '.
                    ($signatory->middlename
                        ? substr(trim($signatory->middlename), 0, 1).'. '
                        : ''
                    ).
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
        /*
        | The member's own certificate is always downloadable by the
        | member themselves (the shared satellite View modal links
        | here); for everyone else the caller needs ModuleID 2 read
        | privilege.
        */
        abort_unless(
            (int) $userId === (int) Auth::id()
                || $this->callerHasReadPermission(Auth::user()),
            403
        );

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
