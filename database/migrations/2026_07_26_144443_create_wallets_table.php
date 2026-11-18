
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
                'DR',  // Direct Referral
                'OV',  // Overrides
                'PF',  // Provident Fund
                'WD',  // Withdrawal
                'CS',  // Company Share
                'SDR', // Satellite Direct Referral
            ])->nullable();

            // Removed ->after('source') to prevent MySQL CREATE TABLE error
            $table->boolean('isWithdrawable')->default(true);

            $table->foreignId('ActivationID')
                ->nullable()
                ->constrained('activations', 'ActivationID')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->unsignedBigInteger('withdrawID')->nullable();

            $table->enum('status', [
                'Active',
                'Inactive',
            ])->default('Active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
