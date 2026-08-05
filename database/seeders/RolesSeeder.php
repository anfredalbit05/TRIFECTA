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
                'description'=> 'Overall system management for CEO and COO',
                'status'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'rolename'   => 'BOT',
                'description'=> 'Overall system management with limited privileges',
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
            [
                'rolename'   => 'Executive Management',
                'description'=> 'Executive management with limited privileges',
                'status'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'rolename'   => 'Agency',
                'description'=> 'Agency with limited privileges',
                'status'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'rolename'   => 'Human Resource',
                'description'=> 'Human Resource with limited privileges',
                'status'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
