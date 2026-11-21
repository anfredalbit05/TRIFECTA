<?php

namespace App\Services;

use App\Models\Activation;
use App\Models\User;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Renders the member's TRIFECTA ID card front (public/images/certificate/trifecta-id.jpg,
 * 1200 x 896 px) as a JPEG, so the ID downloads as an image instead of a PDF.
 *
 * The overlays are the same ones the ID card PDF (and the live preview) print, with
 * the blade's millimetre positions converted onto the artwork: 85.6mm wide card over
 * a 1200px image = 14.0187 px per mm.
 *
 *   Member photo ...... 68.05mm, 16.93mm  (12.41 x 17.25mm) -> 954, 237  (174 x 242)
 *   Name .............. 31.59mm, 20mm     -> x 443, top 280
 *   Membership no. .... 25.08mm, 28.1mm   -> x 352, top 394
 *   Date of issue ..... 31.07mm, 36.2mm   -> x 436, top 508
 *   Valid until ....... 27.56mm, 44.1mm   -> x 386, top 618
 *   Referral QR ....... 69.34mm, 45mm     (12 x 12mm)        -> 972, 631  (168 x 168)
 */
class MemberIdCardImageService
{
    /** ID card front artwork inside the public directory. */
    private const TEMPLATE = 'images/certificate/trifecta-id.jpg';

    private const FONT_BOLD = __DIR__.'/../../vendor/dompdf/dompdf/lib/fonts/DejaVuSans-Bold.ttf';

    /** Printed type colour on the card. */
    private const NAVY = [0x0C, 0x2C, 0x6B];

    /** 8pt on the card, in artwork pixels (1mm = 2.8346pt). */
    private const EM = 39.6;

    /** DejaVu Sans ascent (1901 / 2048 em). */
    private const ASCENT = 0.928;

    /**
     * Render the ID card front and return the JPEG path.
     */
    public function generate(User $user): ?string
    {
        $templatePath = public_path(self::TEMPLATE);

        if (! is_file($templatePath)) {
            Log::warning('ID card template missing; cannot render ID image.', [
                'expected_path' => $templatePath,
            ]);

            return null;
        }

        if (! function_exists('imagecreatefromjpeg')) {
            Log::warning('GD extension unavailable; cannot render ID image.');

            return null;
        }

        try {
            $image = imagecreatefromjpeg($templatePath);

            if ($image === false) {
                Log::error('Could not decode ID card template.', ['path' => $templatePath]);

                return null;
            }

            $ink = imagecolorallocate($image, ...self::NAVY);

            $approvedActivation = Activation::where('UserID', $user->UserID)
                ->where('status', 'Approved')
                ->orderByDesc('ActivationID')
                ->first();

            $this->pastePhoto($image, $user, 954, 237, 174, 242);

            $this->text(
                $image,
                mb_strtoupper(trim(
                    $user->firstname.' '
                    .($user->middlename ? mb_substr(trim($user->middlename), 0, 1).'. ' : '')
                    .$user->lastname
                )),
                443,
                $this->baseline(290),
                29,
                $ink,
                511
            );

            $this->text($image, $this->membershipNumber($user, $approvedActivation), 352, $this->baseline(405), 29, $ink, 540);

            $this->text($image, $this->dateOfIssue($approvedActivation), 436, $this->baseline(517), 29, $ink, 497);

            $this->text($image, $this->validUntil($approvedActivation), 386, $this->baseline(629), 29, $ink, 540);

            $this->pasteQr(
                $image,
                rtrim(config('app.url'), '/').'/?ref='.urlencode($user->referralCode ?? ''),
                925, // X position
                600, // Y position
                220, // Width
                220  // Height
            );

            $directory = storage_path('app/id-cards');

            if (! is_dir($directory)) {
                mkdir($directory, 0775, true);
            }

            $outputPath = $directory.'/id-'.uniqid('card-', true).'.jpg';

            imagejpeg($image, $outputPath, 92);

            $this->cleanupOldCards($directory);

            return is_file($outputPath) ? $outputPath : null;
        } catch (\Throwable $e) {
            Log::error('Failed to render ID card image.', [
                'error' => $e->getMessage(),
                'UserID' => $user->UserID ?? null,
            ]);

            return null;
        }
    }

    /** Baseline for a text box whose top is $top (8pt, line-height 1.2). */
    private function baseline(int $top): int
    {
        $halfLeading = (1.2 - 1.164) * self::EM / 2;

        return (int) round($top + $halfLeading + self::EM * self::ASCENT);
    }

    /**
     * Paste the member's profile picture, scaled to cover the photo box and
     * cropped to its centre like the printed card.
     */
    private function pastePhoto($image, User $user, int $x, int $y, int $width, int $height): void
    {
        $profilePic = $user->profilepic;

        if (blank($profilePic) || ! Storage::disk('public')->exists($profilePic)) {
            return;
        }

        $binary = @file_get_contents(Storage::disk('public')->path($profilePic));

        if ($binary === false) {
            return;
        }

        $photo = @imagecreatefromstring($binary);

        if ($photo === false) {
            return;
        }

        $sourceWidth = imagesx($photo);
        $sourceHeight = imagesy($photo);

        $scale = max($width / $sourceWidth, $height / $sourceHeight);

        $cropWidth = (int) round($width / $scale);
        $cropHeight = (int) round($height / $scale);
        $sourceX = (int) max(0, round(($sourceWidth - $cropWidth) / 2));
        $sourceY = (int) max(0, round(($sourceHeight - $cropHeight) / 2));

        imagecopyresampled(
            $image,
            $photo,
            $x,
            $y,
            $sourceX,
            $sourceY,
            $width,
            $height,
            $cropWidth,
            $cropHeight
        );

        imagedestroy($photo);
    }

    /** Paste a sharp referral QR code using integer-sized modules. */
    private function pasteQr(
        $image,
        string $content,
        int $x,
        int $y,
        int $width,
        int $height
    ): void {
        $matrix = Encoder::encode(
            $content,
            ErrorCorrectionLevel::M()
        )->getMatrix();

        $modules = $matrix->getWidth();
        $margin = 2;
        $total = $modules + ($margin * 2);

        // Keep QR square and preserve sharp module edges.
        $targetSize = min($width, $height);
        $scale = max(1, (int) floor($targetSize / $total));

        $drawnSize = $total * $scale;

        $qr = imagecreatetruecolor($drawnSize, $drawnSize);

        if ($qr === false) {
            throw new \RuntimeException('Could not create QR image.');
        }

        $white = imagecolorallocate($qr, 255, 255, 255);
        $black = imagecolorallocate($qr, ...self::NAVY);

        imagefilledrectangle(
            $qr,
            0,
            0,
            $drawnSize - 1,
            $drawnSize - 1,
            $white
        );

        for ($row = 0; $row < $modules; $row++) {
            for ($column = 0; $column < $modules; $column++) {

                if ($matrix->get($column, $row) !== 1) {
                    continue;
                }

                $left = ($column + $margin) * $scale;
                $top = ($row + $margin) * $scale;

                imagefilledrectangle(
                    $qr,
                    $left,
                    $top,
                    $left + $scale - 1,
                    $top + $scale - 1,
                    $black
                );
            }
        }

        // Center QR inside the requested dimensions.
        $destinationX = $x + (int) floor(($width - $drawnSize) / 2);
        $destinationY = $y + (int) floor(($height - $drawnSize) / 2);

        // Copy without resampling to preserve scan quality.
        imagecopy(
            $image,
            $qr,
            $destinationX,
            $destinationY,
            0,
            0,
            $drawnSize,
            $drawnSize
        );

        imagedestroy($qr);
    }

    private function text(
        $image,
        string $value,
        int $x,
        int $baseline,
        int $size,
        $ink,
        ?int $maxWidth = null
    ): void {
        $value = trim(str_replace(["\r\n", "\r", "\n"], ' ', $value));

        if ($value === '') {
            return;
        }

        if ($maxWidth !== null) {
            while ($size > 6 && $this->textWidth($size, $value) > $maxWidth) {
                $size--;
            }
        }

        imagettftext($image, $size, 0, $x, $baseline, $ink, self::FONT_BOLD, $value);
    }

    private function textWidth(int $size, string $value): float
    {
        $box = imagettfbbox($size, 0, self::FONT_BOLD, $value);

        return $box === false ? 0.0 : abs($box[4] - $box[0]);
    }

    private function membershipNumber(User $user, ?Activation $activation): string
    {
        return $activation?->policy_number
            ?: 'TL'.now()->year.str_pad((string) $user->UserID, 3, '0', STR_PAD_LEFT);
    }

    private function dateOfIssue(?Activation $activation): string
    {
        if ($activation?->policy_effectivity_date) {
            return Carbon::parse($activation->policy_effectivity_date)->format('F d, Y');
        }

        if ($activation?->updated_at) {
            return Carbon::parse($activation->updated_at)->format('F d, Y');
        }

        return 'N/A';
    }

    private function validUntil(?Activation $activation): string
    {
        return $activation?->policy_expiration_date
            ? Carbon::parse($activation->policy_expiration_date)->format('F d, Y')
            : 'N/A';
    }

    /** Remove generated ID images older than 7 days. */
    private function cleanupOldCards(string $directory): void
    {
        $cutoff = now()->subDays(7)->getTimestamp();

        foreach (glob($directory.'/id-card-*.jpg') ?: [] as $file) {
            if (filemtime($file) < $cutoff) {
                @unlink($file);
            }
        }
    }
}
