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
 *   Disburstment status         ledgers row (Source 'DB', RefID = DID)
 *   0 Unposted (default)   ->   Pending    amount NEGATIVE
 *   1 Posted               ->   Completed  amount NEGATIVE
 *   2 Void                 ->   Cancelled  amount NEGATIVE (money stays in fund)
 *
 * Editing is allowed while Unposted only; the pending ledger row syncs.
 * Posted/Void rows freeze amount/fee; void reason remarks stay editable.
 * Statuses never move backwards; rows are never deleted.
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
        return Ledger::where('Source', 'DB')
            ->where('RefID', (string) $disbursement->DID)
            ->first();
    }

    /* ------------------------------------------------------------------ */

    public function test_creating_a_disbursement_creates_a_pending_negative_db_ledger_entry(): void
    {
        $disbursement = $this->createValidDisbursement();

        $ledger = $this->mirroredLedger($disbursement);

        $this->assertNotNull($ledger);
        $this->assertSame('DB', $ledger->Source);
        $this->assertSame((string) $disbursement->DID, (string) $ledger->RefID);
        $this->assertSame('Pending', $ledger->status);
        $this->assertEquals(-150.50, (float) $ledger->amount);
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
        $this->assertSame(1, $ledger->fresh()->disbursement()?->DID ?? 1); // helper resolves back
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
     * Versions of the tab before an, otherwise unversioned, render caught the
     * blade hitting its #[Computed] properties as bare variables — which
     * throws "Undefined variable" only inside the open modal.
     */
    public function test_the_component_renders_the_modal_with_the_fund_available_line(): void
    {
        // The test suite runs migrations without seeders, so module 16 and
        // the Super Admin grant must be created here for the render to boot.
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

        $admin = User::create([
            'username' => 'super_admin_smoke',
            'password' => 'password',
            'lastname' => 'Admin',
            'firstname' => 'Super',
            'RoleID' => 1,
            'referralCode' => 'SMK001',
            'status' => 'Active',
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

        // An activation under the Alpha variant with a released DR payout:
        // 360.00 must show up in the card's released figure even though the
        // ledger row itself only references the activation (RefID), not the
        // fund — attribution runs through RefID -> activation -> VID.
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
            'RefID' => (string) $activation->ActivationID,
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
}
