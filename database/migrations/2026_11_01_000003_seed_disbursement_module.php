<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Registers the Disbursement module (ModuleID 16) and grants every
     * action to the Super Administrator role (RoleID 1), following the
     * same pattern as the 15 seeded modules. Other roles can be granted
     * access from Masterlist > Admin Users > Privileges.
     */
    public function up(): void
    {
        $now = now();

        DB::table('modules')->updateOrInsert(
            ['ModuleID' => 16],
            [
                'modulename' => 'Transaction List - Disbursement',
                'description' => 'Manage disbursements paid out of collected membership fees',
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        // RoleID 1 = Super Administrator, full access like every other module.
        // Guarded: the SQLite test suite runs migrations WITHOUT seeders, so
        // roles may not exist there — the grant only applies when RoleID 1
        // does. Other roles get access from Masterlist > Admin Users.
        if (DB::table('roles')->where('RoleID', 1)->exists()) {
            DB::table('privileges')->updateOrInsert(
                ['RoleID' => 1, 'ModuleID' => 16],
                [
                    'can_create' => 1,
                    'can_read' => 1,
                    'can_update' => 1,
                    'can_delete' => 1,
                    'can_print' => 1,
                    'can_change_status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('privileges')->where('ModuleID', 16)->delete();
        DB::table('modules')->where('ModuleID', 16)->delete();
    }
};
