<?php

namespace App\Http\Controllers;

use App\Models\Activation;
use App\Models\ActivationHistory;
use App\Models\MembershipFee;
use App\Models\User;
use App\Services\AcknowledgementReceiptService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReceiptController extends Controller
{
    /**
     * Download the filled acknowledgement receipt for a member.
     *
     * The receipt is regenerated on the fly from the member's current data
     * (policy number, name, email, address, amount) plus the receipt number
     * and cashier identity snapshotted on their latest approved activation.
     */
    public function adminDownload(int $userId): StreamedResponse
    {
        abort_unless(Auth::check(), 403);

        $user = User::findOrFail($userId);

        // Receipt number + cashier identity from the latest approved activation
        $approved = Activation::where('UserID', $user->UserID)
            ->where('status', 'Approved')
            ->whereNotNull('receipt_number')
            ->latest('ActivationID')
            ->first([
                'ActivationID',
                'receipt_number',
                'cashier_name',
                'cashier_signature',
                'amount',
            ]);

        abort_unless($approved, 404, 'No approved activation receipt is available.');

        $amount = (float) $approved->amount;

        $history = ActivationHistory::where('ActivationID', $approved->ActivationID)
            ->where('status', 'Approved')
            ->latest()
            ->first();

        $path = app(AcknowledgementReceiptService::class)->generate(
            $user,
            $amount,
            $approved?->receipt_number,
            $approved?->cashier_name,
            $approved?->cashier_signature,
            $history?->created_at
        );

        if ($path === null || ! is_file($path)) {
            abort(500, 'Could not generate the acknowledgement receipt.');
        }

        Log::info('Admin downloaded acknowledgement receipt.', [
            'member_UserID' => $user->UserID,
            'receipt_number' => $approved?->receipt_number,
            'downloaded_by' => Auth::id(),
        ]);

        $filename = 'Acknowledgement-Receipt-'.$user->lastname.'-'.($approved?->receipt_number ?: $user->UserID).'.jpg';

        return response()->streamDownload(function () use ($path) {
            $stream = fopen($path, 'rb');
            fpassthru($stream);
            fclose($stream);
        }, $filename, [
            'Content-Type' => 'image/jpeg',
        ]);
    }

    /**
 * Download the currently authenticated member's latest approved
 * acknowledgement receipt.
 */

public function memberDownload(): StreamedResponse
{
    abort_unless(Auth::check(), 403);

    /** @var User $user */
    $user = Auth::user();

    // Get this member's latest approved activation.
    $approved = Activation::where('UserID', $user->UserID)
        ->where('status', 'Approved')
        ->whereNotNull('receipt_number')
        ->latest('ActivationID')
        ->first([
            'ActivationID',
            'receipt_number',
            'cashier_name',
            'cashier_signature',
            'amount',
        ]);

    abort_unless(
        $approved,
        404,
        'No approved activation receipt is available.'
    );

    // Use the amount stored on this specific activation.
    $amount = (float) $approved->amount;

    $path = app(AcknowledgementReceiptService::class)->generate(
        $user,
        $amount,
        $approved->receipt_number,
        $approved->cashier_name,
        $approved->cashier_signature
    );

    if ($path === null || ! is_file($path)) {
        abort(500, 'Could not generate the acknowledgement receipt.');
    }

    Log::info('Member downloaded acknowledgement receipt.', [
        'member_UserID' => $user->UserID,
        'activation_id' => $approved->ActivationID,
        'receipt_number' => $approved->receipt_number,
        'amount' => $amount,
    ]);

    $filename = 'Acknowledgement-Receipt-'
        . $user->lastname
        . '-'
        . $approved->receipt_number
        . '.jpg';

    return response()->streamDownload(function () use ($path) {
        $stream = fopen($path, 'rb');

        if ($stream !== false) {
            fpassthru($stream);
            fclose($stream);
        }
    }, $filename, [
        'Content-Type' => 'image/jpeg',
    ]);
}

}
