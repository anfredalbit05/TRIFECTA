<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Free (satellite-provisioned) members + the satellite an activation
     * belongs to.
     *
     * Both columns already exist on the live database (they were added by
     * hand while the satellite feature grew), so every add is guarded with
     * hasColumn() — the same pattern used by
     * 2026_09_16_140050_add_draw_id_to_ledgers_table.php — which keeps
     * `php artisan migrate` safe on both the live MySQL database and a
     * fresh database (tests run on SQLite).
     */
    public function up(): void
    {
        if (! Schema::hasColumn('activations', 'isFree')) {
            Schema::table('activations', function (Blueprint $table) {
                // True only for the free members provisioned when a satellite
                // activation is approved. Regular activations default to false.
                $table->boolean('isFree')->default(false);
            });
        }

        if (! Schema::hasColumn('activations', 'satelliteID')) {
            Schema::table('activations', function (Blueprint $table) {
                $table->unsignedBigInteger('satelliteID')->nullable();

                // SQLite ignores foreign key commands issued through ALTER
                // TABLE, so this constraint only materialises on MySQL —
                // which is exactly where the live table already has it.
                $table->foreign('satelliteID')
                    ->references('satelliteID')
                    ->on('satellites')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('activations', 'satelliteID')) {
            Schema::table('activations', function (Blueprint $table) {
                $table->dropForeign(['satelliteID']);
                $table->dropColumn('satelliteID');
            });
        }

        if (Schema::hasColumn('activations', 'isFree')) {
            Schema::table('activations', function (Blueprint $table) {
                $table->dropColumn('isFree');
            });
        }
    }
};
