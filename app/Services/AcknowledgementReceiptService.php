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
 *   Left table (navy) ... x 29-448, label cell x 30-167, value cell x 168-446:
 *     Header "FORM OF PAYMENT" .. y 373-421
 *     Cash row ........... y 421-476
 *     Check row .......... y 476-536
 *     Bank row ........... y 536-596
 *     Total row .......... y 596-658
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
        ?string $cashierSignature = null,
        ?string $dateReceipt = null,
        ?Activation $activation = null
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
            $approvedActivation = Activation::where('UserID', $user->UserID)
                ->where('status', 'Approved')
                ->latest('ActivationID')
                ->first();

            $activationPolicyNumber = $approvedActivation?->policy_number;

            /*
            |----------------------------------------------------------------------
            | Payment method
            |----------------------------------------------------------------------
            | Taken from the activation this receipt belongs to (the controller
            | passes it in) so a renewal can never print the newer activation's
            | method on an older receipt. Falls back to the member's latest
            | approved activation for older callers.
            |----------------------------------------------------------------------
            */
            $rawPaymentMethod = $activation?->payment_method;

            if (blank($rawPaymentMethod)) {
                $rawPaymentMethod = $approvedActivation?->payment_method;
            }

            $paymentMethod = strtolower(trim((string) ($rawPaymentMethod ?: 'Cash')));

            $this->text($image, (string) ($activationPolicyNumber ?? ''), 295, 303, 15, self::FONT_BOLD, $ink, 400);

            // Date
            // $this->text($image, now()->format('M j, Y'), 1370, 440, 15, self::FONT_BOLD, $ink, 240);
            $this->text($image, $dateReceipt ?: now()->format('M j, Y'), 1180, 303, 15, self::FONT_BOLD, $ink, 240);

            Log::info('Generated filled acknowledgement receipt.', [
                'member_UserID' => $user->UserID,
                'receipt_number' => $receiptNumber
            ]);

            /*
            |--------------------------------------------------------------------------
            | Left table — FORM OF PAYMENT
            |--------------------------------------------------------------------------
            | The printed labels (Cash / Check / Bank) are part of the form and are
            | never painted over. The value cells carry:
            |
            |   Cash row  -> the amount paid
            |   Bank row  -> the payment method (Cash, GCash, Maya, Bank Transfer)
            |   Total row -> the amount paid
            |
            | so a receipt always states the method without touching the template.
            |--------------------------------------------------------------------------
            */

            // Cash row: y 421-476 — amount paid.
            $this->text($image, $amountText, 185, 461, 17, self::FONT_BOLD, $ink, 250);

            // Bank row: y 536-596 — the payment method as text.
            $this->text($image, $this->paymentLabel($paymentMethod), 185, 576, 17, self::FONT_BOLD, $ink, 250);

            // Total row: y 596-658 — amount paid.
            $this->text($image, $amountText, 185, 638, 17, self::FONT_BOLD, $ink, 250);


            /*
            |----------------------------------------------------------------------
            | Receipt No. — the activation's issued receipt number, drawn after
            | the printed "Receipt No." label (ends x191) inside x 47-429 box.
            |----------------------------------------------------------------------
            */
            if (filled($receiptNumber)) {
                $this->text($image, (string) $receiptNumber, 250, 753, 19, self::FONT_BOLD, $ink, 210);
            }

            /*
            |----------------------------------------------------------------------
            | Right table — values go in the VALUE column (x 744+), never over
            | the printed labels (x 457-743). "Payment for" is pre-printed on
            | this template — do not draw it.
            |----------------------------------------------------------------------
            */
            // Payment From — "Lastname, Firstname Middlename" (row 468-507)
            $this->text($image, $this->formatName($user), 797, 390, 15, self::FONT_BOLD, $ink, 850);

            // Payment for — PRE-PRINTED ("With (HMO ... Insurance.") — skipped

            // Premium Amount Paid (row 549-588)
            $this->text($image, 'PHP '.$amountText, 797, 504, 15, self::FONT_BOLD, $ink, 850);

            // Amount in words (row 591-630)
            $this->text($image, $amountWords, 797, 549, 15, self::FONT_REGULAR, $ink, 850);

            // TIN No. — intentionally blank (members have no TIN on file)

            // Email Address (value box x 1282-1622, row 633-670)
            $this->text($image, (string) ($user->email ?? ''), 1198, 593, 13, self::FONT_REGULAR, $ink, 315);

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
            Log::info('RECEIPT: Preparing to paste cashier signature.', [
                'cashier_name' => $cashierName,
                'cashier_signature' => $cashierSignature,
                'signature_is_empty' => blank($cashierSignature),
            ]);
            $this->pasteSignature($image, $cashierSignature);

            if (filled($cashierName)) {
                // Name directly BELOW the signature
                $this->text(
                    $image,
                    (string) $cashierName,
                    700,
                    770,
                    25, // slightly larger
                    self::FONT_BOLD,
                    $ink,
                    680,
                    1000
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
    Log::info('========== CASHIER SIGNATURE DEBUG START ==========');

    try {
        // STEP 1: Validate path
        if (blank($storagePath)) {
            Log::warning('SIGNATURE FAILED: Storage path is empty.');
            return;
        }

        $storagePath = ltrim(
            str_replace('\\', '/', trim($storagePath)),
            '/'
        );

        Log::info('SIGNATURE: Original storage path.', [
            'path' => $storagePath,
        ]);

        // STEP 2: Resolve file
        $binary = null;
        $resolvedPath = null;
        $storageExists = false;
        $publicExists = false;

        if (Storage::disk('public')->exists($storagePath)) {
            $storageExists = true;

            $binary = Storage::disk('public')->get($storagePath);
            $resolvedPath = Storage::disk('public')->path($storagePath);

            Log::info('SIGNATURE: Found in public storage.', [
                'path' => $resolvedPath,
            ]);
        } else {
            $filename = basename($storagePath);

            $publicImagePath = public_path('images/' . $filename);

            $publicExists = is_file($publicImagePath);

            if ($publicExists) {
                $binary = file_get_contents($publicImagePath);
                $resolvedPath = $publicImagePath;

                Log::info('SIGNATURE: Found in public/images.', [
                    'path' => $resolvedPath,
                ]);
            }
        }

        if ($binary === null || $binary === false) {
            Log::error('SIGNATURE FAILED: File does not exist.', [
                'storage_path' => $storagePath,
                'storage_exists' => $storageExists,
                'public_images_exists' => $publicExists,
                'public_images_path' => public_path(
                    'images/' . basename($storagePath)
                ),
                'storage_public_root' => Storage::disk('public')->path(''),
            ]);

            return;
        }

        Log::info('SIGNATURE: File successfully loaded.', [
            'resolved_path' => $resolvedPath,
            'file_size' => strlen($binary),
        ]);

        // STEP 3: Inspect image
        $imageInfo = @getimagesizefromstring($binary);

        if ($imageInfo === false) {
            Log::error('SIGNATURE FAILED: Invalid image file.', [
                'path' => $resolvedPath,
            ]);

            return;
        }

        Log::info('SIGNATURE: Image information.', [
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
            'mime' => $imageInfo['mime'] ?? 'unknown',
            'bits' => $imageInfo['bits'] ?? null,
        ]);

        // STEP 4: Decode image
        $signature = @imagecreatefromstring($binary);

        if ($signature === false) {
            Log::error('SIGNATURE FAILED: GD could not decode image.', [
                'path' => $resolvedPath,
                'mime' => $imageInfo['mime'] ?? null,
                'gd_available' => extension_loaded('gd'),
            ]);

            return;
        }

        if (!imageistruecolor($signature)) {
            imagepalettetotruecolor($signature);
        }

        $srcWidth = imagesx($signature);
        $srcHeight = imagesy($signature);

        Log::info('SIGNATURE: GD image decoded.', [
            'width' => $srcWidth,
            'height' => $srcHeight,
            'truecolor' => imageistruecolor($signature),
        ]);

        // STEP 5: Detect actual signature ink
        $left = $srcWidth;
        $right = -1;
        $top = $srcHeight;
        $bottom = -1;

        $visiblePixels = 0;
        $transparentPixels = 0;
        $whitePixels = 0;

        for ($y = 0; $y < $srcHeight; $y++) {
            for ($x = 0; $x < $srcWidth; $x++) {

                $pixel = imagecolorat($signature, $x, $y);

                $r = ($pixel >> 16) & 0xFF;
                $g = ($pixel >> 8) & 0xFF;
                $b = $pixel & 0xFF;

                $sourceAlpha = ($pixel >> 24) & 0x7F;

                if ($sourceAlpha >= 120) {
                    $transparentPixels++;
                    continue;
                }

                $brightness =
                    (0.299 * $r) +
                    (0.587 * $g) +
                    (0.114 * $b);

                if ($brightness >= 245) {
                    $whitePixels++;
                    continue;
                }

                $visiblePixels++;

                $left = min($left, $x);
                $right = max($right, $x);
                $top = min($top, $y);
                $bottom = max($bottom, $y);
            }
        }

        Log::info('SIGNATURE: Ink detection results.', [
            'visible_ink_pixels' => $visiblePixels,
            'transparent_pixels' => $transparentPixels,
            'white_pixels' => $whitePixels,
            'left' => $left,
            'right' => $right,
            'top' => $top,
            'bottom' => $bottom,
        ]);

        if ($visiblePixels === 0 || $right < $left || $bottom < $top) {
            Log::error('SIGNATURE FAILED: No visible signature ink detected.', [
                'path' => $resolvedPath,
                'width' => $srcWidth,
                'height' => $srcHeight,
            ]);

            imagedestroy($signature);
            return;
        }

        // STEP 6: Calculate signature dimensions
        $inkWidth = $right - $left + 1;
        $inkHeight = $bottom - $top + 1;

        Log::info('SIGNATURE: Detected signature dimensions.', [
            'ink_width' => $inkWidth,
            'ink_height' => $inkHeight,
        ]);

        // STEP 7: Create cropped transparent signature
        $clean = imagecreatetruecolor($inkWidth, $inkHeight);

        imagealphablending($clean, false);
        imagesavealpha($clean, true);

        $transparent = imagecolorallocatealpha(
            $clean,
            255,
            255,
            255,
            127
        );

        imagefill($clean, 0, 0, $transparent);

        $colors = [];

        for ($y = $top; $y <= $bottom; $y++) {
            for ($x = $left; $x <= $right; $x++) {

                $pixel = imagecolorat($signature, $x, $y);

                $r = ($pixel >> 16) & 0xFF;
                $g = ($pixel >> 8) & 0xFF;
                $b = $pixel & 0xFF;

                $sourceAlpha = ($pixel >> 24) & 0x7F;

                $brightness =
                    (0.299 * $r) +
                    (0.587 * $g) +
                    (0.114 * $b);

                // Preserve the original transparency of the PNG.
                // Recolor its gray signature strokes into solid dark receipt ink.
                if (($imageInfo['mime'] ?? '') === 'image/png' && $transparentPixels > 0) {
                    $opacity = 1 - ($sourceAlpha / 127);
                } else {
                    // For JPEG or PNG files with a white background.
                    $opacity = (1 - ($sourceAlpha / 127))
                        * (1 - ($brightness / 255));
                }

                $alpha = (int) round(127 * (1 - $opacity));

                $alpha = max(0, min(127, $alpha));

                if ($alpha >= 127) {
                    continue;
                }

                if (!isset($colors[$alpha])) {
                    $colors[$alpha] = imagecolorallocatealpha(
                        $clean,
                        self::INK[0],
                        self::INK[1],
                        self::INK[2],
                        $alpha
                    );
                }

                imagesetpixel(
                    $clean,
                    $x - $left,
                    $y - $top,
                    $colors[$alpha]
                );
            }
        }

        imagedestroy($signature);

        Log::info('SIGNATURE: Transparent signature created.', [
            'width' => imagesx($clean),
            'height' => imagesy($clean),
        ]);


        // STEP 8: Destination area inside By: box
        $bandX = 560;
        $bandY = 700;
        $bandWidth = 800;
        $bandHeight = 80;

        // STEP 9: Preserve original resolution where possible.
        // Never enlarge the signature; only reduce it when necessary.
        $scale = min(
            1,
            $bandWidth / $inkWidth,
            $bandHeight / $inkHeight
        );

        $dstWidth = max(1, (int) round($inkWidth * $scale));
        $dstHeight = max(1, (int) round($inkHeight * $scale));

        // STEP 10: Center signature
        $dstX = $bandX + (int) (($bandWidth - $dstWidth) / 2);
        $dstY = $bandY + (int) (($bandHeight - $dstHeight) / 2);

        // STEP 11: Validate destination
        if (
            $dstX < 0 ||
            $dstY < 0 ||
            ($dstX + $dstWidth) > imagesx($image) ||
            ($dstY + $dstHeight) > imagesy($image)
        ) {
            Log::error('SIGNATURE FAILED: Destination outside receipt boundaries.');

            imagedestroy($clean);
            return;
        }

        // STEP 12: Paste signature
        imagealphablending($image, true);

        if ($dstWidth === $inkWidth && $dstHeight === $inkHeight) {
            // No resizing: preserve original pixels.
            $result = imagecopy(
                $image,
                $clean,
                $dstX,
                $dstY,
                0,
                0,
                $inkWidth,
                $inkHeight
            );
        } else {
            // Downscale only when necessary.
            $result = imagecopyresampled(
                $image,
                $clean,
                $dstX,
                $dstY,
                0,
                0,
                $dstWidth,
                $dstHeight,
                $inkWidth,
                $inkHeight
            );
        }

        imagedestroy($clean);

        if (!$result) {
            Log::error('SIGNATURE FAILED: Could not paste signature.');
            return;
        }

        Log::info('SIGNATURE SUCCESS: Cashier signature pasted.', [
            'original_width' => $inkWidth,
            'original_height' => $inkHeight,
            'destination_width' => $dstWidth,
            'destination_height' => $dstHeight,
        ]);

            } catch (\Throwable $e) {

                Log::error('SIGNATURE EXCEPTION: Failed to paste cashier signature.', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'path' => $storagePath,
                ]);
            }

            Log::info('========== CASHIER SIGNATURE DEBUG END ==========');
        }


    /**
     * Human label for a stored activations.payment_method value.
     */
    private function paymentLabel(string $method): string
    {
        return match ($method) {
            'gcash' => 'GCash',
            'maya', 'paymaya' => 'Maya',
            'bank transfer', 'bank' => 'Bank Transfer',
            'check', 'cheque' => 'Check',
            '' => 'Cash',
            default => ucfirst($method),
        };
    }

    /**
     * Draw the home address on a single centred line inside its 38px row
     * (673-711), auto-shrinking/truncating when too wide.
     */
    private function address($image, string $address, int $x, $ink): void
    {
        $this->text($image, $address, $x + 70, 640, 15, self::FONT_REGULAR, $ink, 850);
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
