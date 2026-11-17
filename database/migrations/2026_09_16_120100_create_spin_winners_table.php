<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Every winner produced by a Spin the Wheel cycle.
     *
     * UserID / DrawnBy are intentionally plain indexed columns (no foreign
     * keys): users can be hard-deleted from the masterlist and a deleted
     * account must never break — or silently cascade away — the draw history.
     */
    public function up(): void
    {
        if (Schema::hasTable('spin_winners')) {
            return;
        }

        Schema::create('spin_winners', function (Blueprint $table) {
            $table->id('WinnerID');

            $table->unsignedBigInteger('DrawID');
            $table->unsignedBigInteger('UserID');

            $table->unsignedInteger('spin_order');
            $table->unsignedInteger('qualified_invites')->default(0);

            // Prize snapshot at the moment of the win.
            $table->string('prize_name')->nullable();
            $table->decimal('prize_amount', 12, 2)->nullable();

            $table->unsignedBigInteger('DrawnBy')->nullable();

            $table->timestamps();

            $table->foreign('DrawID')
                ->references('DrawID')
                ->on('spin_draws')
                ->cascadeOnDelete();

            $table->index(['DrawID', 'spin_order']);
            $table->index('UserID');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spin_winners');
    }
};
