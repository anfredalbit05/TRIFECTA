<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Appends the 'DB' (Disbursement) source to the ledgers.Source enum.
     *
     * - MySQL live table: MODIFY the enum, appending 'DB' at the end so
     *   existing values keep their meaning and 32k+ rows stay intact.
     * - SQLite (test suite): enum() is a VARCHAR + CHECK constraint; the
     *   native ->change() rebuild keeps the check in step without any
     *   MySQL-only syntax (same lesson as the move-control-number
     *   migration — no raw INNER JOIN / MODIFY-only statements).
     */
    public function up(): void
    {
        Schema::table('ledgers', function (Blueprint $table) {
            $table->enum('Source', [
                'MF',  // Membership Fee
                'DR',  // Direct Referral
                'OV',  // Overrides
                'CL',  // Claim
                'RF',  // Refund
                'PF',  // Provident Fund
                'WD',  // Withdrawal
                'TX',  // Transaction Fee
                'CF',  // Convenience Fee
                'PK',  // Pangkabuhayan (Spin the Wheel prize expense)
                'OT',  // Others
                'SF',  // Satellite Fund (satellite activation split, 90%)
                'CS',  // Company Share (satellite activation split, 10%)
                'SDR', // Satellite Direct Referral (free member, tier-based)
                'DB',  // Disbursement (money paid out of a collected fee)
            ])->default('OT')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('ledgers', function (Blueprint $table) {
            $table->enum('Source', [
                'MF', 'DR', 'OV', 'CL', 'RF', 'PF', 'WD',
                'TX', 'CF', 'PK', 'OT', 'SF', 'CS', 'SDR',
            ])->default('OT')->nullable(false)->change();
        });
    }
};
