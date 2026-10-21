<?php

namespace App\Services;

use App\Mail\ActivationApprovedMail;
use App\Mail\ActivationRejectedMail;
use App\Mail\InviteeApprovedMail;
use App\Models\Activation;
use App\Models\ActivationHistory;
use App\Models\Beneficiary;
use App\Models\Ledger;
use App\Models\MembershipFee;
use App\Models\Satellite;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ActivationApprovalService
{
    public const TYPE_REGULAR = 'regular';

    public const TYPE_SATELLITE = 'satellite';

    /**
     * A free member provisioned when a satellite activation was approved.
     * No payment was collected, so no policy/receipt numbers and no
     * membership-fee rows are written — only the Pending ledger row created
     * at provisioning is completed.
     */
    public const TYPE_FREE = 'free';

    private const OV_RATES = [
        2 => 50.00,
        3 => 40.00,
        4 => 30.00,
        5 => 20.00,
        6 => 10.00,
    ];


    public function approve(
        Activation $activation,
        int $selectedUserID,
        string $type = self::TYPE_REGULAR,
        array $activationAttributes = []
    ): bool {
        // Approving a Silver/Gold satellite provisions 40 / 80 free members
        // (bcrypt cost 12 ≈ 400 ms per password + hundreds of ledger rows)
        // and then sends mail over SMTP — far beyond the default 30s
        // max_execution_time, which aborted the request halfway (the fatal
        // fired inside the SMTP stream while members were still being
        // created). Wait for the whole job instead: no execution-time cap,
        // and keep running even if the browser navigates away mid-batch.
        $this->removeExecutionLimits();

        $referralUser = $this->findReferralUser($activation);

        $gcashCF = $this->settingEnabled('gcash_convenience_fee_enabled');
        $paymayaCF = $this->settingEnabled('paymaya_convenience_fee_enabled');

        $totalMembershipFees = (float) $this
        ->membershipFeesForActivation($activation)
        ->sum('Cost');
        $activatedUser = null;

        try {
            DB::transaction(function () use (
                $activation,
                $selectedUserID,
                $type,
                $referralUser,
                $gcashCF,
                $paymayaCF,
                $activationAttributes,
                &$activatedUser
            ) {
                Log::info('========== ACTIVATION APPROVAL START ==========', [
                    'ActivationID' => $activation->ActivationID,
                    'UserID' => $selectedUserID,
                    'type' => $type,
                    'upline_code' => $activation->upline_code,
                    'amount' => $activation->amount,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Lock Activation
                |--------------------------------------------------------------------------
                */
                $activation = Activation::where(
                    'ActivationID',
                    $activation->ActivationID
                )->lockForUpdate()->first();

                if (! $activation || $activation->status !== 'Pending') {
                    throw new \RuntimeException(
                        'Activation is no longer pending.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Approve Activation
                |--------------------------------------------------------------------------
                */
                $activation->update(array_merge(
                    ['status' => 'Approved'],
                    $activationAttributes
                ));

                /*
                |--------------------------------------------------------------------------
                | Approval History
                |--------------------------------------------------------------------------
                */
                $admin = Auth::user();

                $adminName = trim(implode(' ', array_filter([
                    $admin?->firstname,
                    $admin?->middlename,
                    $admin?->lastname,
                ])));

                ActivationHistory::create([
                    'ActivationID' => $activation->ActivationID,
                    'AdminID' => Auth::id(),
                    'status' => 'Approved',
                    'remarks' => "Activation approved by {$adminName}.",
                ]);


                /*
                |--------------------------------------------------------------------------
                | ACTIVATE USER + INHERIT BRANCH CODE
                |--------------------------------------------------------------------------
                | The approved member inherits the branchCode of their direct upline.
                | First, use activation.upline_code to find the upline by referralCode.
                | If no upline is found, fall back to the member's uplineCode or
                | referralCode relationship as appropriate.
                |--------------------------------------------------------------------------
                */

                $approvedMember = User::where('UserID', $selectedUserID)
                    ->lockForUpdate()
                    ->first();

                if (! $approvedMember) {
                    throw new \RuntimeException(
                        "User {$selectedUserID} not found during activation approval."
                    );
                }

                $uplineCode = trim((string) (
                    $activation->upline_code
                    ?: $approvedMember->uplineCode
                ));

                $upline = $uplineCode !== ''
                    ? User::where('referralCode', $uplineCode)->first()
                    : null;

                // Set the member's branchCode to the upline's existing branchCode.
                // If no upline is found, retain the member's current branchCode.
                $approvedMember->update([
                    'status' => 'Active',
                    'branchCode' => $upline?->branchCode ?? $approvedMember->branchCode,
                ]);

                $activatedUser = $approvedMember;


                /*
                |--------------------------------------------------------------------------
                | FREE MEMBER (provisioned by a satellite)
                |--------------------------------------------------------------------------
                | Everything the member owes was already written as Pending
                | per-fee membership-fee ledger rows when the satellite was
                | provisioned — approval flips ALL of them to Completed, then
                | pays the satellite owner the same DR / TX / OV / PF stack
                | as a paid activation.
                */
                if ($type === self::TYPE_FREE || $activation->isFree) {
                    $this->processFreeMemberAccounting(
                        $activation,
                        $referralUser
                    );

                    Log::info('========== FREE MEMBER APPROVAL FINISHED ==========', [
                        'ActivationID' => $activation->ActivationID,
                        'UserID' => $selectedUserID,
                    ]);

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | SATELLITE
                |--------------------------------------------------------------------------
                */
                if ($type === self::TYPE_SATELLITE) {
                    $this->processSatelliteAccounting(
                        $activation,
                        $selectedUserID,
                        $referralUser
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | FREE MEMBERS
                    |--------------------------------------------------------------------------
                    | The satellite is live now, so its free members (Bronze 20,
                    | Silver 40, Gold 80) are created in the SAME transaction — if
                    | provisioning fails the approval rolls back with it.
                    */
                    $provisioned = $this->provisionFreeMembers($activation);

                    Log::info('========== SATELLITE APPROVAL FINISHED ==========', [
                        'ActivationID' => $activation->ActivationID,
                        'free_members_provisioned' => $provisioned,
                    ]);

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | REGULAR - MEMBERSHIP FEES
                |--------------------------------------------------------------------------
                */
                foreach ($this->membershipFeesForActivation($activation)->get() as $fee) {
                    $ledger = Ledger::create([
                        'RefID' => $fee->FeeID,
                        'amount' => $fee->Cost,
                        'Source' => 'MF',
                        'ActivationID' => $activation->ActivationID,
                        'status' => 'Completed',
                    ]);

                    Log::info('MF Ledger created.', [
                        'LedgerID' => $ledger->getKey(),
                        'FeeID' => $fee->FeeID,
                        'amount' => $fee->Cost,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | CONVENIENCE FEE
                |--------------------------------------------------------------------------
                */
                $paymentMethod = strtolower(
                    trim((string) $activation->payment_method)
                );

                if (
                    ($paymentMethod === 'gcash' && $gcashCF) ||
                    ($paymentMethod === 'paymaya' && $paymayaCF)
                ) {
                    $cfAmount = round(
                        $this->membershipFeesForActivation($activation)->sum('Cost') * 0.01,
                        2
                    );

                    Ledger::create([
                        'RefID' => $selectedUserID,
                        'amount' => $cfAmount,
                        'Source' => 'CF',
                        'ActivationID' => $activation->ActivationID,
                        'status' => 'Completed',
                    ]);

                    Log::info('CF Ledger created.', [
                        'ActivationID' => $activation->ActivationID,
                        'amount' => $cfAmount,
                        'payment_method' => $paymentMethod,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | PF
                |--------------------------------------------------------------------------
                */
                if ($referralUser) {
                    $this->createWalletLedger(
                        $referralUser->UserID,
                        $activation->ActivationID,
                        50.00,
                        'PF',
                        -50.00
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | DR + OV
                |--------------------------------------------------------------------------
                */
                $this->processUplineCommissions(
                    $activation,
                    $referralUser
                );

                Log::info('========== REGULAR APPROVAL FINISHED ==========', [
                    'ActivationID' => $activation->ActivationID,
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('========== ACTIVATION APPROVAL FAILED ==========', [
                'ActivationID' => $activation->ActivationID ?? null,
                'UserID' => $selectedUserID,
                'type' => $type,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | EMAILS
        |--------------------------------------------------------------------------
        */
        if ($activatedUser?->email) {
            try {
                Mail::to($activatedUser->email)->send(
                    new ActivationApprovedMail(
                        $activatedUser,
                        $activation,
                        $totalMembershipFees
                    )
                );
            } catch (\Throwable $e) {
                Log::error('Failed to send member approval email.', [
                    'email' => $activatedUser->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($referralUser?->email && $activatedUser) {
            try {
                Mail::to($referralUser->email)->send(
                    new InviteeApprovedMail(
                        $referralUser,
                        $activatedUser,
                        $activation
                    )
                );
            } catch (\Throwable $e) {
                Log::error('Failed to send upline approval email.', [
                    'email' => $referralUser->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | FREE MEMBER PROVISIONING
    |--------------------------------------------------------------------------
    | Called from the satellite approval above: creates the free members
    | (RoleID 4) that come with a satellite membership.
    |
    | Every free member only gets:
    |
    |   - a user whose firstname / lastname / username / password are the
    |     satellite_name with its spaces removed + -firstname-N, -username-N,
    |     -lastname-N and -password-N, plus its OWN referral code so
    |     invites made with that code walk into this satellite owner's
    |     OV chain (level 2 = 50 down to level 6 = 10)
    |   - its own activation (isFree = true, status Pending) carrying the
    |     satelliteID of the satellite it belongs to
    |   - ONE Pending membership-fee ledger row PER membership fee
    |     (RefID = FeeID, amount = Cost) — the same per-fee breakdown a
    |     regular approval writes, not one combined row
    |
    | The activation's upline_code is the satellite owner's referral code, so
    | approving a free member pays DR / TX / OV / PF to that satellite owner
    | exactly like a normal activation.
    |
    | Returns the number of members created, or 0 when there is nothing to
    | provision (no satellite row, unknown tier, or already provisioned).
    |--------------------------------------------------------------------------
    */
    public function provisionFreeMembers(Activation $satelliteActivation): int
    {
        // Also lifted here so direct callers of provisioning get the same
        // "wait until every free member exists" behaviour as approve().
        $this->removeExecutionLimits();

        $satelliteID = (int) ($satelliteActivation->satelliteID ?? 0);

        if ($satelliteID <= 0) {
            return 0;
        }

        $satellite = Satellite::find($satelliteID);

        if (! $satellite) {
            Log::warning('Free member provisioning skipped: satellite not found.', [
                'ActivationID' => $satelliteActivation->ActivationID,
                'satelliteID' => $satelliteID,
            ]);

            return 0;
        }

        // Idempotent: a re-approval of the same satellite (REAPPLY) must not
        // create a second set of free members.
        $alreadyProvisioned = Activation::query()
            ->where('satelliteID', $satelliteID)
            ->where('isFree', true)
            ->exists();

        if ($alreadyProvisioned) {
            Log::info('Free member provisioning skipped: already provisioned.', [
                'ActivationID' => $satelliteActivation->ActivationID,
                'satelliteID' => $satelliteID,
            ]);

            return 0;
        }

        $tier = config('satellite.tier_by_type')[(int) $satellite->satellite_typeID] ?? null;
        $slots = $tier ? (int) (config('satellite.free_slots')[$tier] ?? 0) : 0;

        if ($slots < 1) {
            Log::warning('Free member provisioning skipped: tier not configured.', [
                'ActivationID' => $satelliteActivation->ActivationID,
                'satellite_typeID' => $satellite->satellite_typeID,
                'tier' => $tier,
            ]);

            return 0;
        }

        $prefix = $this->freeMemberPrefix($satellite);

        // Every free member hangs off the satellite owner, which is what
        // earns that owner the DR / PF share on each approval.
        $owner = User::find($satellite->UserID);
        $uplineCode = trim((string) $owner?->referralCode);

        $memberRoleID = (int) (
            DB::table('roles')
                ->where('rolename', 'Member')
                ->value('RoleID')
            ?? 4
        );

        $totalMembershipFees = (float) $this
        ->membershipFeesForActivation($satelliteActivation)
        ->sum('Cost');

        return DB::transaction(function () use (
            $satellite,
            $satelliteID,
            $slots,
            $prefix,
            $uplineCode,
            $memberRoleID,
            $totalMembershipFees,
            $owner
        ) {
            $provisioned = 0;

            for ($n = 1; $n <= $slots; $n++) {
                $user = User::create([
                    'username' => "{$prefix}-username-{$n}",
                    'password' => "{$prefix}-password-{$n}",
                    'firstname' => "{$prefix}-firstname-{$n}",
                    'lastname' => "{$prefix}-lastname-{$n}",
                    'referralCode' => $this->uniqueReferralCode(),
                    'uplineCode' => $uplineCode === '' ? null : $uplineCode,
                    'RoleID' => $memberRoleID,
                    'status' => 'Inactive',
                ]);

                $freeActivation = Activation::create([
                    'UserID' => $user->UserID,
                    'satelliteID' => $satelliteID,
                    'VID' => $satelliteActivation->VID ?? 1,
                    'amount' => $totalMembershipFees,
                    'type' => 'W',
                    'payment_method' => 'Cash',
                    'upline_code' => $uplineCode,
                    'ProcessedBy' => Auth::id(),
                    'status' => 'Pending',
                    'isFree' => true,
                ]);

                // Per-fee Pending rows (RefID = FeeID, amount = Cost);
                // ALL of them flip to Completed when this free member is
                // approved — processFreeMemberAccounting() updates every
                // Pending MF row of this activation.
                $this->createPendingFreeMemberBreakdown(
                    $freeActivation,
                    $owner
                );

                $provisioned++;
            }

            Log::info('Free members provisioned.', [
                'satellite_name' => $satellite->satellite_name,
                'tier' => config('satellite.tier_by_type')[(int) $satellite->satellite_typeID] ?? null,
                'prefix' => $prefix,
                'provisioned' => $provisioned,
            ]);

            return $provisioned;
        });
    }

    private function createPendingFreeMemberBreakdown(
        Activation $activation,
        ?User $referralUser
    ): void {

        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP FEES
        |--------------------------------------------------------------------------
        */

        $membershipFees = $this
            ->membershipFeesForActivation($activation)
            ->get(['FeeID', 'Cost']);

        foreach ($membershipFees as $fee) {

            Ledger::create([
                'RefID' => $fee->FeeID,
                'amount' => $fee->Cost,
                'Source' => 'MF',
                'ActivationID' => $activation->ActivationID,
                'status' => 'Pending',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | FIND SATELLITE OWNER
        |--------------------------------------------------------------------------
        |
        | For a free member, $referralUser is the satellite owner.
        |
        */

        if (! $referralUser) {

            Log::warning(
                'Free member pending breakdown created without referral owner.',
                [
                    'ActivationID' => $activation->ActivationID,
                    'upline_code' => $activation->upline_code,
                ]
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | SDR
        |--------------------------------------------------------------------------
        |
        | The satellite owner receives SDR based on the satellite tier.
        |
        | Bronze = 50
        | Silver = 75
        | Gold   = 100
        |
        | SDR is Pending until this free member is approved.
        |
        */

        $satellite = Satellite::find(
            (int) $activation->satelliteID
        );

        if ($satellite) {

            $tier = config('satellite.tier_by_type')[
                (int) $satellite->satellite_typeID
            ] ?? null;

            $sdrAmount = $tier
                ? (float) (
                    config('satellite.sdr_rates')[$tier] ?? 0
                )
                : 0;

            if ($sdrAmount > 0) {

                Ledger::create([
                    'RefID' => $referralUser->UserID,
                    'amount' => -$sdrAmount,
                    'Source' => 'SDR',
                    'ActivationID' => $activation->ActivationID,
                    'status' => 'Pending',
                ]);

                Log::info('Pending SDR ledger created for free member.', [
                    'ActivationID' => $activation->ActivationID,
                    'satelliteID' => $satellite->satelliteID,
                    'satellite_owner_UserID' => $referralUser->UserID,
                    'satellite_typeID' => $satellite->satellite_typeID,
                    'tier' => $tier,
                    'SDR' => $sdrAmount,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | DR
        |--------------------------------------------------------------------------
        |
        | 400 × 90% = 360
        |
        */

        $drFee = (float) (
            $this->membershipFeesForActivation($activation)
                ->where('FeeName', 'Direct Referral')
                ->value('Cost') ?? 400.00
        );

        $drAmount = round($drFee * 0.90, 2);
        $txAmount = round($drFee * 0.10, 2);

        if ($drAmount > 0) {

            Ledger::create([
                'RefID' => $referralUser->UserID,
                'amount' => -$drAmount,
                'Source' => 'DR',
                'ActivationID' => $activation->ActivationID,
                'status' => 'Pending',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | TX
        |--------------------------------------------------------------------------
        */

        if ($txAmount > 0) {

            Ledger::create([
                'RefID' => $referralUser->UserID,
                'amount' => $txAmount,
                'Source' => 'TX',
                'ActivationID' => $activation->ActivationID,
                'status' => 'Pending',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | PF
        |--------------------------------------------------------------------------
        */

        Ledger::create([
            'RefID' => $referralUser->UserID,
            'amount' => -50.00,
            'Source' => 'PF',
            'ActivationID' => $activation->ActivationID,
            'status' => 'Pending',
        ]);

        /*
        |--------------------------------------------------------------------------
        | OV LEVEL 2 → 6
        |--------------------------------------------------------------------------
        */

        $this->createPendingFreeMemberOV(
            $activation,
            $referralUser
        );

        Log::info('Free member pending financial breakdown created.', [
            'ActivationID' => $activation->ActivationID,
            'referralUserID' => $referralUser->UserID,
            'DR' => $drAmount,
            'TX' => $txAmount,
            'PF' => 50.00,
        ]);
    }

    private function createPendingFreeMemberOV(
        Activation $activation,
        User $referralUser
    ): void {
        $code = trim((string) $referralUser->uplineCode);
        $level = 2;

        while ($code !== '' && $level <= 6) {
            $user = User::where('referralCode', $code)->first();

            if (! $user) {
                Log::warning('Free member pending OV upline not found.', [
                    'ActivationID' => $activation->ActivationID,
                    'level' => $level,
                    'search_referralCode' => $code,
                ]);

                break;
            }


            $ovAmount = self::OV_RATES[$level] ?? 0;

            $netOV = round($ovAmount * 0.90, 2);
            $txAmount = round($ovAmount * 0.10, 2);

            if ($ovAmount > 0) {
                // OV ledger: deduct 10% from the original OV amount.
                Ledger::create([
                    'RefID' => $user->UserID,
                    'amount' => -$netOV,
                    'Source' => 'OV',
                    'ActivationID' => $activation->ActivationID,
                    'status' => 'Pending',
                ]);

                // TX ledger: the same upline receives the separate TX entry.
                if ($txAmount > 0) {
                    Ledger::create([
                        'RefID' => $user->UserID,
                        'amount' => $txAmount,
                        'Source' => 'TX',
                        'ActivationID' => $activation->ActivationID,
                        'status' => 'Pending',
                    ]);
                }

                Log::info('Pending free member OV and TX ledgers created.', [
                    'ActivationID' => $activation->ActivationID,
                    'UserID' => $user->UserID,
                    'level' => $level,
                    'original_OV' => $ovAmount,
                    'net_OV' => $netOV,
                    'TX' => $txAmount,
                ]);
            }

    /*
        |--------------------------------------------------------------------------
        | TX
        |--------------------------------------------------------------------------
        */

        if ($txAmount > 0) {

            Ledger::create([
                'RefID' => $referralUser->UserID,
                'amount' => $txAmount,
                'Source' => 'TX',
                'ActivationID' => $activation->ActivationID,
                'status' => 'Pending',
            ]);
        }

            $code = trim((string) $user->uplineCode);
            $level++;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EXECUTION LIMITS
    |--------------------------------------------------------------------------
    | Free-member provisioning for Silver (40) / Gold (80) takes longer than
    | max_execution_time (30s by default): bcrypt hashing alone costs ~16s /
    | ~32s at cost 12, plus the per-fee ledger rows and the approval emails
    | over SMTP. Killing the request mid-run leaves the satellite approved
    | with only part of its members created, so the limit is lifted for the
    | whole approval and the script is kept alive even when the client
    | disconnects (ignore_user_abort) until every member is written.
    |--------------------------------------------------------------------------
    */
    private function removeExecutionLimits(): void
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

        ignore_user_abort(true);
    }

    /*
    |--------------------------------------------------------------------------
    | UNIQUE REFERRAL CODE
    |--------------------------------------------------------------------------
    | Every free member gets their OWN referral code (same 6-character style
    | SatelliteModal / AdminUsers use). Invites made with that code resolve
    | to the free member at level 1, then walk into the satellite owner's
    | chain — level 2 = 50, 3 = 40, 4 = 30, 5 = 20, 6 = 10 — through the
    | normal OV logic.
    |--------------------------------------------------------------------------
    */
    private function uniqueReferralCode(): string
    {
        do {
            $code = strtoupper(substr(
                md5(uniqid((string) mt_rand(), true)),
                0,
                6
            ));
        } while (User::where('referralCode', $code)->exists());

        return $code;
    }

    /*
    |--------------------------------------------------------------------------
    | FREE MEMBER NAME PREFIX
    |--------------------------------------------------------------------------
    | "Satellite Gold" → "SatelliteGold", so the members are named
    | SatelliteGold-username-1, SatelliteGold-firstname-1, ...
    |
    | usernames are unique: when two satellites share the same name the
    | second one gets a satelliteID suffix instead of failing the whole
    | approval with a duplicate-key error.
    |--------------------------------------------------------------------------
    */
    private function freeMemberPrefix(Satellite $satellite): string
    {
        $prefix = preg_replace('/\s+/', '', (string) $satellite->satellite_name)
            ?: 'Satellite';

        $taken = User::query()
            ->where('username', $prefix.'-username-1')
            ->exists();

        if ($taken) {
            $prefix .= (int) $satellite->satelliteID;
        }

        return $prefix;
    }

    /*
    |--------------------------------------------------------------------------
    | FREE MEMBER ACCOUNTING
    |--------------------------------------------------------------------------
    | Approval of a free member: complete every Pending per-fee
    | membership-fee row written at provisioning, then pay the satellite
    | owner the normal stack (PF 50, DR 90 / TX 10 of the Direct Referral
    | fee, OV walk to level 6).
    |--------------------------------------------------------------------------
    */
    private function processFreeMemberAccounting(
        Activation $activation,
        ?User $referralUser
    ): void {
        /*
        |--------------------------------------------------------------------------
        | COMPLETE ALL PENDING LEDGERS
        |--------------------------------------------------------------------------
        |
        | The complete financial breakdown was already created when the
        | satellite was approved.
        |
        | We only change Pending → Completed here.
        |
        */

        $completed = Ledger::query()
            ->where('ActivationID', $activation->ActivationID)
            ->where('status', 'Pending')
            ->update([
                'status' => 'Completed',
            ]);

        Log::info('Free member pending ledgers completed.', [
            'ActivationID' => $activation->ActivationID,
            'rows' => $completed,
        ]);

        /*
        |--------------------------------------------------------------------------
        | FIND REFERRAL OWNER
        |--------------------------------------------------------------------------
        */

        if (! $referralUser) {
            Log::warning('Free member approved without referral owner.', [
                'ActivationID' => $activation->ActivationID,
                'upline_code' => $activation->upline_code,
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE WALLET ENTRIES
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | Wallets are created ONLY NOW.
        |
        | The ledger rows already existed as Pending.
        |
        */

        /*
        |--------------------------------------------------------------------------
        | DR
        |--------------------------------------------------------------------------
        */

        $drLedger = Ledger::query()
            ->where('ActivationID', $activation->ActivationID)
            ->where('Source', 'DR')
            ->where('status', 'Completed')
            ->first();

        if ($drLedger) {
            $walletAmount = abs((float) $drLedger->amount);

            if ($walletAmount > 0) {
                $this->createWalletFromLedger(
                    $drLedger,
                    $referralUser->UserID,
                    $walletAmount
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SDR
        |--------------------------------------------------------------------------
        |
        | SDR was created as Pending when the free member was provisioned.
        | Once the free member is approved, the Pending SDR becomes Completed
        | and the satellite owner receives the SDR amount in their wallet.
        |
        */

        $sdrLedger = Ledger::query()
            ->where('ActivationID', $activation->ActivationID)
            ->where('Source', 'SDR')
            ->where('status', 'Completed')
            ->first();

        if ($sdrLedger) {
            $walletAmount = abs((float) $sdrLedger->amount);

            if ($walletAmount > 0) {
                $this->createWalletFromLedger(
                    $sdrLedger,
                    (int) $sdrLedger->RefID,
                    $walletAmount
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PF
        |--------------------------------------------------------------------------
        */

        $pfLedger = Ledger::query()
            ->where('ActivationID', $activation->ActivationID)
            ->where('Source', 'PF')
            ->where('status', 'Completed')
            ->first();

        if ($pfLedger) {
            $walletAmount = abs((float) $pfLedger->amount);

            if ($walletAmount > 0) {
                $this->createWalletFromLedger(
                    $pfLedger,
                    $referralUser->UserID,
                    $walletAmount
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | OV
        |--------------------------------------------------------------------------
        |
        | OV ledger rows contain negative amounts.
        |
        | Example:
        |
        | Ledger = -50
        | Wallet  = +50
        |
        */

        $ovLedgers = Ledger::query()
            ->where('ActivationID', $activation->ActivationID)
            ->where('Source', 'OV')
            ->where('status', 'Completed')
            ->get();

        foreach ($ovLedgers as $ovLedger) {
            $walletAmount = abs((float) $ovLedger->amount);

            if ($walletAmount <= 0) {
                continue;
            }

            $this->createWalletFromLedger(
                $ovLedger,
                (int) $ovLedger->RefID,
                $walletAmount
            );
        }

        /*
        |--------------------------------------------------------------------------
        | TX
        |--------------------------------------------------------------------------
        |
        | TX remains ledger-only, exactly as your current implementation.
        |
        */

        Log::info('Free member accounting finalized.', [
            'ActivationID' => $activation->ActivationID,
            'DR' => $drLedger?->amount,
            'PF' => $pfLedger?->amount,
            'OV_count' => $ovLedgers->count(),
        ]);
    }

    private function createWalletFromLedger(
        Ledger $ledger,
        int $userID,
        ?float $walletAmount = null
    ): void {
        $walletAmount ??= abs((float) $ledger->amount);

        if ($walletAmount <= 0) {
            return;
        }

        $wallet = Wallet::create([
            'UserID' => $userID,
            'ActivationID' => $ledger->ActivationID,
            'amount' => $walletAmount,
            'source' => $ledger->Source,
            'status' => 'Active',
        ]);

        Log::info('Wallet created from completed ledger.', [
            'WalletID' => $wallet->getKey(),
            'LedgerID' => $ledger->getKey(),
            'UserID' => $userID,
            'ActivationID' => $ledger->ActivationID,
            'Source' => $ledger->Source,
            'amount' => $walletAmount,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FIND DIRECT REFERRAL
    |--------------------------------------------------------------------------
    */
    private function findReferralUser(Activation $activation): ?User
    {
        $code = trim((string) $activation->upline_code);

        if ($code === '') {
            Log::warning('No activation upline_code.', [
                'ActivationID' => $activation->ActivationID,
            ]);

            return null;
        }

        $user = User::where('referralCode', $code)->first();

        Log::info('Direct referral lookup.', [
            'ActivationID' => $activation->ActivationID,
            'upline_code' => $code,
            'found' => (bool) $user,
            'UserID' => $user?->UserID,
            'referralCode' => $user?->referralCode,
            'uplineCode' => $user?->uplineCode,
        ]);

        return $user;
    }

    /*
    |--------------------------------------------------------------------------
    | REGULAR + SATELLITE UPLINE COMMISSIONS
    |--------------------------------------------------------------------------
    */
    private function processUplineCommissions(
        Activation $activation,
        ?User $referralUser
    ): void {
        $drFee = (float) (
            $this->membershipFeesForActivation($activation)
                ->where('FeeName', 'Direct Referral')
                ->value('Cost') ?? 400.00
        );

        $drAmount = round($drFee * 0.90, 2);
        $txAmount = round($drFee * 0.10, 2);

        $code = trim((string) $activation->upline_code);
        $level = 1;

        Log::info('========== UPLINE TRAVERSAL START ==========', [
            'ActivationID' => $activation->ActivationID,
            'starting_upline_code' => $code,
            'DR' => $drAmount,
            'TX' => $txAmount,
        ]);

        while ($code !== '' && $level <= 6) {

            Log::info('Upline level lookup.', [
                'ActivationID' => $activation->ActivationID,
                'level' => $level,
                'search_referralCode' => $code,
            ]);

            $user = User::where('referralCode', $code)->first();

            if (! $user) {
                Log::warning('Upline NOT FOUND - traversal stopped.', [
                    'ActivationID' => $activation->ActivationID,
                    'level' => $level,
                    'search_referralCode' => $code,
                ]);

                break;
            }

            Log::info('Upline FOUND.', [
                'ActivationID' => $activation->ActivationID,
                'level' => $level,
                'UserID' => $user->UserID,
                'referralCode' => $user->referralCode,
                'uplineCode' => $user->uplineCode,
            ]);

            /*
            |--------------------------------------------------------------------------
            | LEVEL 1 = DR + TX
            |--------------------------------------------------------------------------
            */
            if ($level === 1) {

                Log::info('Processing Level 1 DR/TX.', [
                    'ActivationID' => $activation->ActivationID,
                    'UserID' => $user->UserID,
                    'DR' => $drAmount,
                    'TX' => $txAmount,
                ]);

                if ($drAmount > 0) {
                    $this->createWalletLedger(
                        $user->UserID,
                        $activation->ActivationID,
                        $drAmount,
                        'DR',
                        -$drAmount
                    );
                }

                if ($txAmount > 0) {
                    $ledger = Ledger::create([
                        'RefID' => $user->UserID,
                        'amount' => $txAmount,
                        'Source' => 'TX',
                        'ActivationID' => $activation->ActivationID,
                        'status' => 'Completed',
                    ]);

                    Log::info('TX Ledger created.', [
                        'LedgerID' => $ledger->getKey(),
                        'UserID' => $user->UserID,
                        'amount' => $txAmount,
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | LEVEL 2-6 = OV
            |--------------------------------------------------------------------------
            */
            if ($level >= 2) {

                $ovAmount = self::OV_RATES[$level] ?? 0;

                Log::info('OV LEVEL DETECTED.', [
                    'ActivationID' => $activation->ActivationID,
                    'level' => $level,
                    'UserID' => $user->UserID,
                    'referralCode' => $user->referralCode,
                    'uplineCode' => $user->uplineCode,
                    'OV' => $ovAmount,
                ]);

                if ($ovAmount <= 0) {
                    Log::warning('OV amount is zero.', [
                        'ActivationID' => $activation->ActivationID,
                        'level' => $level,
                    ]);
                } else {
                    $this->createOV(
                        $user->UserID,
                        $activation->ActivationID,
                        $level,
                        $ovAmount
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | NEXT UPLINE
            |--------------------------------------------------------------------------
            */
            $nextCode = trim((string) $user->uplineCode);

            Log::info('Moving to next upline.', [
                'ActivationID' => $activation->ActivationID,
                'current_level' => $level,
                'current_UserID' => $user->UserID,
                'current_referralCode' => $user->referralCode,
                'next_upline_code' => $nextCode,
            ]);

            $code = $nextCode;
            $level++;
        }

        if ($level > 6) {
            Log::info('OV traversal reached maximum Level 6.', [
                'ActivationID' => $activation->ActivationID,
            ]);
        }

        Log::info('========== UPLINE TRAVERSAL END ==========', [
            'ActivationID' => $activation->ActivationID,
            'last_level' => $level - 1,
            'remaining_code' => $code,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE OV WALLET + LEDGER
    |--------------------------------------------------------------------------
    */

    private function createOV(
        int $userID,
        int $activationID,
        int $level,
        float $amount
    ): void {
        // Deduct 10% TX from the original OV commission.
        $ovAmount = round($amount * 0.90, 2);
        $txAmount = round($amount * 0.10, 2);

        // OV wallet receives only 90%.
        if ($ovAmount > 0) {
            Wallet::create([
                'UserID' => $userID,
                'ActivationID' => $activationID,
                'amount' => $ovAmount,
                'source' => 'OV',
                'status' => 'Active',
            ]);

            // Negative ledger amount follows the existing OV convention.
            Ledger::create([
                'RefID' => $userID,
                'amount' => -$ovAmount,
                'Source' => 'OV',
                'ActivationID' => $activationID,
                'status' => 'Completed',
            ]);
        }

        // Record the deducted 10% as a separate TX ledger entry.
        if ($txAmount > 0) {
            Ledger::create([
                'RefID' => $userID,
                'amount' => $txAmount,
                'Source' => 'TX',
                'ActivationID' => $activationID,
                'status' => 'Completed',
            ]);
        }

        Log::info('OV and TX ledgers created.', [
            'UserID' => $userID,
            'ActivationID' => $activationID,
            'level' => $level,
            'original_OV' => $amount,
            'net_OV' => $ovAmount,
            'TX' => $txAmount,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | WALLET + LEDGER HELPER
    |--------------------------------------------------------------------------
    */
    private function createWalletLedger(
        int $userID,
        int $activationID,
        float $walletAmount,
        string $source,
        float $ledgerAmount
    ): void {
        $wallet = Wallet::create([
            'UserID' => $userID,
            'ActivationID' => $activationID,
            'amount' => $walletAmount,
            'source' => $source,
            'status' => 'Active',
        ]);

        Log::info('Wallet created.', [
            'WalletID' => $wallet->getKey(),
            'UserID' => $userID,
            'ActivationID' => $activationID,
            'source' => $source,
            'amount' => $walletAmount,
        ]);

        $ledger = Ledger::create([
            'RefID' => $userID,
            'amount' => $ledgerAmount,
            'Source' => $source,
            'ActivationID' => $activationID,
            'status' => 'Completed',
        ]);

        Log::info('Ledger created.', [
            'LedgerID' => $ledger->getKey(),
            'RefID' => $userID,
            'ActivationID' => $activationID,
            'Source' => $source,
            'amount' => $ledgerAmount,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SATELLITE ACCOUNTING
    |--------------------------------------------------------------------------
    */
    private function processSatelliteAccounting(
        Activation $activation,
        int $selectedUserID,
        ?User $referralUser
    ): void {
        $activationAmount = (float) $activation->amount;

        $sfAmount = round($activationAmount * 0.90, 2);
        $csAmount = round($activationAmount * 0.10, 2);

        Log::info('Satellite accounting.', [
            'ActivationID' => $activation->ActivationID,
            'activationAmount' => $activationAmount,
            'SF' => $sfAmount,
            'CS' => $csAmount,
        ]);

        /*
        |--------------------------------------------------------------------------
        | SF
        |--------------------------------------------------------------------------
        */
        if ($sfAmount > 0) {
            Ledger::create([
                'RefID' => $selectedUserID,
                'amount' => $sfAmount,
                'Source' => 'SF',
                'ActivationID' => $activation->ActivationID,
                'status' => 'Completed',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CS
        |--------------------------------------------------------------------------
        */
        if ($csAmount > 0) {
            Ledger::create([
                'RefID' => $selectedUserID,
                'amount' => -$csAmount,
                'Source' => 'CS',
                'ActivationID' => $activation->ActivationID,
                'status' => 'Completed',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | UPLINE DR / TX / OV / PF
        |--------------------------------------------------------------------------
        */
        if ($referralUser) {

            /*
            |--------------------------------------------------------------------------
            | CS WALLET
            |--------------------------------------------------------------------------
            */
            if ($csAmount > 0) {
                Wallet::create([
                    'UserID' => $referralUser->UserID,
                    'ActivationID' => $activation->ActivationID,
                    'amount' => $csAmount,
                    'source' => 'CS',
                    'status' => 'Active',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | DR + TX + OV
            |--------------------------------------------------------------------------
            */
            $this->processUplineCommissions(
                $activation,
                $referralUser
            );

            /*
            |--------------------------------------------------------------------------
            | PF
            |--------------------------------------------------------------------------
            */
            $this->createWalletLedger(
                $referralUser->UserID,
                $activation->ActivationID,
                50.00,
                'PF',
                -50.00
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SETTING
    |--------------------------------------------------------------------------
    */
    private function settingEnabled(string $name): bool
    {
        return filter_var(
            Setting::where('name', $name)->value('value'),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    */
    public function reject(
        Activation $activation,
        int $selectedUserID,
        string $remarks
    ): void {
        DB::transaction(function () use (
            $activation,
            $selectedUserID,
            $remarks
        ) {
            $activation->update([
                'status' => 'Rejected',
            ]);

            ActivationHistory::create([
                'ActivationID' => $activation->ActivationID,
                'AdminID' => Auth::id(),
                'status' => 'Rejected',
                'remarks' => $remarks,
            ]);

            User::where('UserID', $selectedUserID)->update([
                'status' => 'Inactive',
            ]);
        });

        $user = User::find($selectedUserID);

        if ($user?->email) {
            $beneficiaries = Beneficiary::where('UserID', $selectedUserID)
                ->where('status', true)
                ->orderBy('BeneficiaryID')
                ->get()
                ->toArray();

            try {
                Mail::to($user->email)->send(
                    new ActivationRejectedMail(
                        $user,
                        $activation,
                        $beneficiaries,
                        $remarks
                    )
                );
            } catch (\Throwable $e) {
                Log::error('Failed to send rejection email.', [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Get membership fees for the activation's variant.
     */
    private function membershipFeesForActivation(Activation $activation)
    {
        $vid = $activation->VID ?? 1;

        return MembershipFee::where('VID', $vid);
    }

}
