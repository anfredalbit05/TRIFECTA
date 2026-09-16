<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Links a ledger row to the Spin the Wheel cycle that produced it — the
     * Pangkabuhayan prize expenses written as Source 'PK' (one per winner).
     *
     * This lives in its own later migration (rather than in the historical
     * create_ledgers_table) because `spin_draws` did not exist yet when the
     * ledgers table was created.
     *
     * The hasColumn() guard keeps `php artisan migrate` safe on a database
     * where the column was already added by hand (see
     * database/sql/2026_09_16_spin_wheel.sql).
     */
    public function up(): void
    {
        if (Schema::hasColumn('ledgers', 'DrawID')) {
            return;
        }

        Schema::table('ledgers', function (Blueprint $table) {
            $table->unsignedBigInteger('DrawID')->nullable()->after('withdrawID');

            $table->foreign('DrawID')
                ->references('DrawID')
                ->on('spin_draws')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('ledgers', function (Blueprint $table) {
            $table->dropForeign(['DrawID']);
            $table->dropColumn('DrawID');
        });
    }
};
