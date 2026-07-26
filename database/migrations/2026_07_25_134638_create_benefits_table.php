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
        Schema::create('benefits', function (Blueprint $table) {

            $table->id('BentID');

            $table->string('name',150);

            $table->text('details')->nullable();

            $table->decimal('amount',12,2)->default(0);

            $table->enum('holdertype',[
                'Principal',
                'Dependent',
                'Both'
            ])->default('Principal');

            $table->boolean('status')->default(true);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('benefits');
    }
};