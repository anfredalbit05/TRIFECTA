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

        // Carry over any control numbers already saved on users.
        // Written as a PHP loop (not UPDATE ... INNER JOIN) so the migration
        // runs on every driver MySQL supports in testing (SQLite) too.
        $controlByUser = DB::table('users')
            ->whereNotNull('control_number')
            ->pluck('control_number', 'UserID');

        foreach ($controlByUser as $userID => $controlNumber) {
            $latestApproved = DB::table('activations')
                ->where('UserID', $userID)
                ->where('status', 'Approved')
                ->orderByDesc('ActivationID')
                ->value('ActivationID');

            if ($latestApproved) {
                DB::table('activations')
                    ->where('ActivationID', $latestApproved)
                    ->update(['control_number' => $controlNumber]);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('control_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('control_number', 50)->nullable()->after('policy_number');
        });

        // Copy the newest control number per user back onto users.
        $controlPerUser = DB::table('activations')
            ->whereNotNull('control_number')
            ->orderByDesc('ActivationID')
            ->get(['UserID', 'control_number'])
            ->unique('UserID');

        foreach ($controlPerUser as $row) {
            DB::table('users')
                ->where('UserID', $row->UserID)
                ->update(['control_number' => $row->control_number]);
        }

        Schema::table('activations', function (Blueprint $table) {
            $table->dropColumn('control_number');
        });
    }
};
