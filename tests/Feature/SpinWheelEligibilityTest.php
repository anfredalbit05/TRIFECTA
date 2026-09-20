<?php

namespace Tests\Feature;

use App\Models\Activation;
use App\Models\Ledger;
use App\Models\User;
use App\Services\SpinWheelService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SpinWheelEligibilityTest extends TestCase
{
    use RefreshDatabase;

    /** Cycle window of e.g. the September 12 draw. */
    private const START = '2026-08-12 00:00:00';

    private const END = '2026-09-11 23:59:59';

    private SpinWheelService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // The users.RoleID foreign key needs its targets in place.
        DB::table('roles')->insert([
            ['RoleID' => 1, 'rolename' => 'Super Administrator', 'created_at' => now(), 'updated_at' => now()],
            ['RoleID' => 4, 'rolename' => 'Member', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->service = app(SpinWheelService::class);
    }

    public function test_uplines_and_their_activated_downlines_share_the_wheel(): void
    {
        // Upline U invited D (D was activated); D then invited E (E was activated).
        $upline = $this->member('Uplineson', 'Uline', 'UP1');
        $downline = $this->member('Downson', 'Dline', 'DN1', 'UP1');
        $grand = $this->member('Grandson', 'Gline', 'GR1', 'DN1');

        $this->activate($downline, 'UP1'); // U earns 1 · D earns 1 (own activation)
        $this->activate($grand, 'DN1');    // D earns 1 more · E earns 1

        $eligible = $this->eligible();

        $this->assertSame(3, $eligible->count());
        $this->assertSame(1, $eligible[$upline->UserID]['invites']);
        // The activated downline qualifies through both routes: their own
        // activation plus the activation they invited — merged into one entry.
        $this->assertSame(2, $eligible[$downline->UserID]['invites']);
        $this->assertSame(1, $eligible[$grand->UserID]['invites']);
    }

    public function test_self_referral_counts_the_member_once(): void
    {
        $member = $this->member('Selfson', 'Self', 'SE1');

        // Uplines earn nothing from self-referrals, but the activated member
        // themselves still gets their wheel spot.
        $this->activate($member, 'SE1');

        $eligible = $this->service->eligibleMembers(
            Carbon::parse(self::START),
            Carbon::parse(self::END)
        );

        $this->assertSame(1, $eligible->count());
        $this->assertSame($member->UserID, $eligible[0]['user']->UserID);
        $this->assertSame(1, $eligible[0]['invites']);
    }

    public function test_staff_accounts_never_appear_on_the_wheel_when_members_only(): void
    {
        $upline = $this->member('Uplineson', 'Uline', 'UP1');
        $staff = $this->member('Staffson', 'Staff', 'ST1', 'UP1', RoleID: 1);
        $member = $this->member('Memberson', 'Member', 'MB1', 'ST1');

        $this->activate($staff, 'UP1');  // Upline U earns a spot for activating staff
        $this->activate($member, 'ST1'); // ...but staff never earn spots themselves

        $eligible = $this->eligible();

        $this->assertSame(2, $eligible->count());
        $this->assertArrayHasKey($upline->UserID, $eligible);
        $this->assertArrayHasKey($member->UserID, $eligible);
        $this->assertArrayNotHasKey($staff->UserID, $eligible);
    }

    public function test_staff_downlines_join_the_wheel_when_members_only_is_off(): void
    {
        $staff = $this->member('Staffson', 'Staff', 'ST1');

        $this->activate($staff, '');

        $eligible = $this->service->eligibleMembers(
            Carbon::parse(self::START),
            Carbon::parse(self::END),
            membersOnly: false
        );

        $this->assertSame(1, $eligible->count());
        $this->assertSame($staff->UserID, $eligible[0]['user']->UserID);
    }

    public function test_activations_outside_the_window_do_not_qualify(): void
    {
        $upline = $this->member('Uplineson', 'Uline', 'UP1');
        $downline = $this->member('Downson', 'Dline', 'DN1', 'UP1');

        // One day before the window opens and one day after it closes.
        $this->activate($downline, 'UP1', at: '2026-08-11 23:59:59');

        $this->assertSame(0, $this->eligible()->count());
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /**
     * The window's eligible members, keyed by UserID for easy assertions.
     */
    private function eligible()
    {
        return $this->service->eligibleMembers(
            Carbon::parse(self::START),
            Carbon::parse(self::END)
        )->keyBy(fn ($row) => $row['user']->UserID);
    }

    private function member(
        string $lastname,
        string $firstname,
        string $code,
        ?string $upline = null,
        int $RoleID = 4
    ): User {
        return User::create([
            'username' => strtolower($firstname.'_'.$code),
            'password' => bcrypt('password'),
            'lastname' => $lastname,
            'firstname' => $firstname,
            'RoleID' => $RoleID,
            'referralCode' => $code,
            'uplineCode' => $upline,
            'email' => strtolower($code).'@test.local',
        ]);
    }

    /**
     * Approve an activation and write its Pangkabuhayan share, the same way
     * the activation review flow does — that Completed FEE004 row is what
     * both the pool and the wheel are built from.
     */
    private function activate(User $member, string $uplineCode, string $at = '2026-08-20 10:00:00'): Activation
    {
        $activation = Activation::create([
            'UserID' => $member->UserID,
            'amount' => 1250.00,
            'type' => 'W',
            'payment_method' => 'Cash',
            'upline_code' => $uplineCode,
            'status' => 'Approved',
        ])->forceFill(['created_at' => $at, 'updated_at' => $at]);
        $activation->save();

        (new Ledger([
            'RefID' => SpinWheelService::PANGKABUHAYAN_FEE_ID,
            'amount' => 50.00,
            'Source' => 'MF',
            'ActivationID' => $activation->ActivationID,
            'status' => 'Completed',
        ]))->forceFill(['created_at' => $at, 'updated_at' => $at])->save();

        return $activation;
    }
}
