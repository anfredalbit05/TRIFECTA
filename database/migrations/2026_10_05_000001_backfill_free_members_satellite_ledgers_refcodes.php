<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
|---------------------------------------------------------------------------
| FREE MEMBER BACKFILL
|---------------------------------------------------------------------------
| The first version of ActivationApprovalService::provisionFreeMembers()
| provisioned the free members correctly EXCEPT:
|
|   1. it never wrote activations.satelliteID — the Free Members modal
|      (and the member's own satellite view) query by satelliteID, so the
|      rows never showed up;
|   2. it wrote ONE combined Pending MF ledger row per member instead of
|      the per-fee breakdown (FEE001 450, FEE002 400, ...) a regular
|      approval writes;
|   3. it left users.referralCode NULL — invites made with a free member's
|      referral code could never resolve, so the satellite owner lost the
|      OV walk (50 → 10) on those invites.
|
| This migration repairs rows already provisioned by that version. It is
| idempotent: re-running it changes nothing once every row is fixed, and a
| fresh database (tests) has no free members at migration time, so it is a
| no-op there too.
|---------------------------------------------------------------------------
*/
return new class extends Migration
{
    public function up(): void
    {
        $this->linkActivationsToSatellites();
        $this->splitCombinedLedgerRows();
        $this->generateMissingReferralCodes();
    }

    public function down(): void
    {
        // Data repair — nothing to reverse.
    }

    /*
    |-----------------------------------------------------------------------
    | 1. activations.satelliteID
    |-----------------------------------------------------------------------
    | Free users are named <Prefix>-username-<n> where <Prefix> is the
    | satellite_name without its spaces (plus the satelliteID when two
    | satellites share a name), so the owner of the matching Satellite row
    | can be recovered from the username.
    |-----------------------------------------------------------------------
    */
    private function linkActivationsToSatellites(): void
    {
        $orphaned = DB::table('activations')
            ->where('isFree', true)
            ->whereNull('satelliteID')
            ->pluck('UserID')
            ->all();

        if ($orphaned === []) {
            return;
        }

        $satellites = DB::table('satellites')->get();

        foreach ($satellites as $satellite) {
            $base = preg_replace('/\s+/', '', (string) $satellite->satellite_name) ?: 'Satellite';

            // Provisioning falls back to <Prefix><satelliteID> when the
            // plain prefix was already taken by an earlier satellite.
            $prefixes = [$base, $base.(int) $satellite->satelliteID];

            foreach ($prefixes as $prefix) {
                DB::table('activations')
                    ->where('isFree', true)
                    ->whereNull('satelliteID')
                    ->whereIn('UserID', function ($query) use ($prefix) {
                        $query->select('UserID')
                            ->from('users')
                            ->where('username', 'like', $prefix.'-username-%');
                    })
                    ->update(['satelliteID' => $satellite->satelliteID]);
            }
        }
    }

    /*
    |-----------------------------------------------------------------------
    | 2. per-fee ledger breakdown
    |-----------------------------------------------------------------------
    | A combined row is one whose RefID is NOT a FeeID (it held the UserID).
    | Replace it with one row per membership fee — same status, same
    | activation — so free members show the FEE001 / FEE002 / ... breakdown
    | exactly like a regular approval.
    |-----------------------------------------------------------------------
    */
    private function splitCombinedLedgerRows(): void
    {
        $fees = DB::table('membership_fees')->orderBy('FeeID')->get(['FeeID', 'Cost']);

        if ($fees->isEmpty()) {
            return;
        }

        $feeIDs = $fees->pluck('FeeID')->all();

        $combinedRows = DB::table('ledgers')
            ->where('Source', 'MF')
            ->where('ActivationID', '!=', null)
            ->whereNotIn('RefID', $feeIDs)
            ->get();

        foreach ($combinedRows as $row) {
            $isFreeActivation = DB::table('activations')
                ->where('ActivationID', $row->ActivationID)
                ->where('isFree', true)
                ->exists();

            if (! $isFreeActivation) {
                continue;
            }

            DB::transaction(function () use ($row, $fees) {
                DB::table('ledgers')->where('TransactionID', $row->TransactionID)->delete();

                foreach ($fees as $fee) {
                    DB::table('ledgers')->insert([
                        'RefID' => $fee->FeeID,
                        'amount' => $fee->Cost,
                        'Source' => 'MF',
                        'ActivationID' => $row->ActivationID,
                        'status' => $row->status,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ]);
                }
            });
        }
    }

    /*
    |-----------------------------------------------------------------------
    | 3. referral codes
    |-----------------------------------------------------------------------
    | Free members need their own referralCode so invites can use it as
    | uplineCode and walk the normal OV chain into the satellite owner.
    | Same 6-character generator the application forms use.
    |-----------------------------------------------------------------------
    */
    private function generateMissingReferralCodes(): void
    {
        $missing = DB::table('users')
            ->whereNull('referralCode')
            ->where('username', 'like', '%-username-%')
            ->where('RoleID', 4)
            ->pluck('UserID');

        foreach ($missing as $userID) {
            do {
                $code = strtoupper(substr(md5(uniqid((string) mt_rand(), true)), 0, 6));
            } while (DB::table('users')->where('referralCode', $code)->exists());

            DB::table('users')->where('UserID', $userID)->update(['referralCode' => $code]);
        }
    }
};
