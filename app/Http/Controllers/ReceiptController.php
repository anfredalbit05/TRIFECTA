<?php

namespace App\Http\Controllers;

use App\Models\Activation;
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

        // Same membership amount the approval email uses (sum of MembershipFee).
        $amount = (float) MembershipFee::sum('Cost');

        // Receipt number + cashier identity from the latest approved activation
        $approved = Activation::where('UserID', $user->UserID)
            ->where('status', 'Approved')
            ->whereNotNull('receipt_number')
            ->latest('ActivationID')
            ->first(['ActivationID', 'receipt_number', 'cashier_name', 'cashier_signature']);

        $path = app(AcknowledgementReceiptService::class)->generate(
            $user,
            $amount,
            $approved?->receipt_number,
            $approved?->cashier_name,
            $approved?->cashier_signature
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
}
