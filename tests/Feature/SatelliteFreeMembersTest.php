<?php

namespace Tests\Feature;

use App\Livewire\AdminPages\Masterlist\SatelliteMembers;
use App\Livewire\AdminPages\Masterlist\SatelliteMembersComponents\FreeMembersModal;
use App\Models\Activation;
use App\Models\Ledger;
use App\Models\MembershipFee;
use App\Models\Satellite;
use App\Models\User;
use App\Models\Wallet;
use App\Services\ActivationApprovalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Free members provisioned under a satellite membership.
 *
 * A satellite application (SatelliteModal) creates a RoleID 8 user + a
 * Satellite row + a Pending activation carrying the satelliteID. Approving
 * that activation provisions the free members of the applied tier
 * (Bronze 20 / Silver 40 / Gold 80), and approving a free member completes
 * its Pending membership-fee ledger and pays the satellite owner.
 */
class SatelliteFreeMembersTest extends TestCase
{
    use RefreshDatabase;

    private ActivationApprovalService $service;

    private int $uplineSeq = 0;

    protected function setUp(): void
    {
        parent::setUp();

        /*
        | .env sets APP_URL=localhost (no scheme), so the app's base request
        | boots with a doubled root (http://localhost/localhost) and every
        | Livewire::test() endpoint resolves to /localhost/... and 404s —
        | coming back as a redirect instead of a snapshot. Pin a canonical
        | root URL for these tests only.
        */
        config(['app.url' => 'http://localhost']);
        URL::forceRootUrl('http://localhost');

        // The users.RoleID foreign key needs its targets in place.
        DB::table('roles')->insert([
            ['RoleID' => 1, 'rolename' => 'Super Administrator', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['RoleID' => 4, 'rolename' => 'Member', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['RoleID' => 8, 'rolename' => 'Satellite', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 450 + 400 + 50 = 900 total membership fees.
        DB::table('membership_fees')->insert([
            ['FeeID' => 'FEE001', 'FeeName' => 'Insurance', 'Cost' => 450.00, 'created_at' => now(), 'updated_at' => now()],
            ['FeeID' => 'FEE002', 'FeeName' => 'Direct Referral', 'Cost' => 400.00, 'created_at' => now(), 'updated_at' => now()],
            ['FeeID' => 'FEE003', 'FeeName' => 'Provident Fund', 'Cost' => 50.00, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->service = app(ActivationApprovalService::class);
    }

    // ------------------------------------------------------------------
    // PROVISIONING
    // ------------------------------------------------------------------

    public function test_approving_a_satellite_provisions_free_members_named_after_the_satellite(): void
    {
        $this->actingAs($this->admin());

        [$owner, $satellite, $activation] = $this->makeSatellite(8, 'Satellite Gold', 'SATGOLD');

        $this->assertTrue(
            $this->service->approve($activation, $owner->UserID, ActivationApprovalService::TYPE_SATELLITE),
            'Satellite approval should succeed.'
        );

        // Gold = 80 free members, all still Pending.
        $freeActivations = Activation::query()
            ->where('satelliteID', $satellite->satelliteID)
            ->where('isFree', true)
            ->get();

        $this->assertCount(80, $freeActivations);
        $this->assertSame(0, $freeActivations->where('status', '!=', 'Pending')->count());

        // RoleID 4 = true life member, provisioned but not yet Active.
        $freeUsers = User::query()
            ->where('RoleID', 4)
            ->where('username', 'like', 'SatelliteGold-username-%')
            ->get();

        $this->assertCount(80, $freeUsers);
        $this->assertSame(0, $freeUsers->where('status', '!=', 'Inactive')->count());

        // Naming: satellite_name without its spaces + -firstname-N /
        // -username-N / -lastname-N / -password-N.
        $first = User::where('username', 'SatelliteGold-username-1')->first();

        $this->assertNotNull($first);
        $this->assertSame('SatelliteGold-firstname-1', $first->firstname);
        $this->assertSame('SatelliteGold-lastname-1', $first->lastname);
        $this->assertTrue(Hash::check('SatelliteGold-password-1', $first->password));
        $this->assertSame('Inactive', $first->status);
        $this->assertNull($first->email);

        // They hang off the satellite owner, which is what pays that owner.
        $this->assertSame('SATGOLD', $first->uplineCode);

        $firstActivation = $first->activations()->first();
        $this->assertNotNull($firstActivation);
        $this->assertTrue($firstActivation->isFree);
        $this->assertSame($satellite->satelliteID, (int) $firstActivation->satelliteID);

        // Last member of the tier too.
        $this->assertNotNull(User::where('username', 'SatelliteGold-username-80')->first());

        // Each free member gets the same per-fee breakdown a regular
        // approval writes: ONE Pending MF row per membership fee
        // (RefID = FeeID, amount = Cost), summing to the total fees.
        $totalFees = (float) MembershipFee::sum('Cost');
        $this->assertSame(900.00, $totalFees);

        $feeRows = MembershipFee::orderBy('FeeID')->get();

        foreach ($freeActivations->take(3) as $freeActivation) {
            $ledgers = Ledger::where('ActivationID', $freeActivation->ActivationID)
                ->where('Source', 'MF')
                ->orderBy('RefID')
                ->get();

            $this->assertCount(3, $ledgers);

            foreach ($ledgers as $index => $ledger) {
                $this->assertSame($feeRows[$index]->FeeID, $ledger->RefID);
                $this->assertSame('Pending', $ledger->status);
                $this->assertSame((float) $feeRows[$index]->Cost, (float) $ledger->amount);
            }

            $this->assertSame($totalFees, (float) $ledgers->sum('amount'));
        }

        // Every free member carries their OWN referral code, so invites
        // made with it resolve to the free member first and then walk into
        // the satellite owner's OV chain (50 → 10).
        $referralCodes = $freeUsers->pluck('referralCode');

        $this->assertSame(80, $referralCodes->filter()->count(), 'Every free member needs a referral code.');
        $this->assertSame(80, $referralCodes->unique()->count(), 'Referral codes must be unique.');
    }

    public function test_free_member_count_follows_the_applied_satellite_tier(): void
    {
        foreach ([6 => 20, 7 => 40, 8 => 80] as $typeID => $expected) {
            $name = "Tier {$typeID} Satellite";
            $prefix = str_replace(' ', '', $name);

            [, $satellite, $activation] = $this->makeSatellite(
                $typeID,
                $name,
                'SAT'.$typeID
            );

            $this->assertSame(
                $expected,
                $this->service->provisionFreeMembers($activation),
                "Tier {$typeID} should provision {$expected} free members."
            );

            $this->assertSame(
                $expected,
                Activation::where('satelliteID', $satellite->satelliteID)
                    ->where('isFree', true)
                    ->count()
            );

            $this->assertSame(
                $expected,
                User::where('RoleID', 4)
                    ->where('username', 'like', $prefix.'-username-%')
                    ->count()
            );
        }
    }

    public function test_provisioning_never_duplicates_an_already_provisioned_satellite(): void
    {
        [, , $activation] = $this->makeSatellite(6, 'Bronze Satellite', 'SATBRZ');

        $this->assertSame(20, $this->service->provisionFreeMembers($activation));

        // A REAPPROVAL (reapply of the same satellite) must not create a
        // second set of free members.
        $this->assertSame(0, $this->service->provisionFreeMembers($activation));
        $this->assertSame(20, Activation::where('isFree', true)->count());
        $this->assertSame(20, User::where('RoleID', 4)->count());
    }

    public function test_activations_without_a_satellite_provision_nothing(): void
    {
        $member = $this->plainMember('Regular', 'Member', 'REG001');

        $activation = Activation::create([
            'UserID' => $member->UserID,
            'amount' => 900.00,
            'type' => 'W',
            'payment_method' => 'Cash',
            'upline_code' => '',
            'status' => 'Pending',
        ]);

        $before = User::count();

        $this->assertSame(0, $this->service->provisionFreeMembers($activation));
        $this->assertSame($before, User::count());
        $this->assertSame(0, Activation::where('isFree', true)->count());
    }

    // ------------------------------------------------------------------
    // FREE MEMBER APPROVAL
    // ------------------------------------------------------------------

    public function test_approving_a_free_member_completes_its_ledger_and_pays_the_satellite_owner(): void
    {
        $this->actingAs($this->admin());

        [$owner, , $satelliteActivation] = $this->makeSatellite(6, 'Bronze Satellite', 'SATBRZ');

        $this->service->provisionFreeMembers($satelliteActivation);

        $freeActivation = Activation::where('isFree', true)->first();
        $freeUser = User::find($freeActivation->UserID);

        $this->assertTrue(
            $this->service->approve($freeActivation, $freeUser->UserID, ActivationApprovalService::TYPE_FREE),
            'Free member approval should succeed.'
        );

        // Activation + account flipped.
        $freeActivation->refresh();
        $freeUser->refresh();

        $this->assertSame('Approved', $freeActivation->status);
        $this->assertSame('Active', $freeUser->status);

        // One-click approval: no policy / receipt numbers are ever issued.
        $this->assertNull($freeActivation->policy_number);
        $this->assertNull($freeActivation->receipt_number);

        // ALL the Pending per-fee membership-fee rows become Completed...
        $mfLedgers = Ledger::where('ActivationID', $freeActivation->ActivationID)
            ->where('Source', 'MF')
            ->orderBy('RefID')
            ->get();

        $this->assertCount(3, $mfLedgers);
        $this->assertSame(0, $mfLedgers->where('status', '!=', 'Completed')->count());
        $this->assertSame(['FEE001', 'FEE002', 'FEE003'], $mfLedgers->pluck('RefID')->all());
        $this->assertSame(900.00, (float) $mfLedgers->sum('amount'));

        // ...and nothing from the satellite split (SF / CS) leaks into a free
        // member's approval — those belong to the satellite activation only.
        $this->assertSame(
            0,
            Ledger::where('ActivationID', $freeActivation->ActivationID)
                ->whereIn('Source', ['SF', 'CS'])
                ->count()
        );

        // Satellite owner earns the normal stack: DR 90% of the Direct
        // Referral fee (360) + TX 10% (40) + ₱50 Provident Fund.
        $this->assertDatabaseHas('wallets', [
            'UserID' => $owner->UserID,
            'source' => 'DR',
            'amount' => 360.00,
            'status' => 'Active',
        ]);

        $this->assertDatabaseHas('ledgers', [
            'RefID' => (string) $owner->UserID,
            'Source' => 'TX',
            'amount' => 40.00,
            'status' => 'Completed',
        ]);

        $this->assertDatabaseHas('wallets', [
            'UserID' => $owner->UserID,
            'source' => 'PF',
            'amount' => 50.00,
            'status' => 'Active',
        ]);

        $this->assertDatabaseHas('ledgers', [
            'RefID' => (string) $owner->UserID,
            'Source' => 'PF',
            'amount' => -50.00,
            'status' => 'Completed',
        ]);

        // The free member earns nothing from their own approval.
        $this->assertSame(0, Wallet::where('UserID', $freeUser->UserID)->count());
    }

    public function test_rejecting_a_free_member_keeps_its_ledger_pending(): void
    {
        $this->actingAs($this->admin());

        [, , $satelliteActivation] = $this->makeSatellite(6, 'Bronze Satellite', 'SATBRZ');

        $this->service->provisionFreeMembers($satelliteActivation);

        $freeActivation = Activation::where('isFree', true)->first();

        $this->service->reject($freeActivation, $freeActivation->UserID, 'Not eligible.');

        $this->assertSame('Rejected', $freeActivation->refresh()->status);
        $this->assertSame('Inactive', User::find($freeActivation->UserID)->status);

        // Nothing was paid out, so every per-fee membership-fee row stays
        // Pending.
        $this->assertSame(
            3,
            Ledger::where('ActivationID', $freeActivation->ActivationID)
                ->where('Source', 'MF')
                ->where('status', 'Pending')
                ->count()
        );
    }

    public function test_an_invite_under_a_free_members_referral_code_pays_the_satellite_owner_ov(): void
    {
        $this->actingAs($this->admin());

        [$owner, , $satelliteActivation] = $this->makeSatellite(6, 'Bronze Satellite', 'SATBRZ');
        $this->service->provisionFreeMembers($satelliteActivation);

        $freeActivation = Activation::where('isFree', true)->first();
        $freeUser = User::find($freeActivation->UserID);

        $this->assertNotNull($freeUser?->referralCode, 'A free member needs their own referral code.');

        // The satellite owner's own upline chain, so every OV level above
        // the owner has somebody to land on:
        // invitee → free member (L1) → owner (L2) → up2 → up3 → up4 → up5 (L6).
        $up2 = $this->plainMember('Upline', 'Level2', 'UPL2', RoleID: 1);
        $up3 = $this->plainMember('Upline', 'Level3', 'UPL3', RoleID: 1);
        $up4 = $this->plainMember('Upline', 'Level4', 'UPL4', RoleID: 1);
        $up5 = $this->plainMember('Upline', 'Level5', 'UPL5', RoleID: 1);

        $owner->update(['uplineCode' => $up2->referralCode]);
        $up2->update(['uplineCode' => $up3->referralCode]);
        $up3->update(['uplineCode' => $up4->referralCode]);
        $up4->update(['uplineCode' => $up5->referralCode]);

        // A new member signs up with the FREE MEMBER's referral code.
        $invitee = User::create([
            'username' => 'invitee_of_free',
            'password' => 'password',
            'lastname' => 'Invitee',
            'firstname' => 'New',
            'RoleID' => 4,
            'referralCode' => 'INV001',
            'uplineCode' => $freeUser->referralCode,
            'status' => 'Inactive',
        ]);

        $inviteActivation = Activation::create([
            'UserID' => $invitee->UserID,
            'amount' => 900.00,
            'type' => 'W',
            'payment_method' => 'Cash',
            'upline_code' => $freeUser->referralCode,
            'ProcessedBy' => Auth::id(),
            'status' => 'Pending',
        ]);

        $this->assertTrue(
            $this->service->approve($inviteActivation, $invitee->UserID, ActivationApprovalService::TYPE_REGULAR),
            'Invite approval should succeed.'
        );

        // Level 1 = the free member earns the normal direct-referral stack.
        $this->assertDatabaseHas('wallets', [
            'UserID' => $freeUser->UserID,
            'source' => 'DR',
            'amount' => 360.00,
        ]);
        $this->assertDatabaseHas('wallets', [
            'UserID' => $freeUser->UserID,
            'source' => 'PF',
            'amount' => 50.00,
        ]);
        $this->assertDatabaseHas('ledgers', [
            'RefID' => (string) $freeUser->UserID,
            'Source' => 'TX',
            'amount' => 40.00,
        ]);

        // Level 2 = the satellite owner gets OV 50, and the walk continues
        // 40 → 30 → 20 → 10 through the owner's uplines — the same logic as
        // any normal invite, because the free member carries the owner's
        // referral code as their uplineCode.
        $expectedOV = [
            $owner->UserID => 50.00,
            $up2->UserID => 40.00,
            $up3->UserID => 30.00,
            $up4->UserID => 20.00,
            $up5->UserID => 10.00,
        ];

        foreach ($expectedOV as $userID => $amount) {
            $this->assertDatabaseHas('wallets', [
                'UserID' => $userID,
                'ActivationID' => $inviteActivation->ActivationID,
                'source' => 'OV',
                'amount' => $amount,
            ]);

            $this->assertDatabaseHas('ledgers', [
                'RefID' => (string) $userID,
                'ActivationID' => $inviteActivation->ActivationID,
                'Source' => 'OV',
                'amount' => -$amount,
            ]);
        }

        // Level 7 does not exist — exactly five OV payouts for the invite.
        $this->assertSame(
            5,
            Wallet::where('ActivationID', $inviteActivation->ActivationID)
                ->where('source', 'OV')
                ->count()
        );
    }

    // ------------------------------------------------------------------
    // FREE MEMBERS MODAL (the admin flow in the Satellite Members table)
    // ------------------------------------------------------------------

    public function test_free_members_can_be_listed_and_activated_from_the_modal(): void
    {
        $this->actingAs($this->admin());

        [$owner, , $satelliteActivation] = $this->makeSatellite(6, 'Bronze Satellite', 'SATBRZ');
        $this->service->provisionFreeMembers($satelliteActivation);

        $freeActivation = Activation::where('isFree', true)->first();
        $freeUser = User::find($freeActivation->UserID);

        $component = Livewire::test(FreeMembersModal::class)
            ->dispatch('open-free-members', UserID: $owner->UserID)
            ->assertSet('open', true)
            ->assertSet('pendingCount', 20)
            ->assertSet('approvedCount', 0)
            ->assertSee('BronzeSatellite-username-1');

        // Row-level confirmation, then the approval itself.
        $component
            ->call('confirmApproveFreeMember', $freeActivation->ActivationID)
            ->assertDispatched('swal:confirm');

        $component
            ->dispatch('free-member-approve-confirmed')
            ->assertDispatched('swal:success');

        $this->assertSame('Approved', $freeActivation->refresh()->status);
        $this->assertSame('Active', $freeUser->refresh()->status);

        $this->assertSame(
            'Completed',
            Ledger::where('ActivationID', $freeActivation->ActivationID)
                ->where('Source', 'MF')
                ->value('status')
        );

        // The table counters follow the approval.
        $component->assertSet('pendingCount', 19)->assertSet('approvedCount', 1);
    }

    public function test_the_modal_refuses_free_members_of_another_satellite(): void
    {
        $this->actingAs($this->admin());

        [, , $satelliteActivation] = $this->makeSatellite(6, 'Bronze Satellite', 'SATBRZ');
        $this->service->provisionFreeMembers($satelliteActivation);

        [$otherOwner, $otherSatellite] = $this->makeSatellite(6, 'Second Satellite', 'SATSEC');
        $foreignActivation = Activation::where('satelliteID', $otherSatellite->satelliteID)
            ->where('isFree', true)
            ->first();
        $this->assertNull($foreignActivation, 'The second satellite is not provisioned yet.');

        // A Pending activation ID belonging to another satellite can never be
        // approved through this modal, even if the state is stale.
        $component = Livewire::test(FreeMembersModal::class)
            ->dispatch('open-free-members', UserID: $otherOwner->UserID)
            ->assertSet('open', true)
            ->assertSet('pendingCount', 0);

        $component
            ->call('confirmApproveFreeMember', 999999)
            ->assertDispatched('swal:error');
    }

    public function test_satellite_members_page_exposes_the_free_members_action(): void
    {
        $this->actingAs($this->admin());

        [$owner, , $satelliteActivation] = $this->makeSatellite(6, 'Bronze Satellite', 'SATBRZ');
        $this->service->provisionFreeMembers($satelliteActivation);

        // The actions column shows the free-members icon for the satellite
        // member's row (the table renders firstname / middlename / lastname).
        Livewire::test(SatelliteMembers::class)
            ->assertSee('Manage Free Members')
            ->assertSee('Owner')
            ->call('openFreeMembers', $owner->UserID)
            ->assertDispatched('open-free-members');
    }

    // ------------------------------------------------------------------
    // REGULAR (NON-FREE) MEMBERS UNDER A SATELLITE
    // ------------------------------------------------------------------

    /**
     * A regular member invited with the satellite member's referralCode
     * shows up in the modal too — but with an "Approve" button that opens
     * the shared ActivationReviewModal instead of one-click activation.
     */
    public function test_regular_member_under_a_satellite_gets_the_approve_button_opening_the_review_modal(): void
    {
        $this->actingAs($this->admin());

        [$owner, , $satelliteActivation] = $this->makeSatellite(6, 'Bronze Satellite', 'SATBRZ');
        $this->service->provisionFreeMembers($satelliteActivation);

        // A paid member signs up under the satellite member's code.
        $regular = $this->plainMember('Regular', 'Invitee', 'REGINV');
        $regular->update(['uplineCode' => $owner->referralCode]);

        $regularActivation = Activation::create([
            'UserID' => $regular->UserID,
            'amount' => 900.00,
            'type' => 'W',
            'payment_method' => 'Cash',
            'upline_code' => $owner->referralCode,
            'ProcessedBy' => Auth::id(),
            'status' => 'Pending',
        ]);

        $component = Livewire::test(FreeMembersModal::class)
            ->dispatch('open-free-members', UserID: $owner->UserID)
            ->assertSet('open', true)
            // 20 free + 1 regular all pending; only 20 are FREE pending.
            ->assertSet('pendingCount', 21)
            ->assertSet('pendingFreeCount', 20)
            ->assertSee('invitee_reginv')
            ->assertSee('REGULAR')
            ->assertSee('Approve')
            ->assertSee('Activate');

        // The Approve button opens the EXACT shared review modal with the
        // activation ID — same process as approving any normal member.
        $component
            ->call('openRegularMemberReview', $regularActivation->ActivationID)
            ->assertDispatched(
                'open-activation-review',
                UserID: $regular->UserID,
                activationId: $regularActivation->ActivationID,
            );

        // Nothing was approved by opening the review — it is a review flow.
        $this->assertSame('Pending', $regularActivation->refresh()->status);

        // ...and the free one-click path refuses the regular activation.
        $component
            ->call('confirmApproveFreeMember', $regularActivation->ActivationID)
            ->assertDispatched('swal:error');
    }

    /**
     * A regular pending activation can never be swept up by Activate All —
     * it only ever touches Pending FREE rows.
     */
    public function test_activate_all_never_includes_regular_members(): void
    {
        $this->actingAs($this->admin());

        [$owner, , $satelliteActivation] = $this->makeSatellite(6, 'Bronze Satellite', 'SATBRZ');
        $this->service->provisionFreeMembers($satelliteActivation);

        $regular = $this->plainMember('Regular', 'Invitee', 'REGINV');
        $regular->update(['uplineCode' => $owner->referralCode, 'status' => 'Inactive']);

        $regularActivation = Activation::create([
            'UserID' => $regular->UserID,
            'amount' => 900.00,
            'type' => 'W',
            'payment_method' => 'Cash',
            'upline_code' => $owner->referralCode,
            'ProcessedBy' => Auth::id(),
            'status' => 'Pending',
        ]);

        $component = Livewire::test(FreeMembersModal::class)
            ->dispatch('open-free-members', UserID: $owner->UserID)
            ->assertSet('pendingCount', 21)
            ->assertSet('pendingFreeCount', 20);

        // The batch confirm is typed ("ACTIVATE ALL") and previews only
        // free usernames — the regular member is not in the payload.
        $component->call('confirmApproveAllFreeMembers')
            ->assertDispatched('swal:confirm', function ($event, $params) {
                $payload = $params[0] ?? $params;

                return ($payload['requireConfirmWord'] ?? null) === 'ACTIVATE ALL'
                    && ! str_contains($payload['html'] ?? '', 'invitee_reginv');
            });

        $component->dispatch('free-member-approve-all-confirmed');

        // Every free member was activated...
        $this->assertSame(
            20,
            Activation::where('satelliteID', $satelliteActivation->satelliteID)
                ->where('isFree', true)
                ->where('status', 'Approved')
                ->count()
        );

        // ...and the regular member is untouched: still Pending.
        $this->assertSame('Pending', $regularActivation->refresh()->status);
        $this->assertSame('Inactive', $regular->refresh()->status);
    }

    /**
     * Both activation dialogs are typed confirmations with a preview:
     * the single activate requires the member's username, the batch
     * requires "ACTIVATE ALL".
     */
    public function test_activate_confirmations_require_typed_input(): void
    {
        $this->actingAs($this->admin());

        [$owner, , $satelliteActivation] = $this->makeSatellite(6, 'Bronze Satellite', 'SATBRZ');
        $this->service->provisionFreeMembers($satelliteActivation);

        $freeActivation = Activation::where('isFree', true)->first();
        $freeUsername = User::find($freeActivation->UserID)->username;

        $component = Livewire::test(FreeMembersModal::class)
            ->dispatch('open-free-members', UserID: $owner->UserID);

        // Single activate → typed word = the member's own username,
        // and the dialog previews that same username.
        $component
            ->call('confirmApproveFreeMember', $freeActivation->ActivationID)
            ->assertDispatched('swal:confirm', function ($event, $params) use ($freeUsername) {
                $payload = $params[0] ?? $params;

                return ($payload['requireConfirmWord'] ?? null) === $freeUsername
                    && str_contains($payload['html'] ?? '', $freeUsername);
            });

        // Activate All → typed word = "ACTIVATE ALL" + a preview list.
        $component
            ->call('confirmApproveAllFreeMembers')
            ->assertDispatched('swal:confirm', function ($event, $params) use ($freeUsername) {
                $payload = $params[0] ?? $params;

                return ($payload['requireConfirmWord'] ?? null) === 'ACTIVATE ALL'
                    && str_contains($payload['html'] ?? '', $freeUsername);
            });
    }

    // ------------------------------------------------------------------
    // PALETTE — blue + yellow only
    // ------------------------------------------------------------------

    public function test_the_free_members_modal_uses_only_the_blue_and_yellow_palette(): void
    {
        $this->actingAs($this->admin());

        [$owner, , $satelliteActivation] = $this->makeSatellite(6, 'Bronze Satellite', 'SATBRZ');
        $this->service->provisionFreeMembers($satelliteActivation);

        $html = Livewire::test(FreeMembersModal::class)
            ->dispatch('open-free-members', UserID: $owner->UserID)
            ->html();

        // No violet / green / rose / purple / indigo anywhere in the modal.
        foreach (['emerald', 'violet', 'green', 'purple', 'indigo', 'rose-', 'teal', 'lime'] as $color) {
            $this->assertStringNotContainsString(
                $color,
                $html,
                "The free members palette must be blue + yellow only — found '{$color}'."
            );
        }
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function admin(): User
    {
        return $this->plainMember('Admin', 'Super', 'ADM001', RoleID: 1);
    }

    private function plainMember(
        string $lastname,
        string $firstname,
        string $code,
        int $RoleID = 4
    ): User {
        return User::create([
            'username' => strtolower($firstname.'_'.$code),
            'password' => 'password',
            'lastname' => $lastname,
            'firstname' => $firstname,
            'RoleID' => $RoleID,
            'referralCode' => $code,
            'status' => 'Active',
        ]);
    }

    /**
     * The state a submitted satellite application leaves behind: a RoleID 8
     * user (own referralCode), its Satellite row and a Pending activation
     * carrying the satelliteID + the upline code chosen in SatelliteModal.
     *
     * @return array{0: User, 1: Satellite, 2: Activation}
     */
    private function makeSatellite(int $typeID, string $name, string $ownerCode): array
    {
        $owner = User::create([
            'username' => 'sat_'.strtolower($ownerCode),
            'password' => 'password',
            'lastname' => 'Owner',
            'firstname' => 'Satellite',
            'RoleID' => 8,
            'referralCode' => $ownerCode,
            'status' => 'Active',
        ]);

        $satellite = Satellite::create([
            'satellite_name' => $name,
            'UserID' => $owner->UserID,
            'satellite_typeID' => $typeID,
        ]);

        $activation = Activation::create([
            'UserID' => $owner->UserID,
            'satelliteID' => $satellite->satelliteID,
            'amount' => 1250.00,
            'type' => 'W',
            'payment_method' => 'Cash',
            'upline_code' => $this->upline()->referralCode,
            'ProcessedBy' => Auth::id(),
            'status' => 'Pending',
        ]);

        return [$owner, $satellite, $activation];
    }

    /**
     * A fresh upline per satellite so referral codes stay unique. Kept at
     * RoleID 1 (a staff account) so the RoleID 4 counts below only ever
     * count provisioned free members.
     */
    private function upline(): User
    {
        $this->uplineSeq++;

        return $this->plainMember('Upline', 'Direct', 'UP'.$this->uplineSeq, RoleID: 1);
    }
}
