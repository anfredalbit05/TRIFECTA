<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('satellites');
    }
};
