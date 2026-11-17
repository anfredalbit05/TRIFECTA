<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'RoleID' => 1,
                'rolename' => 'Super Admin',
                'description' => 'Has complete access and control over the entire system.',
            ],
            [
                'RoleID' => 2,
                'rolename' => 'CEO',
                'description' => 'Chief Executive Officer with executive-level system access.',
            ],
            [
                'RoleID' => 3,
                'rolename' => 'Agency',
                'description' => 'Agency personnel with access to assigned agency operations.',
            ],
            [
                'RoleID' => 4,
                'rolename' => 'Admin',
                'description' => 'Manages overall system operations and administrative functions.',
            ],
            [
                'RoleID' => 5,
                'rolename' => 'Branch Manager - San Franz',
                'description' => 'Manages San Franz branch operations, personnel, and records.',
            ],
            [
                'RoleID' => 6,
                'rolename' => 'Branch Manager - Digos',
                'description' => 'Manages Digos branch operations, personnel, and records.',
            ],
            [
                'RoleID' => 7,
                'rolename' => 'Branch Manager - Mati',
                'description' => 'Manages Mati branch operations, personnel, and records.',
            ],
            [
                'RoleID' => 8,
                'rolename' => 'TRIFECTA Member',
                'description' => 'Regular TRIFECTA organization member.',
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['RoleID' => $role['RoleID']],
                [
                    'rolename' => $role['rolename'],
                    'description' => $role['description'],
                    'status' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}

