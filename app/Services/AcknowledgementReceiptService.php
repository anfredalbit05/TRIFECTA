<?php

namespace App\Services;

use App\Models\Activation;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Fills the blank acknowledgement-receipt form (public/images/ack-receipt.jpg,
 * 1669 x 942 px — 2026 blue design) with the activated member's real data
 * using the GD library and returns a generated JPEG that the approval email
 * attaches and the admin Download Receipt button serves.
 *
 * Mapped form geometry (pixel coordinates probed from the template):
 *
 *   Policy Number box ... x 281-715,  y 414-455  (blank in template)
 *   Date box ............ x 1355-1623, y 414-455 (blank in template)
 *   Left table (navy) ... x 47-457, header 467-508:
 *     Cash row ........... y 509-554 (label col x47-151, value x152-428)
 *     Check row .......... y 555-599
 *     Bank row ........... y 600-643
 *     Total row .......... y 644-687 (value x152-428)
 *   Receipt No. box ..... x 47-429,  y 702-748 (label ends x191 — receipt no. goes after it)
 *   Right table ......... x 457-1623, label col x457-743, value col x744-1622:
 *     Payment From ....... y 468-507
 *     Payment for ........ y 508-547  (PRE-PRINTED — never draw)
 *     Premium Amount Paid  y 549-588
 *     Amount in words .... y 591-630
 *     TIN No. / Email .... y 633-670  (Email label ends x1260, value x1282-1622)
 *     Home Address ....... y 673-711
 *   By: box ............. x 457-1230, y 730-817 (divider line at y774):
 *                         signature band y734-758 (after "By:" label ending
 *                         x510), cashier name baseline y769 between the
 *                         signature and the divider, pre-printed
 *                         "Cashier/Authorized Representative" below (y786-809)
 */
class AcknowledgementReceiptService
{
    /** Blank form template inside the public directory. */
    private const TEMPLATE = 'images/ack-receipt.jpg';

    /** Built-in Windows fonts used to imitate the form's printed type. */
private const FONT_REGULAR = __DIR__.'/../../vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf';

private const FONT_BOLD = __DIR__.'/../../vendor/dompdf/dompdf/lib/fonts/DejaVuSans-Bold.ttf';

    private const INK = [0x1E, 0x29, 0x3B]; // slate-800

    /**
     * Generate a filled receipt JPEG for the newly activated member.
     *
     * @param  string|null  $receiptNumber  Issued receipt number (e.g. 000550)
     * @param  string|null  $cashierName  Cashier name (falls back to the
     *                                    cashier_name setting)
     * @param  string|null  $cashierSignature  Storage path of the cashier
     *                                         signature image (falls back to
     *                                         the cashier_signature setting)
     * @param  float  $amount  Total membership amount paid (e.g. 1999.00)
     * @return string|null Absolute path to the generated JPEG, or null on failure.
     */
    public function generate(
        User $user,
        float $amount,
        ?string $receiptNumber = null,
        ?string $cashierName = null,
        ?string $cashierSignature = null
    ): ?string {
        $templatePath = public_path(self::TEMPLATE);

        if (! is_file($templatePath)) {
            Log::warning('Acknowledgement receipt template missing; skipping filled receipt.', [
                'expected_path' => $templatePath,
            ]);

            return null;
        }

        if (! function_exists('imagecreatefromjpeg')) {
            Log::warning('GD extension unavailable; skipping filled acknowledgement receipt.');

            return null;
        }

        try {
            $image = imagecreatefromjpeg($templatePath);

            if ($image === false) {
                Log::error('Could not decode acknowledgement receipt template.', [
                    'path' => $templatePath,
                ]);

                return null;
            }

            $ink = imagecolorallocate($image, ...self::INK);
            $amountText = number_format($amount, 2);
            $amountWords = $this->amountInWords($amount);

            // Cashier identity: activation values first, then system settings
            $cashierName = $cashierName ?: Setting::where('name', 'cashier_name')->value('value');
            $cashierSignature = $cashierSignature ?: Setting::where('name', 'cashier_signature')->value('value');

            /*
            |----------------------------------------------------------------------
            | Top boxes (baselines centred in y 414-455)
            |----------------------------------------------------------------------
            */
            // Policy Number — from the member's latest APPROVED activation
            // (the users table no longer holds policy data; drawn only when
            // present).
            $activationPolicyNumber = Activation::where('UserID', $user->UserID)
                ->where('status', 'Approved')
                ->latest('ActivationID')
                ->value('policy_number');

            $this->text($image, (string) ($activationPolicyNumber ?? ''), 295, 440, 15, self::FONT_BOLD, $ink, 400);

            // Date
            $this->text($image, now()->format('M j, Y'), 1370, 440, 15, self::FONT_BOLD, $ink, 240);

            /*
            |----------------------------------------------------------------------
            | Left table (navy) — Cash amount + Total (value cell x 152-428)
            |----------------------------------------------------------------------
            */
            $this->text($image, $amountText, 165, 538, 15, self::FONT_BOLD, $ink, 250);
            $this->text($image, $amountText, 165, 671, 15, self::FONT_BOLD, $ink, 250);

            /*
            |----------------------------------------------------------------------
            | Receipt No. — the activation's issued receipt number, drawn after
            | the printed "Receipt No." label (ends x191) inside x 47-429 box.
            |----------------------------------------------------------------------
            */
            if (filled($receiptNumber)) {
                $this->text($image, (string) $receiptNumber, 205, 733, 15, self::FONT_BOLD, $ink, 210);
            }

            /*
            |----------------------------------------------------------------------
            | Right table — values go in the VALUE column (x 744+), never over
            | the printed labels (x 457-743). "Payment for" is pre-printed on
            | this template — do not draw it.
            |----------------------------------------------------------------------
            */
            // Payment From — "Lastname, Firstname Middlename" (row 468-507)
            $this->text($image, $this->formatName($user), 758, 494, 15, self::FONT_BOLD, $ink, 850);

            // Payment for — PRE-PRINTED ("With (HMO ... Insurance.") — skipped

            // Premium Amount Paid (row 549-588)
            $this->text($image, 'PHP '.$amountText, 758, 575, 15, self::FONT_BOLD, $ink, 850);

            // Amount in words (row 591-630)
            $this->text($image, $amountWords, 758, 617, 15, self::FONT_REGULAR, $ink, 850);

            // TIN No. — intentionally blank (members have no TIN on file)

            // Email Address (value box x 1282-1622, row 633-670)
            $this->text($image, (string) ($user->email ?? ''), 1295, 658, 15, self::FONT_REGULAR, $ink, 315);

            // Home Address (row 673-711, table bottom border at 711)
            $this->address($image, (string) ($user->address ?? ''), 640, $ink);

            /*
            |----------------------------------------------------------------------
            | By: — cashier signature pasted above, cashier name printed just
            | above the divider (y774), like a signed form: signature, typed
            | name underneath, pre-printed caption below the divider.
            |----------------------------------------------------------------------
            */
            /*
            |--------------------------------------------------------------------------
            | By: — signature ABOVE name
            |--------------------------------------------------------------------------
            */
            $this->pasteSignature($image, $cashierSignature);

            if (filled($cashierName)) {
                // Name directly BELOW the signature
                $this->text(
                    $image,
                    (string) $cashierName,
                    530,
                    772,
                    18, // slightly larger
                    self::FONT_BOLD,
                    $ink,
                    680,
                    870
                );
            }

            /*
            |----------------------------------------------------------------------
            | Persist to storage/app/receipts
            |----------------------------------------------------------------------
            */
            $directory = storage_path('app/receipts');

            if (! is_dir($directory)) {
                mkdir($directory, 0775, true);
            }

            $outputPath = $directory.'/receipt-'.uniqid('ack-', true).'.jpg';

            imagejpeg($image, $outputPath, 92);

            $this->cleanupOldReceipts($directory);

            return is_file($outputPath) ? $outputPath : null;
        } catch (\Throwable $e) {
            Log::error('Failed to generate filled acknowledgement receipt.', [
                'error' => $e->getMessage(),
                'UserID' => $user->UserID ?? null,
            ]);

            return null;
        }
    }

    /**
     * Delete generated receipts older than 7 days so storage/app/receipts
     * never grows unbounded (files remain long enough for queued mails).
     */
    private function cleanupOldReceipts(string $directory): void
    {
        $cutoff = now()->subDays(7)->getTimestamp();

        foreach (glob($directory.'/receipt-ack-*.jpg') ?: [] as $file) {
            if (filemtime($file) < $cutoff) {
                @unlink($file);
            }
        }
    }

    /**
     * Draw left-aligned text at a baseline coordinate, auto-shrinking and
     * truncating with an ellipsis when wider than $maxWidth. When $centerX
     * is given the finished line is centred on that pixel (never starting
     * left of $x).
     */
   private function text(
    $image,
    string $value,
    int $x,
    int $y,
    int $size,
    string $font,
    $ink,
    int $maxWidth,
    ?int $centerX = null
): void {
    $value = trim($value);

    if ($value === '') {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | TrueType font
    |--------------------------------------------------------------------------
    */
    if (is_file($font)) {

        /*
        |--------------------------------------------------------------------------
        | DO NOT AUTO-SHRINK
        |--------------------------------------------------------------------------
        | The $size passed to this function is the actual font size.
        |--------------------------------------------------------------------------
        */

        if ($centerX !== null) {

            $box = imagettfbbox(
                $size,
                0,
                $font,
                $value
            );

            $textWidth = abs($box[4] - $box[0]);

            $x = (int) (
                $centerX - ($textWidth / 2)
            );
        }

        imagettftext(
            $image,
            $size,
            0,
            $x,
            $y,
            $ink,
            $font,
            $value
        );

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | GD fallback
    |--------------------------------------------------------------------------
    */

    $fontSize = 5;

    $charWidth = imagefontwidth($fontSize);

    $textWidth = $charWidth * strlen($value);

    if ($centerX !== null) {
        $x = (int) (
            $centerX - ($textWidth / 2)
        );
    }

    imagestring(
        $image,
        $fontSize,
        $x,
        $y - imagefontheight($fontSize),
        $value,
        $ink
    );
}

    /**
     * Paste the cashier signature into the By: box (band x 530-1210,
     * y 734-758). The stored files are JPEGs without an alpha channel, so the
     * white background is knocked out to transparency first (ink darkness
     * drives opacity, keeping anti-aliased strokes smooth), the ink is then
     * trimmed to its bounding box and CENTRED in the band. Silently skipped
     * when the file is missing or unreadable.
     */
    /**
     * Paste cashier signature above the cashier name.
     *
     * The signature image may be JPEG/PNG and may have a white background.
     * White/light pixels become transparent while the actual signature ink
     * is converted to a dark ink color so it remains visible on Railway/PDF/etc.
     */
    private function pasteSignature($image, ?string $storagePath): void
    {
        if (blank($storagePath)) {
            return;
        }

        try {
            if (! Storage::disk('public')->exists($storagePath)) {
                Log::warning('Cashier signature file missing; receipt signed without signature.', [
                    'path' => $storagePath,
                ]);

                return;
            }

            $binary = Storage::disk('public')->get($storagePath);

            $signature = @imagecreatefromstring($binary);

            if ($signature === false) {
                Log::warning('Cashier signature file could not be decoded; skipped.', [
                    'path' => $storagePath,
                ]);

                return;
            }

            $srcWidth = imagesx($signature);
            $srcHeight = imagesy($signature);

            /*
            |--------------------------------------------------------------------------
            | STEP 1: Find the white signature canvas
            |--------------------------------------------------------------------------
            |
            | Some uploaded images contain a dark/black border around the actual
            | white signature area. We must ignore that border completely.
            |
            */

            $canvasLeft = $srcWidth;
            $canvasRight = -1;
            $canvasTop = $srcHeight;
            $canvasBottom = -1;

            for ($y = 0; $y < $srcHeight; $y++) {
                for ($x = 0; $x < $srcWidth; $x++) {

                    $rgb = imagecolorat($signature, $x, $y);

                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;

                    /*
                     * White / near-white pixels belong to the signature canvas.
                     */
                    if ($r >= 245 && $g >= 245 && $b >= 245) {

                        $canvasLeft = min($canvasLeft, $x);
                        $canvasRight = max($canvasRight, $x);
                        $canvasTop = min($canvasTop, $y);
                        $canvasBottom = max($canvasBottom, $y);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | No white canvas detected
            |--------------------------------------------------------------------------
            */

            if ($canvasRight < $canvasLeft || $canvasBottom < $canvasTop) {
                Log::warning('Could not detect white signature canvas.', [
                    'path' => $storagePath,
                ]);

                imagedestroy($signature);

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 2: Crop to the actual white signature canvas
            |--------------------------------------------------------------------------
            */

            $canvasWidth = $canvasRight - $canvasLeft + 1;
            $canvasHeight = $canvasBottom - $canvasTop + 1;

            $clean = imagecreatetruecolor(
                $canvasWidth,
                $canvasHeight
            );

            imagealphablending($clean, false);
            imagesavealpha($clean, true);

            $transparent = imagecolorallocatealpha(
                $clean,
                0,
                0,
                0,
                127
            );

            imagefill(
                $clean,
                0,
                0,
                $transparent
            );

            /*
            |--------------------------------------------------------------------------
            | STEP 3: Extract ONLY the actual signature ink
            |--------------------------------------------------------------------------
            */

            for ($y = $canvasTop; $y <= $canvasBottom; $y++) {

                for ($x = $canvasLeft; $x <= $canvasRight; $x++) {

                    $rgb = imagecolorat($signature, $x, $y);

                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;

                    $brightness =
                        (0.299 * $r) +
                        (0.587 * $g) +
                        (0.114 * $b);

                    /*
                     * Ignore white background.
                     */
                    if ($brightness >= 245) {
                        continue;
                    }

                    /*
                     * Convert darkness into opacity.
                     *
                     * Dark pixels  = opaque
                     * Light pixels = transparent
                     */
                    $alpha = (int) round(
                        ($brightness / 255) * 127
                    );

                    $alpha = max(0, min(127, $alpha));

                    $inkColor = imagecolorallocatealpha(
                        $clean,
                        self::INK[0],
                        self::INK[1],
                        self::INK[2],
                        $alpha
                    );

                    imagesetpixel(
                        $clean,
                        $x - $canvasLeft,
                        $y - $canvasTop,
                        $inkColor
                    );
                }
            }

            imagedestroy($signature);

            /*
            |--------------------------------------------------------------------------
            | STEP 4: Find the actual signature ink bounds
            |--------------------------------------------------------------------------
            */

            $left = $canvasWidth;
            $right = -1;
            $top = $canvasHeight;
            $bottom = -1;

            for ($y = 0; $y < $canvasHeight; $y++) {

                for ($x = 0; $x < $canvasWidth; $x++) {

                    $pixel = imagecolorat($clean, $x, $y);

                    $alpha = ($pixel >> 24) & 0x7F;

                    /*
                     * Anything with meaningful opacity is actual ink.
                     */
                    if ($alpha < 120) {

                        $left = min($left, $x);
                        $right = max($right, $x);
                        $top = min($top, $y);
                        $bottom = max($bottom, $y);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | No signature found
            |--------------------------------------------------------------------------
            */

            if ($right < $left || $bottom < $top) {

                imagedestroy($clean);

                Log::warning(
                    'Cashier signature contained no visible ink.',
                    ['path' => $storagePath]
                );

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 5: Actual signature dimensions
            |--------------------------------------------------------------------------
            */

            $inkWidth = $right - $left + 1;
            $inkHeight = $bottom - $top + 1;

            /*
            |--------------------------------------------------------------------------
            | STEP 6: Receipt signature area
            |--------------------------------------------------------------------------
            |
            | Signature sits above cashier name.
            |
            */

            $bandX = 530;
            $bandY = 690;

            $bandWidth = 750;
            $bandHeight = 80;

            /*
            |--------------------------------------------------------------------------
            | STEP 7: Scale while maintaining aspect ratio
            |--------------------------------------------------------------------------
            */

            $scale = min(
                $bandWidth / $inkWidth,
                $bandHeight / $inkHeight
            );

            $dstWidth = max(
                1,
                (int) round($inkWidth * $scale)
            );

            $dstHeight = max(
                1,
                (int) round($inkHeight * $scale)
            );

            /*
            |--------------------------------------------------------------------------
            | STEP 8: Center signature
            |--------------------------------------------------------------------------
            */

            $dstX = $bandX +
                (int) (($bandWidth - $dstWidth) / 2);

            $dstY = $bandY +
                (int) (($bandHeight - $dstHeight) / 2);

            /*
            |--------------------------------------------------------------------------
            | STEP 9: Paste signature
            |--------------------------------------------------------------------------
            */

            imagealphablending($image, true);

            imagecopyresampled(
                $image,
                $clean,
                $dstX,
                $dstY,
                $left,
                $top,
                $dstWidth,
                $dstHeight,
                $inkWidth,
                $inkHeight
            );

            imagedestroy($clean);

        } catch (\Throwable $e) {

            Log::error(
                'Failed to paste cashier signature onto receipt.',
                [
                    'error' => $e->getMessage(),
                    'path' => $storagePath,
                ]
            );
        }
    }

    /**
     * Draw the home address on a single centred line inside its 38px row
     * (673-711), auto-shrinking/truncating when too wide.
     */
    private function address($image, string $address, int $x, $ink): void
    {
        $this->text($image, $address, $x, 698, 15, self::FONT_REGULAR, $ink, 850);
    }

    private function truncatePlain(string $text, float $maxChars): string
    {
        return mb_strlen($text) > $maxChars ? mb_substr($text, 0, (int) $maxChars - 1).'…' : $text;
    }

    /**
     * "Lastname, Firstname Middlename"
     */
    private function formatName(User $user): string
    {
        $first = trim($user->firstname ?? '');
        $middle = trim($user->middlename ?? '');
        $last = trim($user->lastname ?? '');

        if ($last === '') {
            return $first;
        }

        return trim($last.', '.$first.($middle !== '' ? ' '.$middle : ''));
    }

    /**
     * 1999.00 → "One Thousand Nine Hundred Ninety Nine Pesos Only"
     */
    private function amountInWords(float $amount): string
    {
        $pesos = (int) floor($amount);
        $centavos = (int) round(($amount - $pesos) * 100);

        $words = $this->numberToWords($pesos).' Pesos';

        if ($centavos > 0) {
            $words .= ' and '.$this->numberToWords($centavos).' Centavos';
        }

        return trim($words.' Only');
    }

    private function numberToWords(int $number): string
    {
        $ones = [
            '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
            'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
            'Seventeen', 'Eighteen', 'Nineteen',
        ];

        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        if ($number === 0) {
            return 'Zero';
        }

        if ($number < 20) {
            return $ones[$number];
        }

        if ($number < 100) {
            return trim($tens[intdiv($number, 10)].' '.$ones[$number % 10]);
        }

        if ($number < 1000) {
            return trim($ones[intdiv($number, 100)].' Hundred'.($number % 100 !== 0 ? ' '.$this->numberToWords($number % 100) : ''));
        }

        if ($number < 1000000) {
            return trim($this->numberToWords(intdiv($number, 1000)).' Thousand'.($number % 1000 !== 0 ? ' '.$this->numberToWords($number % 1000) : ''));
        }

        return (string) $number;
    }
}
