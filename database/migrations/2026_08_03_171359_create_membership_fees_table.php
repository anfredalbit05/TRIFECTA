<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('membership_fees', function (Blueprint $table) {
            $table->string('FeeID')->primary();
            $table->string('FeeName');
            $table->unsignedBigInteger('VID')->nullable();
            $table->double('Cost')->nullable()->default(0);
            $table->timestamps();

            $table->foreign('VID', 'membership_fees_variantid_foreign')
                ->references('VID')->on('variants')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_fees');
    }
};
