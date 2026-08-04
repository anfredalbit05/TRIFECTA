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
        Schema::create('ledgers', function (Blueprint $table) {

            $table->id('TransactionID');

            // Membership Fee Reference
            $table->string('RefID');

            $table->decimal('amount', 12, 2);

            $table->enum('Source', [
                'MF', // Membership Fee
                'DR', // Direct Referral
                'OV', // Overrides
                'CL', // Claim
                'RF', // Refund
                'OT', // Others
            ])->default('OT');

            $table->foreignId('ActivationID')
                ->nullable()
                ->constrained('activations', 'ActivationID')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->enum('status', [
                'Pending',
                'Completed',
                'Cancelled',
            ])->default('Completed');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};