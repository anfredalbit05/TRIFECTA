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
        Schema::create('wallets', function (Blueprint $table) {

            $table->id('WalletID');

            $table->foreignId('UserID')
                ->constrained('users', 'UserID')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->decimal('amount', 12, 2)->default(0);
            $table->enum('source', [
                'DR', // Direct Referral
                'OV', // Overrides
            ])->nullable();

            $table->foreignId('ActivationID')
                ->nullable()
                ->constrained('activations', 'ActivationID')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->enum('status', [
                'Active',
                'Inactive',
            ])->default('Active');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
