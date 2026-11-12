<?php

namespace Tests\Feature;

use App\Livewire\AdminPages\TransactionList\Disbursements;
use App\Models\Disbursement;
use App\Models\Ledger;
use App\Models\MembershipFee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use LogicException;
use Tests\TestCase;

/**
 * Disburstment ↔ ledgers mirror contract:
 *
 * ONE ledger row per fee fund (Source 'DB', RefID = FeeID):
 *   mirror amount  = -(total non-void disbursements of the fund)
 *   mirror status  = Cancelled when every disbursement is voided
 *                    Pending   while any non-void row is Unposted
 *                    Completed once every non-void row is Posted
 *
 * Every created / updated / posted / voided disbursement re-rolls its
 * fund's mirror automatically. Editing is allowed while Unposted only
 * (the record date too); Posted/Void rows freeze amount/fee/date and
 * void-reason remarks stay editable. Statuses never move backwards;
 * rows are never deleted.
 */
class DisbursementLedgerSyncTest extends TestCase
{
    use RefreshDatabase;

    private MembershipFee $fee;

    protected function setUp(): void
    {
        parent::setUp();

        // FeeID is a VARCHAR business key ('FEE001'…), just like production.
        // VID is deliberately NOT set here: the column exists only on the
        // live MySQL database (added by hand), not in the migrated SQLite
        // test schema.
        // The live database keeps `variants` and membership_fees.VID as
        // hand-created structures the migrations do not define; the variant
        // cards and filters read both.
        if (! Schema::hasColumn('membership_fees', 'VID')) {
            Schema::table('membership_fees', function ($table) {
                $table->string('VID')->nullable();
            });
        }

        // activations.VID is the same story: production-only, added by hand.
        // Released money is attributed to a variant through it.
        if (! Schema::hasColumn('activations', 'VID')) {
            Schema::table('activations', function ($table) {
                $table->unsignedBigInteger('VID')->nullable();
            });
        }

        if (! Schema::hasTable('variants')) {
            Schema::create('variants', function ($table) {
                $table->id('VID');
                $table->string('name');
                $table->string('description')->default('');
                $table->string('status')->default('Active');
                $table->timestamps();
            });
        }

        $this->fee = MembershipFee::create([
            'FeeID' => 'FEE900',
            'FeeName' => 'Test Fund',
            'Cost' => 1000,
        ]);
    }

    private function createValidDisbursement(array $overrides = []): Disbursement
    {
        return Disbursement::create($overrides + [
            'FeeID' => $this->fee->FeeID,
            'amount' => 150.50,
            'remarks' => 'payout to implementer',
        ]);
    }

    private function mirroredLedger(Disbursement $disbursement): ?Ledger
    {
        // One rollup row per fee fund: RefID = FeeID.
        return Ledger::where('Source', 'DB')
            ->where('RefID', $disbursement->FeeID)
            ->first();
    }

    /* ------------------------------------------------------------------ */

    public function test_creating_a_disbursement_creates_a_pending_negative_db_ledger_entry(): void
    {
        $disbursement = $this->createValidDisbursement();

        $ledger = $this->mirroredLedger($disbursement);

        $this->assertNotNull($ledger);
        $this->assertSame('DB', $ledger->Source);
        $this->assertSame($this->fee->FeeID, (string) $ledger->RefID); // RefID = membership_fees ID
        $this->assertSame('Pending', $ledger->status);
        $this->assertEquals(-150.50, (float) $ledger->amount);
    }

    public function test_several_disbursements_of_one_fund_share_a_single_rollup_row(): void
    {
        $first = $this->createValidDisbursement();              // 150.50
        $second = $this->createValidDisbursement(['amount' => 100]);

        // While any row is Unposted the fund mirror stays Pending.
        $ledger = $this->mirroredLedger($first);
        $this->assertSame('Pending', $ledger->status);
        $this->assertEquals(-250.50, (float) $ledger->amount);
        $this->assertSame(
            $ledger->TransactionID,
            $this->mirroredLedger($second)->TransactionID, // same single row
            'Multiple disbursements of one fund must share ONE rollup row.'
        );

        // Posting one row leaves the fund Pending (the other is not).
        $first->update(['status' => Disbursement::STATUS_POSTED]);
        $this->assertSame('Pending', $this->mirroredLedger($first)->status);

        // Once every non-void row is Posted the mirror completes.
        $second->update(['status' => Disbursement::STATUS_POSTED]);
        $ledger = $this->mirroredLedger($first);
        $this->assertSame('Completed', $ledger->status);
        $this->assertEquals(-250.50, (float) $ledger->amount);
    }

    public function test_void_rows_drop_out_of_the_fund_rollup(): void
    {
        $posted = $this->createValidDisbursement();
        $posted->update(['status' => Disbursement::STATUS_POSTED]);
        $pending = $this->createValidDisbursement(['amount' => 30]);

        // Only the 150.50 posted row counts; the 30.00 pending row keeps the mirror Pending.
        $this->createValidDisbursement(['amount' => 70])->update(['status' => Disbursement::STATUS_VOID]);
        $ledger = $this->mirroredLedger($posted);
        $this->assertEquals(-180.50, (float) $ledger->amount);
        $this->assertSame('Pending', $ledger->status);

        // Voiding the pending row too: everything left is posted.
        $pending->update(['status' => Disbursement::STATUS_VOID]);
        $ledger2 = $this->mirroredLedger($posted);
        $this->assertSame('Completed', $ledger2->status);
        $this->assertEquals(-150.50, (float) $ledger2->amount);
    }

    public function test_voiding_the_only_disbursement_cancels_the_fund_mirror_at_zero(): void
    {
        $disbursement = $this->createValidDisbursement();
        $disbursement->update(['status' => Disbursement::STATUS_VOID]);

        $ledger = $this->mirroredLedger($disbursement);
        $this->assertSame('Cancelled', $ledger->status);
        $this->assertEquals(0.0, (float) $ledger->amount);
    }

    public function test_the_ledger_enums_accept_the_db_source(): void
    {
        $disbursement = $this->createValidDisbursement();

        $this->assertDatabaseHas('ledgers', [
            'Source' => 'DB',
            'status' => 'Pending',
        ]);
        $this->assertSame(
            Disbursement::ledgerStatusFor(Disbursement::STATUS_UNPOSTED),
            $this->mirroredLedger($disbursement)->status
        );
    }

    public function test_editing_an_unposted_disbursement_syncs_the_pending_ledger(): void
    {
        $disbursement = $this->createValidDisbursement();

        $disbursement->fill([
            'amount' => 90,
            'FeeID' => $this->fee->FeeID,
            'remarks' => 'adjusted payout',
        ])->save();

        $ledger = $this->mirroredLedger($disbursement);
        $this->assertSame('Pending', $ledger->status);
        $this->assertEquals(-90.0, (float) $ledger->amount);
    }

    public function test_posting_completes_the_ledger_entry(): void
    {
        $disbursement = $this->createValidDisbursement();
        $disbursement->update(['status' => Disbursement::STATUS_POSTED]);

        $ledger = $this->mirroredLedger($disbursement);
        $this->assertSame('Completed', $ledger->status);
        $this->assertEquals(-150.50, (float) $ledger->amount);
        $this->assertSame($this->fee->FeeID, (string) $ledger->fresh()->RefID); // still points at the fund
    }

    public function test_voiding_from_unposted_cancels_the_ledger_entry(): void
    {
        $disbursement = $this->createValidDisbursement();
        $disbursement->update(['status' => Disbursement::STATUS_VOID]);

        $ledger = $this->mirroredLedger($disbursement);
        $this->assertSame('Cancelled', $ledger->status);
    }

    public function test_voiding_from_posted_cancels_the_ledger_entry_and_keeps_the_reason_intact(): void
    {
        $disbursement = $this->createValidDisbursement();
        $disbursement->update(['status' => Disbursement::STATUS_POSTED]);

        // Remarks stay editable on posted rows so a void reason can be
        // appended to the audit trail (financial fields stay frozen).
        $disbursement->remarks = $disbursement->remarks."\nVoid reason: wrong fund";
        $disbursement->save();

        $disbursement->update(['status' => Disbursement::STATUS_VOID]);

        $ledger = $this->mirroredLedger($disbursement);
        $this->assertSame('Cancelled', $ledger->status);
        $this->assertStringContainsString('Void reason: wrong fund', (string) $disbursement->remarks);
    }

    public function test_posted_rows_freeze_their_financial_fields(): void
    {
        $disbursement = $this->createValidDisbursement();
        $disbursement->update(['status' => Disbursement::STATUS_POSTED]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('no longer be edited');

        $disbursement->update([
            'amount' => 999,
            'FeeID' => $this->fee->FeeID,
        ]);
    }

    public function test_status_never_moves_backwards(): void
    {
        $disbursement = $this->createValidDisbursement();
        $disbursement->update(['status' => Disbursement::STATUS_POSTED]);

        try {
            $disbursement->update(['status' => Disbursement::STATUS_UNPOSTED]);
            $this->fail('Posting back to unposted should have been rejected.');
        } catch (LogicException) {
            $this->assertTrue(true);
        }

        // Void is terminal too: 2 -> 1 is forbidden.
        $disbursement->update(['status' => Disbursement::STATUS_VOID]);

        try {
            $disbursement->update(['status' => Disbursement::STATUS_POSTED]);
            $this->fail('Void → posted should have been rejected.');
        } catch (LogicException) {
            $this->assertTrue(true);
        }

        // And the true mirror rows above never changed status mid-flight.
        $this->assertSame('Cancelled', $this->mirroredLedger($disbursement)->status);
    }

    public function test_void_rows_do_not_count_against_the_fund(): void
    {
        $this->createValidDisbursement(['amount' => 100]);
        $this->createValidDisbursement(['amount' => 50]);
        $voided = $this->createValidDisbursement(['amount' => 70]);
        $voided->update(['status' => Disbursement::STATUS_VOID]);

        $this->assertSame(150.0, Disbursement::disbursedForFee($this->fee->FeeID));
    }

    /* ------------------------------------------------------------------ */

    /**
     * The test suite runs migrations without seeders: seed module 16 with
     * a full Super Admin grant and return the acting admin user.
     */
    private function seedAccess(): User
    {
        $now = now();

        DB::table('modules')->updateOrInsert(['ModuleID' => 16], [
            'modulename' => 'Transaction List - Disbursement',
            'description' => 'Manage disbursements paid out of collected membership fees',
            'status' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // users.privileges FK to roles requires the role row to exist first.
        DB::table('roles')->updateOrInsert(['RoleID' => 1], [
            'rolename' => 'Super Administrator',
            'status' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('privileges')->updateOrInsert(
            ['RoleID' => 1, 'ModuleID' => 16],
            [
                'can_create' => 1,
                'can_read' => 1,
                'can_update' => 1,
                'can_delete' => 1,
                'can_print' => 1,
                'can_change_status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        return User::create([
            'username' => 'super_admin_smoke',
            'password' => 'password',
            'lastname' => 'Admin',
            'firstname' => 'Super',
            'RoleID' => 1,
            'referralCode' => 'SMK001',
            'status' => 'Active',
        ]);
    }

    /**
     * Versions of the tab before an, otherwise unversioned, render caught the
     * blade hitting its #[Computed] properties as bare variables — which
     * throws "Undefined variable" only inside the open modal.
     */
    public function test_the_component_renders_the_modal_with_the_fund_available_line(): void
    {
        $admin = $this->seedAccess();

        // Fund collected and drawn from, so "available" has a real figure:
        // ledger 1,000.00 in, 150.50 disbursed → 849.50 left.
        Ledger::create(['Source' => 'MF', 'RefID' => $this->fee->FeeID, 'amount' => 1000, 'status' => 'Completed']);
        $this->createValidDisbursement(); // 150.50

        Livewire::actingAs($admin)
            ->test(Disbursements::class)
            ->set('showModal', true)
            ->set('fee_id', $this->fee->FeeID)
            ->assertSee('FEE900', false)
            ->assertSee('Fund available', false)
            ->assertSee('849.50');
    }

    /**
     * The variant card row: picking a card scopes the modal fund dropdown
     * (and the tab's table) to that variant's funds only; a previously
     * picked fee outside the scope is cleared.
     */
    public function test_selecting_a_variant_card_narrows_the_fund_dropdown_to_that_variant(): void
    {
        $now = now();

        $vidAlpha = DB::table('variants')->insertGetId([
            'name' => 'Alpha Variant',
            'description' => '',
            'status' => 'Active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $vidBravo = DB::table('variants')->insertGetId([
            'name' => 'Bravo Variant',
            'description' => '',
            'status' => 'active',   // lowercase on purpose: matching is case-tolerant
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('variants')->insert([
            'name' => 'Retired Variant',
            'description' => '',
            'status' => 'Inactive',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('membership_fees')->where('FeeID', $this->fee->FeeID)
            ->update(['VID' => $vidAlpha]);

        MembershipFee::create(['FeeID' => 'FEE901', 'FeeName' => 'Bravo Fund', 'Cost' => 100]);
        DB::table('membership_fees')->where('FeeID', 'FEE901')->update(['VID' => $vidBravo]);

        $component = Livewire::test(Disbursements::class);

        // Cards list the ACTIVE variants only (case-insensitive), with fund counts.
        $cards = $component->instance()->variantCards;
        $this->assertSame(['Alpha Variant', 'Bravo Variant'], $cards->pluck('name')->all());
        $this->assertSame(1, $cards->firstWhere('name', 'Alpha Variant')['funds']);

        // Card money is sourced from the ledger (Source MF, Completed): the
        // Alpha variant has one fund that collected 1,000.00 and later
        // disbursed 150.50, so its card must read collected / available
        // exactly like the Report & Settings breakdown does.
        Ledger::create(['Source' => 'MF', 'RefID' => $this->fee->FeeID, 'amount' => 1000, 'status' => 'Completed']);
        $this->createValidDisbursement(); // 150.50 out, unposted (pending)

        // A 'Direct Referral' fee fund under this variant with a released DR payout:
        // 360.00 must show up in the card's released figure even though the
        // ledger row never names the fund — attribution runs through
        // ledgers.ActivationID -> activation.VID (RefID holds the member's
        // UserID on release rows, never the activation).
        // users.RoleID FK — create the role first, as migrations seed none.
        DB::table('roles')->updateOrInsert(['RoleID' => 4], [
            'rolename' => 'Member',
            'status' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $activationUser = User::create([
            'username' => 'release_holder',
            'password' => 'password',
            'lastname' => 'Holder',
            'firstname' => 'Release',
            'RoleID' => 4,
            'referralCode' => 'RLS001',
            'status' => 'Active',
        ]);

        $activation = \App\Models\Activation::create([
            'UserID' => $activationUser->UserID,
            'VID' => $vidAlpha,
            'amount' => 100,
            'type' => 'W',
            'payment_method' => 'Cash',
            'upline_code' => 'SMK001',
        ]);

        Ledger::create([
            'Source' => 'DR',
            'RefID' => (string) $activationUser->UserID, // the member, not the activation
            'ActivationID' => $activation->ActivationID, // the real attribution column
            'amount' => -360,
            'status' => 'Completed',
        ]);

        // #[Computed] memoizes per request; drop the cache so the re-read
        // reflects the rows just written.
        unset($component->instance()->variantCards);

        $alpha = $component->instance()->variantCards->firstWhere('name', 'Alpha Variant');
        $this->assertSame(1000.0, $alpha['collected']);
        $this->assertEquals(150.5, $alpha['disbursed']);
        $this->assertEquals(360.0, $alpha['released']);
        $this->assertSame(0.0, $alpha['releasedLegacy']); // two actives — no fallback
        $this->assertEquals(0.0, $alpha['otherIn']);
        $this->assertEquals(489.5, $alpha['available']);

        // Selecting Bravo clears the Alpha fee that was picked pre-selection.
        $component
            ->set('fee_id', $this->fee->FeeID)
            ->call('selectVariant', $vidBravo)
            ->assertSet('fee_id', null);

        $this->assertSame(
            ['FEE901'],
            $component->instance()->feeOptions->pluck('FeeID')->all()
        );

        // Selecting Alpha narrows the other way; null returns to every fund.
        $component->call('selectVariant', $vidAlpha);
        $this->assertSame(
            ['FEE900'],
            $component->instance()->feeOptions->pluck('FeeID')->all()
        );

        $component->call('selectVariant', null);
        $this->assertSame(
            ['FEE901', 'FEE900'], // all funds, FeeName order
            $component->instance()->feeOptions->pluck('FeeID')->all()
        );
    }

    /**
     * With exactly one Active variant, outflow rows that cannot be
     * attributed through activation VID (legacy activations, dead RefIDs)
     * count against that variant — the sole-active fallback the admins
     * chose for their legacy ledger data.
     */
    public function test_unattributed_releases_fall_back_to_the_sole_active_variant(): void
    {
        $now = now();

        DB::table('variants')->insert([
            'name' => 'Only Variant',
            'description' => '',
            'status' => 'Active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // The single fund collected 1000. It belongs to the sole variant.
        DB::table('membership_fees')->where('FeeID', $this->fee->FeeID)
            ->update(['VID' => DB::table('variants')->where('name', 'Only Variant')->value('VID')]);
        Ledger::create(['Source' => 'MF', 'RefID' => $this->fee->FeeID, 'amount' => 1000, 'status' => 'Completed']);

        // Migrations seed no roles, but users.RoleID FK needs the row.
        DB::table('roles')->updateOrInsert(['RoleID' => 4], [
            'rolename' => 'Member',
            'status' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $activationUser = User::create([
            'username' => 'legacy_holder',
            'password' => 'password',
            'lastname' => 'Holder',
            'firstname' => 'Legacy',
            'RoleID' => 4,
            'referralCode' => 'LEG001',
            'status' => 'Active',
        ]);

        // Legacy outflows: a real activation whose VID is NULL and a RefID
        // that hits no activation at all — both unattributable to a variant.
        $legacyActivation = \App\Models\Activation::create([
            'UserID' => $activationUser->UserID,
            'VID' => null, // predates the activations.VID column
            'amount' => 100,
            'type' => 'W',
            'payment_method' => 'Cash',
            'upline_code' => 'LEG001',
        ]);

        Ledger::create([
            'Source' => 'DR',
            'RefID' => (string) $legacyActivation->ActivationID,
            'amount' => -700,
            'status' => 'Completed',
        ]);

        Ledger::create([
            'Source' => 'WD',
            'RefID' => '999999', // hits no activation
            'amount' => -100,
            'status' => 'Completed',
        ]);

        // Money-in also rides the fallback: a legacy CF row lands on the
        // sole active card and widens the fund.
        Ledger::create([
            'Source' => 'CF',
            'RefID' => (string) $legacyActivation->ActivationID,
            'amount' => 100,
            'status' => 'Completed',
        ]);

        $card = Livewire::test(Disbursements::class)->instance()->variantCards->sole();

        $this->assertEquals(800.0, $card['released']);       // 700 + 100
        $this->assertEquals(800.0, $card['releasedLegacy']); // everything is legacy
        $this->assertEquals(100.0, $card['otherIn']);
        $this->assertEquals(100.0, $card['otherInLegacy']);
        $this->assertEquals(300.0, $card['available']);      // 1000 − 800 + 100
    }

    /**
     * A fee fund's "Fund available" line in the modal ALSO subtracts the
     * ledger releases matched to the fund by source name (DR out of
     * "Direct Referral", etc.). Unmatched funds ignore releases entirely;
     * the legacy share counts while the sole-active fallback applies.
     */
    public function test_a_fund_line_subtracts_the_releases_of_its_matched_source(): void
    {
        $admin = $this->seedAccess();

        $now = now();
        $vid = DB::table('variants')->insertGetId([
            'name' => 'Alpha Variant', 'description' => '', 'status' => 'Active',
            'created_at' => $now, 'updated_at' => $now,
        ]);

        // The two funds of the single variant: one matched to DR, one not.
        MembershipFee::create(['FeeID' => 'FEE901', 'FeeName' => 'Direct Referral', 'Cost' => 400]);
        DB::table('membership_fees')->where('FeeID', 'FEE901')->update(['VID' => $vid]);
        MembershipFee::create(['FeeID' => 'FEE902', 'FeeName' => 'Insurance', 'Cost' => 450]);
        DB::table('membership_fees')->where('FeeID', 'FEE902')->update(['VID' => $vid]);

        Ledger::create(['Source' => 'MF', 'RefID' => 'FEE901', 'amount' => 1000, 'status' => 'Completed']);
        Ledger::create(['Source' => 'MF', 'RefID' => 'FEE902', 'amount' => 500, 'status' => 'Completed']);

        DB::table('roles')->updateOrInsert(['RoleID' => 4], [
            'rolename' => 'Member', 'status' => 1, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $member = User::create([
            'username' => 'release_holder', 'password' => 'password', 'lastname' => 'Holder',
            'firstname' => 'Release', 'RoleID' => 4, 'referralCode' => 'RLS001', 'status' => 'Active',
        ]);
        $activation = \App\Models\Activation::create([
            'UserID' => $member->UserID, 'VID' => $vid, 'amount' => 100,
            'type' => 'W', 'payment_method' => 'Cash', 'upline_code' => 'RLS001',
        ]);

        // A release attributed to the variant (DR out of the matched fund),
        // plus a legacy DR row nothing can attribute to any activation.
        Ledger::create(['Source' => 'DR', 'RefID' => (string) $activation->ActivationID, 'amount' => -579.88, 'status' => 'Completed']);
        Ledger::create(['Source' => 'DR', 'RefID' => '999999', 'amount' => -100, 'status' => 'Completed']);

        $component = Livewire::actingAs($admin)->test(Disbursements::class)
            ->set('showModal', true);

        // Matched fund: 1,000 collected − 679.88 released (579.88 strict +
        // 100 legacy while exactly one variant is Active) = 320.12 available.
        $component->set('fee_id', 'FEE901')
            ->assertSee('320.12', false);

        // Unmatched fund: releases never touch "Insurance" — 500.00 available.
        $component->set('fee_id', 'FEE902')
            ->assertSee('500.00', false);
    }

    /**
     * The record date defaults to "now" at creation, is editable while the
     * disbursement is Unposted, and freezes forever once Posted.
     */
    public function test_the_record_date_is_editable_while_unposted_and_frozen_after(): void
    {
        $disbursement = $this->createValidDisbursement(['created_at' => '2026-10-01 05:00:00']);
        $this->assertSame('2026-10-01 05:00:00', $disbursement->created_at->format('Y-m-d H:i:s'));

        // Editable while Unposted.
        $disbursement->created_at = '2026-10-15 05:00:00';
        $disbursement->save();
        $this->assertSame('2026-10-15 05:00:00', $disbursement->fresh()->created_at->format('Y-m-d H:i:s'));

        // Frozen once Posted — the date joins the financial fields.
        $disbursement->update(['status' => Disbursement::STATUS_POSTED]);
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('no longer be edited');

        $disbursement->created_at = '2020-01-01 00:00:00';
        $disbursement->save();
    }

    /**
     * Auto refresh: every create, update and void re-renders the tab's
     * table from the live data (no manual reload step required).
     */
    public function test_the_table_refreshes_after_every_create_update_and_void(): void
    {
        $admin = $this->seedAccess();

        Ledger::create(['Source' => 'MF', 'RefID' => $this->fee->FeeID, 'amount' => 1000, 'status' => 'Completed']);

        $component = Livewire::actingAs($admin)->test(Disbursements::class)
            ->set('showModal', true)
            ->set('fee_id', $this->fee->FeeID)
            ->set('amount', '250')
            ->set('remarks', 'first payout')
            ->call('save');

        unset($component->instance()->rows);
        $this->assertSame(1, $component->instance()->rows->count());

        $component->set('editingDid', $component->instance()->rows->first()->DID)
            ->set('fee_id', $this->fee->FeeID)
            ->set('amount', '275')
            ->call('save');
        unset($component->instance()->rows);
        $this->assertEquals(275.0, (float) $component->instance()->rows->first()->amount);

        $component->call('void', $component->instance()->rows->first()->DID);
        unset($component->instance()->rows);
        $this->assertSame(Disbursement::STATUS_VOID,
            (int) $component->instance()->rows->first()->status);
    }

    /**
     * Money can only be taken out of a fund that still holds it: the amount
     * is validated against the modal's "Fund available" figure (collected −
     * ledger releases matched to the fund − already disbursed, excl. void).
     */
    public function test_the_amount_cannot_exceed_the_remaining_balance_of_the_selected_fund(): void
    {
        $admin = $this->seedAccess();

        Ledger::create(['Source' => 'MF', 'RefID' => $this->fee->FeeID, 'amount' => 1000, 'status' => 'Completed']);
        $this->createValidDisbursement(['amount' => 400]); // 600.00 left

        Livewire::actingAs($admin)->test(Disbursements::class)
            ->set('showModal', true)
            ->set('fee_id', $this->fee->FeeID)
            ->set('amount', '650')
            ->call('save')
            ->assertHasErrors(['amount']);

        $this->assertSame(1, Disbursement::count());

        // Right at the ceiling it goes through.
        Livewire::actingAs($admin)->test(Disbursements::class)
            ->set('showModal', true)
            ->set('fee_id', $this->fee->FeeID)
            ->set('amount', '600')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(2, Disbursement::count());

        // Regression: the form field is `fee_id` but the column is `FeeID` —
        // a missing mapping silently saved NULL funds.
        $this->assertSame(
            [$this->fee->FeeID, $this->fee->FeeID],
            Disbursement::orderBy('DID')->pluck('FeeID')->all()
        );
    }

    /**
     * The variant card scopes the fund list of a NEW disbursement only: the
     * table must keep listing every row, otherwise a saved disbursement
     * looks like it was never created.
     */
    public function test_the_table_is_not_narrowed_by_the_selected_variant_card(): void
    {
        $admin = $this->seedAccess();
        $now = now();

        $vidAlpha = DB::table('variants')->insertGetId([
            'name' => 'Alpha Variant', 'description' => '', 'status' => 'Active',
            'created_at' => $now, 'updated_at' => $now,
        ]);
        $vidBravo = DB::table('variants')->insertGetId([
            'name' => 'Bravo Variant', 'description' => '', 'status' => 'Active',
            'created_at' => $now, 'updated_at' => $now,
        ]);

        DB::table('membership_fees')->where('FeeID', $this->fee->FeeID)->update(['VID' => $vidAlpha]);
        MembershipFee::create(['FeeID' => 'FEE903', 'FeeName' => 'Bravo Fund', 'Cost' => 100]);
        DB::table('membership_fees')->where('FeeID', 'FEE903')->update(['VID' => $vidBravo]);

        Ledger::create(['Source' => 'MF', 'RefID' => $this->fee->FeeID, 'amount' => 1000, 'status' => 'Completed']);

        $alphaRow = $this->createValidDisbursement();
        $bravoRow = Disbursement::create(['FeeID' => 'FEE903', 'amount' => 40]);

        $component = Livewire::actingAs($admin)->test(Disbursements::class);

        // The Alpha card narrows the creation dropdown…
        $component->call('selectVariant', $vidAlpha);
        $this->assertSame(['FEE900'], $component->instance()->feeOptions->pluck('FeeID')->all());

        // …but never the table.
        $this->assertSame(
            [$bravoRow->DID, $alphaRow->DID],
            $component->instance()->rows->pluck('DID')->all()
        );

        // A row saved while a card is selected shows up immediately.
        $component->set('showModal', true)
            ->set('fee_id', 'FEE900')
            ->set('amount', '10')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertCount(3, $component->instance()->rows);
    }

    /**
     * Editing ignores the selected card: the dropdown is not narrowed, so
     * the row's own source fund is restored (and stays selectable).
     */
    public function test_editing_restores_the_saved_fund_even_with_another_card_selected(): void
    {
        $admin = $this->seedAccess();
        $now = now();

        $vidAlpha = DB::table('variants')->insertGetId([
            'name' => 'Alpha Variant', 'description' => '', 'status' => 'Active',
            'created_at' => $now, 'updated_at' => $now,
        ]);
        $vidBravo = DB::table('variants')->insertGetId([
            'name' => 'Bravo Variant', 'description' => '', 'status' => 'Active',
            'created_at' => $now, 'updated_at' => $now,
        ]);

        DB::table('membership_fees')->where('FeeID', $this->fee->FeeID)->update(['VID' => $vidAlpha]);
        MembershipFee::create(['FeeID' => 'FEE903', 'FeeName' => 'Bravo Fund', 'Cost' => 100]);
        DB::table('membership_fees')->where('FeeID', 'FEE903')->update(['VID' => $vidBravo]);

        Ledger::create(['Source' => 'MF', 'RefID' => $this->fee->FeeID, 'amount' => 1000, 'status' => 'Completed']);

        $row = $this->createValidDisbursement();

        $component = Livewire::actingAs($admin)->test(Disbursements::class);
        $component->call('selectVariant', $vidBravo);
        $component->call('openEdit', $row->DID);

        $instance = $component->instance();

        $this->assertSame('FEE900', $instance->fee_id);
        $this->assertContains('FEE900', $instance->feeOptions->pluck('FeeID')->all());
        $this->assertNotNull($instance->selectedFund);

        // Re-saving keeps the fund instead of nulling it.
        $component->set('amount', '100')->call('save')->assertHasNoErrors();

        $this->assertSame('FEE900', $row->fresh()->FeeID);
        $this->assertEquals(100.0, (float) $row->fresh()->amount);
    }

    /**
     * Post / Void now ask through SweetAlert: the row action only raises the
     * confirmation dialog, and the shared handler's event does the change.
     */
    public function test_posting_and_voiding_are_gated_by_a_sweetalert_confirmation(): void
    {
        $admin = $this->seedAccess();

        $toPost = $this->createValidDisbursement();
        $toVoid = $this->createValidDisbursement(['amount' => 20]);

        $component = Livewire::actingAs($admin)->test(Disbursements::class);

        // Nothing changes until the dialog is confirmed.
        $component->call('confirmPost', $toPost->DID)->assertDispatched('swal:confirm');
        $this->assertSame(Disbursement::STATUS_UNPOSTED, (int) $toPost->fresh()->status);

        $component->call('postPending');
        $this->assertSame(Disbursement::STATUS_POSTED, (int) $toPost->fresh()->status);

        $component->call('confirmVoid', $toVoid->DID)->assertDispatched('swal:confirm');
        $this->assertSame(Disbursement::STATUS_UNPOSTED, (int) $toVoid->fresh()->status);

        $component->call('voidPending');
        $this->assertSame(Disbursement::STATUS_VOID, (int) $toVoid->fresh()->status);

        // A posted row is voided through the typed-reason dialog instead.
        $component->call('confirmVoid', $toPost->DID)->assertDispatched('swal:error');
    }

    /**
     * Audit trail: the creator is stored on create, shown in the list and
     * usable as a filter.
     */
    public function test_creating_records_the_creator_and_the_list_filters_by_creator(): void
    {
        $admin = $this->seedAccess();

        $other = User::create([
            'username' => 'second_admin', 'password' => 'password', 'lastname' => 'Second',
            'firstname' => 'Admin', 'RoleID' => 1, 'referralCode' => 'SMK002', 'status' => 'Active',
        ]);

        Ledger::create(['Source' => 'MF', 'RefID' => $this->fee->FeeID, 'amount' => 1000, 'status' => 'Completed']);

        Livewire::actingAs($admin)->test(Disbursements::class)
            ->set('showModal', true)
            ->set('fee_id', $this->fee->FeeID)
            ->set('amount', '100')
            ->call('save');

        Disbursement::create([
            'FeeID' => $this->fee->FeeID,
            'amount' => 50,
            'remarks' => 'recorded by the other admin',
            'CreatedBy' => $other->UserID,
        ]);

        $this->assertSame(
            $admin->UserID,
            (int) Disbursement::orderBy('DID')->first()->CreatedBy,
            'The signed-in user must be stamped as the creator.'
        );

        $component = Livewire::actingAs($admin)->test(Disbursements::class);

        $this->assertCount(2, $component->instance()->rows);
        $this->assertCount(2, $component->instance()->creatorOptions);

        $component->set('creatorFilter', (string) $other->UserID);

        $this->assertCount(1, $component->instance()->rows);
        $this->assertSame((int) $other->UserID, (int) $component->instance()->rows->first()->CreatedBy);
    }

    /**
     * Branch isolation: a non–System-Administrator only sees the variants
     * granted to their branch (users.branchCode → branches.BranchID →
     * branch_variant_grants). RoleID 1 stays unrestricted.
     */
    public function test_a_branch_only_sees_the_variants_granted_to_it(): void
    {
        $now = now();

        DB::table('roles')->updateOrInsert(['RoleID' => 2], [
            'rolename' => 'Administrator', 'status' => 1, 'created_at' => $now, 'updated_at' => $now,
        ]);

        $owner = User::create([
            'username' => 'branch_owner', 'password' => 'password', 'lastname' => 'Owner',
            'firstname' => 'Branch', 'RoleID' => 2, 'referralCode' => 'OWN001', 'status' => 'Active',
        ]);

        $branchOne = DB::table('branches')->insertGetId([
            'branchname' => 'Branch One', 'address' => '', 'status' => 1,
            'UserID' => $owner->UserID, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $branchTwo = DB::table('branches')->insertGetId([
            'branchname' => 'Branch Two', 'address' => '', 'status' => 1,
            'UserID' => $owner->UserID, 'created_at' => $now, 'updated_at' => $now,
        ]);

        $vidAlpha = DB::table('variants')->insertGetId([
            'name' => 'Alpha Variant', 'description' => '', 'status' => 'Active',
            'created_at' => $now, 'updated_at' => $now,
        ]);
        $vidBravo = DB::table('variants')->insertGetId([
            'name' => 'Bravo Variant', 'description' => '', 'status' => 'Active',
            'created_at' => $now, 'updated_at' => $now,
        ]);

        DB::table('membership_fees')->where('FeeID', $this->fee->FeeID)->update(['VID' => $vidAlpha]);
        MembershipFee::create(['FeeID' => 'FEE903', 'FeeName' => 'Bravo Fund', 'Cost' => 100]);
        DB::table('membership_fees')->where('FeeID', 'FEE903')->update(['VID' => $vidBravo]);

        // Branch One handles Alpha only; Branch Two handles Bravo only.
        DB::table('branch_variant_grants')->insert([
            ['BranchID' => $branchOne, 'VID' => $vidAlpha, 'status' => 'Active', 'created_at' => $now, 'updated_at' => $now],
            ['BranchID' => $branchTwo, 'VID' => $vidBravo, 'status' => 'Active', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $alphaRow = $this->createValidDisbursement(); // FEE900 → Alpha
        Disbursement::create(['FeeID' => 'FEE903', 'amount' => 25, 'remarks' => 'bravo payout']);

        $branchUser = User::create([
            'username' => 'branch_one_admin', 'password' => 'password', 'lastname' => 'One',
            'firstname' => 'Branch', 'RoleID' => 2, 'branchCode' => (string) $branchOne,
            'referralCode' => 'BR1001', 'status' => 'Active',
        ]);

        $component = Livewire::actingAs($branchUser)->test(Disbursements::class);

        $this->assertSame([$alphaRow->DID], $component->instance()->rows->pluck('DID')->all());
        $this->assertSame(['FEE900'], $component->instance()->feeOptions->pluck('FeeID')->all());
        $this->assertSame(['Alpha Variant'], $component->instance()->variantCards->pluck('name')->all());
        $this->assertSame($vidAlpha, $component->instance()->variantFilter); // its only card
        $this->assertEquals(150.5, $component->instance()->statistics['unposted']);

        // The System Administrator is exempt and sees every branch.
        $admin = $this->seedAccess();
        $all = Livewire::actingAs($admin)->test(Disbursements::class)->instance();

        $this->assertCount(2, $all->rows);
        $this->assertCount(2, $all->variantCards);
        // One card is always selected, so the creation list starts narrowed
        // to it (Alpha sorts first and both cards are empty).
        $this->assertSame(['FEE900'], $all->feeOptions->pluck('FeeID')->all());
        $this->assertEquals(175.5, $all->statistics['unposted']);
    }

    /**
     * Regression: saving from the modal with a manually chosen record date
     * used to blow up on a non-existent Illuminate\Support\Carbon\Carbon
     * class. Create and edit must both parse the date and persist it.
     */
    public function test_saving_with_a_custom_record_date_persists_it(): void
    {
        $admin = $this->seedAccess();

        Ledger::create(['Source' => 'MF', 'RefID' => $this->fee->FeeID, 'amount' => 1000, 'status' => 'Completed']);

        Livewire::actingAs($admin)->test(Disbursements::class)
            ->set('showModal', true)
            ->set('fee_id', $this->fee->FeeID)
            ->set('amount', '100')
            ->set('disbursement_date', '2026-10-01')
            ->call('save')
            ->assertHasNoErrors();

        $created = Disbursement::orderByDesc('DID')->first();
        $this->assertSame('2026-10-01', $created->created_at->format('Y-m-d'));

        // Editing the date while Unposted re-parses it too.
        Livewire::actingAs($admin)->test(Disbursements::class)
            ->set('showModal', true)
            ->set('editingDid', $created->DID)
            ->set('fee_id', $this->fee->FeeID)
            ->set('amount', '100')
            ->set('disbursement_date', '2026-10-05')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('2026-10-05', $created->fresh()->created_at->format('Y-m-d'));
    }

    /**
     * The top cards describe every created disbursement inside the branch
     * scope — selecting a variant card (or filtering) must not change them.
     */
    public function test_statistics_ignore_the_selected_variant_card_and_the_filters(): void
    {
        $admin = $this->seedAccess();
        $now = now();

        $vidAlpha = DB::table('variants')->insertGetId([
            'name' => 'Alpha Variant', 'description' => '', 'status' => 'Active',
            'created_at' => $now, 'updated_at' => $now,
        ]);
        $vidBravo = DB::table('variants')->insertGetId([
            'name' => 'Bravo Variant', 'description' => '', 'status' => 'Active',
            'created_at' => $now, 'updated_at' => $now,
        ]);

        DB::table('membership_fees')->where('FeeID', $this->fee->FeeID)->update(['VID' => $vidAlpha]);
        MembershipFee::create(['FeeID' => 'FEE903', 'FeeName' => 'Bravo Fund', 'Cost' => 100]);
        DB::table('membership_fees')->where('FeeID', 'FEE903')->update(['VID' => $vidBravo]);

        $this->createValidDisbursement(); // Alpha, 150.50 unposted
        Disbursement::create(['FeeID' => 'FEE903', 'amount' => 40, 'remarks' => 'bravo']); // Bravo, 40.00
        $this->createValidDisbursement(['amount' => 10])->update(['status' => Disbursement::STATUS_VOID]);

        $component = Livewire::actingAs($admin)->test(Disbursements::class);

        $this->assertEquals(190.5, $component->instance()->statistics['unposted']); // 150.50 + 40
        $this->assertEquals(10.0, $component->instance()->statistics['void']);

        $component->call('selectVariant', $vidAlpha);
        unset($component->instance()->statistics);

        // The table keeps every row (the card only scopes a new record), so
        // the statistics stay company-wide.
        $this->assertCount(3, $component->instance()->rows);
        $this->assertEquals(190.5, $component->instance()->statistics['unposted']);
    }

    /**
     * There is no "All Variants" card any more: the tab opens on the first
     * Active variant that still holds money (Variant 1 when both are live).
     */
    public function test_the_tab_opens_on_the_first_variant_that_still_holds_money(): void
    {
        $now = now();

        $vidAlpha = DB::table('variants')->insertGetId([
            'name' => 'Alpha Variant', 'description' => '', 'status' => 'Active',
            'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('variants')->insertGetId([
            'name' => 'Bravo Variant', 'description' => '', 'status' => 'Active',
            'created_at' => $now, 'updated_at' => $now,
        ]);

        // Alpha holds money, Bravo's fund is still empty.
        MembershipFee::create(['FeeID' => 'FEE901', 'FeeName' => 'Alpha Fund', 'Cost' => 100]);
        DB::table('membership_fees')->where('FeeID', 'FEE901')->update(['VID' => $vidAlpha]);
        MembershipFee::create(['FeeID' => 'FEE902', 'FeeName' => 'Bravo Fund', 'Cost' => 100]);
        DB::table('membership_fees')->where('FeeID', 'FEE902')
            ->update(['VID' => DB::table('variants')->where('name', 'Bravo Variant')->value('VID')]);

        Ledger::create(['Source' => 'MF', 'RefID' => 'FEE901', 'amount' => 500, 'status' => 'Completed']);

        $component = Livewire::test(Disbursements::class);

        $this->assertSame($vidAlpha, $component->instance()->variantFilter);
        $this->assertSame(['FEE901'], $component->instance()->feeOptions->pluck('FeeID')->all());

        // Once the second variant holds money too, the first card stays the
        // default (Variant 1) and its own funds stay in the dropdown.
        Ledger::create(['Source' => 'MF', 'RefID' => 'FEE902', 'amount' => 500, 'status' => 'Completed']);

        $again = Livewire::test(Disbursements::class)->instance();

        $this->assertSame($vidAlpha, $again->variantFilter);
        $this->assertSame(['FEE901'], $again->feeOptions->pluck('FeeID')->all());
    }

    /**
     * Card available follows the company collection formula:
     * MF + CF + TX − DR − OV − PF − CL − RF − WD − PK − DB.
     *
     * Money rows are attributed through ledgers.ActivationID -> activation
     * VID; rows that carry no VID (legacy activations, withdrawals,
     * pangkabuhayan) land on the first Active card, so the cards' total stays
     * equal to the whole-company figure.
     */
    public function test_card_available_follows_the_collection_formula(): void
    {
        $now = now();

        $vidAlpha = DB::table('variants')->insertGetId([
            'name' => 'Alpha Variant', 'description' => '', 'status' => 'Active',
            'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('variants')->insertGetId([
            'name' => 'Bravo Variant', 'description' => '', 'status' => 'Active',
            'created_at' => $now, 'updated_at' => $now,
        ]);

        DB::table('membership_fees')->where('FeeID', $this->fee->FeeID)->update(['VID' => $vidAlpha]);

        DB::table('roles')->updateOrInsert(['RoleID' => 4], [
            'rolename' => 'Member', 'status' => 1, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $member = User::create([
            'username' => 'formula_member', 'password' => 'password', 'lastname' => 'Formula',
            'firstname' => 'Member', 'RoleID' => 4, 'referralCode' => 'FRM001', 'status' => 'Active',
        ]);

        $attributed = \App\Models\Activation::create([
            'UserID' => $member->UserID, 'VID' => $vidAlpha, 'amount' => 100,
            'type' => 'W', 'payment_method' => 'Cash', 'upline_code' => 'FRM001',
        ]);
        $legacy = \App\Models\Activation::create([
            'UserID' => $member->UserID, 'VID' => null, 'amount' => 100,
            'type' => 'W', 'payment_method' => 'Cash', 'upline_code' => 'FRM001',
        ]);

        Ledger::create(['Source' => 'MF', 'RefID' => $this->fee->FeeID, 'amount' => 1000, 'status' => 'Completed']);

        // Money in: CF + TX (both attributed, both legacy).
        Ledger::create(['Source' => 'CF', 'RefID' => (string) $member->UserID, 'ActivationID' => $attributed->ActivationID, 'amount' => 20, 'status' => 'Completed']);
        Ledger::create(['Source' => 'TX', 'RefID' => (string) $member->UserID, 'ActivationID' => $legacy->ActivationID, 'amount' => 50, 'status' => 'Completed']);

        // Money out: DR attributed, OV + RF legacy, WD carries no activation.
        Ledger::create(['Source' => 'DR', 'RefID' => (string) $member->UserID, 'ActivationID' => $attributed->ActivationID, 'amount' => -300, 'status' => 'Completed']);
        Ledger::create(['Source' => 'OV', 'RefID' => (string) $member->UserID, 'ActivationID' => $legacy->ActivationID, 'amount' => -40, 'status' => 'Completed']);
        Ledger::create(['Source' => 'RF', 'RefID' => (string) $member->UserID, 'ActivationID' => $legacy->ActivationID, 'amount' => -10, 'status' => 'Completed']);
        Ledger::create(['Source' => 'WD', 'RefID' => (string) $member->UserID, 'amount' => -60, 'status' => 'Completed']);

        // Voided releases never leave the fund.
        Ledger::create(['Source' => 'PK', 'RefID' => (string) $member->UserID, 'amount' => -999, 'status' => 'Cancelled']);

        $cards = Livewire::test(Disbursements::class)->instance()->variantCards;
        $alpha = $cards->firstWhere('name', 'Alpha Variant');

        $this->assertSame(1000.0, $alpha['collected']);
        $this->assertEquals(410.0, $alpha['released']);      // 300 + 40 + 10 + 60
        $this->assertEquals(110.0, $alpha['releasedLegacy']); // 40 + 10 + 60
        $this->assertEquals(70.0, $alpha['otherIn']);        // 20 + 50
        $this->assertEquals(660.0, $alpha['available']);     // 1000 − 410 + 70

        // A disbursement out of the fund comes off the same figure.
        $this->createValidDisbursement(['amount' => 60]);

        $card = Livewire::test(Disbursements::class)->instance()->variantCards->firstWhere('name', 'Alpha Variant');
        $this->assertEquals(600.0, $card['available']);
    }
}
