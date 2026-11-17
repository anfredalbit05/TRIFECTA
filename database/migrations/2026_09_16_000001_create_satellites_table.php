<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the `satellites` table used by the Satellite membership
     * feature. Mirrors the table that was created manually on the live
     * database (MariaDB defaults included).
     */
    public function up(): void
    {
        if (Schema::hasTable('satellites')) {
            return;
        }

        Schema::create('satellites', function (Blueprint $table) {
            $table->bigIncrements('satelliteID');

            $table->string('satellite_name');
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Owner account of the satellite office
            $table->unsignedBigInteger('UserID')->nullable()->index();

            // 1 = Bronze, 2 = Silver, 3 = Gold
            $table->unsignedBigInteger('satellite_typeID')->nullable()->index();

            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });

        // activations.satelliteID points here. The column itself is declared in
        // create_activations_table; the constraint is attached from this
        // migration because `satellites` only exists from here on.
        $hasConstraint = collect(Schema::getForeignKeys('activations'))->contains(
            fn (array $foreignKey) => in_array('satelliteID', $foreignKey['columns'], true)
        );

        if (! $hasConstraint && DB::getDriverName() === 'mysql') {
            Schema::table('activations', function (Blueprint $table) {
                $table->foreign('satelliteID', 'fk_activations_satellite')
                    ->references('satelliteID')->on('satellites')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('satellites');
    }
};
