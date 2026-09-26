<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Move control_number: activations own it (one per approved activation),
     * users no longer need the column added earlier today.
     */
    public function up(): void
    {
        Schema::table('activations', function (Blueprint $table) {
            $table->string('control_number', 50)->nullable()->after('status');
        });

        // Carry over any control numbers already saved on users
        DB::statement('
            UPDATE activations a
            INNER JOIN users u ON u.UserID = a.UserID
            SET a.control_number = u.control_number
            WHERE u.control_number IS NOT NULL
              AND a.status = "Approved"
              AND a.ActivationID = (
                  SELECT MAX(a2.ActivationID)
                  FROM activations a2
                  WHERE a2.UserID = a.UserID
                    AND a2.status = "Approved"
              )
        ');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('control_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('control_number', 50)->nullable()->after('policy_number');
        });

        DB::statement('
            UPDATE users u
            INNER JOIN activations a ON a.UserID = u.UserID
            SET u.control_number = a.control_number
            WHERE a.control_number IS NOT NULL
              AND a.ActivationID = (
                  SELECT MAX(a2.ActivationID)
                  FROM activations a2
                  WHERE a2.UserID = u.UserID
                    AND a2.control_number IS NOT NULL
              )
        ');

        Schema::table('activations', function (Blueprint $table) {
            $table->dropColumn('control_number');
        });
    }
};
