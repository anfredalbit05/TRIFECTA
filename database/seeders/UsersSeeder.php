<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'username' => 'superadmin',
                'password' => Hash::make('Admin@2026'),

                'lastname' => 'Administrator',
                'firstname' => 'Super',
                'middlename' => null,

                'birthdate' => '1990-01-01',

                'contactno' => '09123456789',
                'address' => 'Head Office',

                'email' => 'dtechmsolution@gmail.com',
                'email_verified_at' => now(),

                'referralCode' => 'SUPERADMIN001',
                'uplineCode' => null,

                'branchCode' => null,

                'RoleID' => 1,

                'status' => 'Active',

                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}