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

            // Kept in step with the live `ledgers` table: WD / TX / CF / PK
            // were added by hand as the app grew (withdrawals, transaction
            // fees, PayMaya convenience fee, Pangkabuhayan spin prizes).
            $table->enum('Source', [
                'MF', // Membership Fee
                'DR', // Direct Referral
                'OV', // Overrides
                'CL', // Claim
                'RF', // Refund
                'PF', // Provident Fund
                'WD', // Withdrawal
                'TX', // Transaction Fee
                'CF', // Convenience Fee
                'PK', // Pangkabuhayan (Spin the Wheel prize expense)
                'OT', // Others
                'SF', // Satellite Fund (satellite activation split, 90%)
                'CS', // Company Share (satellite activation split, 10%)
                'SDR', // Satellite Direct Referral (free member, tier-based)
                'DB', // Disbursement (money paid out of a collected fee)
            ])->default('OT');

            $table->foreignId('ActivationID')
                ->nullable()
                ->constrained('activations', 'ActivationID')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('withdrawID')->nullable();

            // Spin the Wheel cycle that produced a Pangkabuhayan prize row.
            // Its foreign key is attached by 2026_09_16_140050 once
            // `spin_draws` exists.
            $table->unsignedBigInteger('DrawID')->nullable();

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
