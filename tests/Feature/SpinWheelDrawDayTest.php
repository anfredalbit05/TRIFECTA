<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Services\SpinWheelService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class SpinWheelDrawDayTest extends TestCase
{
    use RefreshDatabase;

    private SpinWheelService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(SpinWheelService::class);
    }

    public function test_draw_day_falls_back_to_the_default_when_the_setting_is_missing(): void
    {
        $this->assertSame(SpinWheelService::DEFAULT_DRAW_DAY, $this->service->drawDay());
    }

    public function test_draw_day_reads_the_setting(): void
    {
        Setting::create(['name' => SpinWheelService::DRAW_DAY_SETTING, 'value' => '5']);

        $this->assertSame(5, $this->service->drawDay());
    }

    public function test_invalid_setting_values_fall_back_to_the_default(): void
    {
        foreach (['0', '29', '31', 'abc', '-3'] as $invalid) {
            Setting::where('name', SpinWheelService::DRAW_DAY_SETTING)->delete();
            Setting::create(['name' => SpinWheelService::DRAW_DAY_SETTING, 'value' => $invalid]);

            $this->assertSame(
                SpinWheelService::DEFAULT_DRAW_DAY,
                $this->service->drawDay(),
                "A setting value of {$invalid} must fall back to the default."
            );
        }
    }

    public function test_save_draw_day_persists_the_setting(): void
    {
        $this->service->saveDrawDay(7);

        $this->assertSame(7, $this->service->drawDay());
        $this->assertDatabaseHas('settings', [
            'name' => SpinWheelService::DRAW_DAY_SETTING,
            'value' => '7',
        ]);
    }

    public function test_save_draw_day_rejects_days_outside_1_to_28(): void
    {
        foreach ([0, -1, 29, 31] as $invalid) {
            $this->expectException(InvalidArgumentException::class);
            $this->service->saveDrawDay($invalid);
        }
    }

    public function test_active_draw_date_respects_the_configured_day(): void
    {
        Setting::create(['name' => SpinWheelService::DRAW_DAY_SETTING, 'value' => '5']);

        // After the 5th → this month's 5th is in force.
        $this->assertSame('2026-09-05', $this->service->activeDrawDate(Carbon::parse('2026-09-22'))->toDateString());

        // Before the 5th → last month's 5th is still in force.
        $this->assertSame('2026-08-05', $this->service->activeDrawDate(Carbon::parse('2026-09-04'))->toDateString());
    }

    public function test_collection_window_matches_the_configured_day(): void
    {
        Setting::create(['name' => SpinWheelService::DRAW_DAY_SETTING, 'value' => '5']);

        [$start, $end] = $this->service->periodFor(Carbon::parse('2026-09-05'));

        $this->assertSame('2026-08-05 00:00:00', $start->format('Y-m-d H:i:s'));
        $this->assertSame('2026-09-04 23:59:59', $end->format('Y-m-d H:i:s'));
    }

    public function test_schedule_description_uses_ordinals(): void
    {
        $this->assertSame(
            'The wheel is drawn every 19th — cycles cover the 19th of the previous month to the 18th.',
            SpinWheelService::describeSchedule(19)
        );

        $this->assertSame(
            'The wheel is drawn every 1st — cycles cover the whole previous month.',
            SpinWheelService::describeSchedule(1)
        );

        $this->assertSame(
            'The wheel is drawn every 3rd — cycles cover the 3rd of the previous month to the 2nd.',
            SpinWheelService::describeSchedule(3)
        );
    }

    public function test_schedule_description_clamps_out_of_range_days(): void
    {
        $this->assertSame(SpinWheelService::describeSchedule(28), SpinWheelService::describeSchedule(31));
        $this->assertSame(SpinWheelService::describeSchedule(1), SpinWheelService::describeSchedule(0));
    }
}
