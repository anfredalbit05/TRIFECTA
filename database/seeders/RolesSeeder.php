<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'rolename'   => 'Super Administrator',
                'description'=> 'Has full access to the entire system.',
                'status'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'rolename'   => 'Administrator',
                'description'=> 'Manages users, modules, and system settings.',
                'status'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'rolename'   => 'Branch Manager',
                'description'=> 'Manages branch operations and staff.',
                'status'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'rolename'   => 'Branch Staff',
                'description'=> 'Processes member and benefit transactions.',
                'status'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'rolename'   => 'Member',
                'description'=> 'Regular Dayongan member.',
                'status'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}