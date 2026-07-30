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
        Schema::create('activation_histories', function (Blueprint $table) {

            $table->id('HistoryID');

            /*
            |--------------------------------------------------------------------------
            | Activation
            |--------------------------------------------------------------------------
            */

            $table->foreignId('ActivationID')
                ->constrained('activations', 'ActivationID')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Admin who processed the activation
            |--------------------------------------------------------------------------
            */

            $table->foreignId('AdminID')
                ->constrained('users', 'UserID')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'Approved',
                'Rejected',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Remarks
            |--------------------------------------------------------------------------
            */

            $table->text('remarks')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activation_histories');
    }
};
