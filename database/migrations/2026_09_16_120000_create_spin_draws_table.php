<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per monthly Spin the Wheel cycle.
     *
     * The cycle is identified by its draw date — the 12th of the month —
     * and covers the Pangkabuhayan collections / invitations from the
     * 12th of the previous month up to the day before the draw.
     */
    public function up(): void
    {
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

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spin_draws');
    }
};
