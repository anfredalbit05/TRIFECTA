<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per monthly Spin the Wheel cycle.
     *
     * The cycle is identified by its draw date — the configured draw day
     * (`spin_draw_day` setting) of the month — and covers the Pangkabuhayan
     * collections / invitations from that day of the previous month up to
     * the day before the draw.
     */
    public function up(): void
    {
        if (Schema::hasTable('spin_draws')) {
            return;
        }

        Schema::create('spin_draws', function (Blueprint $table) {
            $table->id('DrawID');

            $table->date('draw_date')->unique();
            $table->date('period_start');
            $table->date('period_end');

            // Snapshot of the figures used for this cycle so a later
            // collection or settings change cannot rewrite history.
            $table->decimal('pangkabuhayan_total', 12, 2)->default(0);
            $table->string('prize_name')->default('5 kls Rice');
            $table->decimal('prize_amount', 12, 2)->default(275);
            $table->unsignedInteger('winners_target')->default(0);
            $table->boolean('allow_repeat_winners')->default(true);

            $table->enum('status', ['Open', 'Completed'])->default('Open');
            $table->timestamp('completed_at')->nullable();

            // News feed announcement that lists this cycle's winners.
            $table->unsignedBigInteger('AnnouncementID')->nullable();
            $table->index('AnnouncementID');

            $table->timestamps();
        });

        // ledgers.DrawID links a Pangkabuhayan prize row to this cycle. The
        // column is declared in create_ledgers_table; the constraint is
        // attached here because `spin_draws` only exists from here on.
        $hasConstraint = collect(Schema::getForeignKeys('ledgers'))->contains(
            fn (array $foreignKey) => in_array('DrawID', $foreignKey['columns'], true)
        );

        if (! $hasConstraint && DB::getDriverName() === 'mysql') {
            Schema::table('ledgers', function (Blueprint $table) {
                $table->foreign('DrawID', 'ledgers_drawid_foreign')
                    ->references('DrawID')->on('spin_draws')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('spin_draws');
    }
};
