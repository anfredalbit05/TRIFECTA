<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRoleId = DB::table('roles')
            ->where('rolename', 'Super Administrator')
            ->value('RoleID');

        $administratorRoleId = DB::table('roles')
            ->where('rolename', 'Administrator')
            ->value('RoleID');

        $botRoleId = DB::table('roles')
            ->where('rolename', 'BOT')
            ->value('RoleID');

        DB::table('users')->insert([

            // Super Administrator
            [
                'username' => 'sysadmin.ebs',
                'password' => Hash::make('Admin@c0nf3d3nt!47'),

                'lastname' => 'Administrator',
                'firstname' => 'Super',
                'middlename' => null,

                'birthdate' => '1992-07-01',

                'contactno' => '09978251816',
                'address' => 'Head Office',

                'email' => 'dtechmsolution@gmail.com',
                'email_verified_at' => now(),

                'profilepic' => 'profiles/default-avatar.png',

                'referralCode' => strtoupper(Str::random(8)),
                'uplineCode' => null,

                'branchCode' => null,

                'RoleID' => $superAdminRoleId,

                'status' => 'Active',

                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'sysadmin.albit',
                'password' => Hash::make('Admin@2026'),

                'lastname' => 'Albit',
                'firstname' => 'Anfrid',
                'middlename' => null,

                'birthdate' => '1990-01-01',

                'contactno' => '09123456789',
                'address' => 'Head Office',

                'email' => 'anfredalbit110525@gmail.com',
                'email_verified_at' => now(),

                'profilepic' => 'profiles/default-avatar.png',

                'referralCode' => strtoupper(Str::random(8)),
                'uplineCode' => null,

                'branchCode' => null,

                'RoleID' => $superAdminRoleId,

                'status' => 'Active',

                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],

            // CEO
            [
                'username' => 'renante.robles',
                'password' => Hash::make('Admin@2026'),

                'lastname' => 'Robles, MPA, LFA',
                'firstname' => 'Renante',
                'middlename' => 'Suaybaguio',

                'birthdate' => '1975-05-11',

                'contactno' => '09665622912',
                'address' => null,

                'email' => 'selboretnan@gmail.com',
                'email_verified_at' => now(),

                'profilepic' => 'profiles/default-avatar.png',

                'referralCode' => strtoupper(Str::random(8)),
                'uplineCode' => null,

                'branchCode' => null,

                'RoleID' => $administratorRoleId,

                'status' => 'Active',

                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],

            // COO
            [
                'username' => 'bobby.dagala',
                'password' => Hash::make('Admin@2026'),

                'lastname' => 'Dagala',
                'firstname' => 'Bobby',
                'middlename' => 'Paulo',

                'birthdate' => '1983-12-17',

                'contactno' => '09278381647',

                'address' => null,

                'email' => 'theroyalicon2026@gmail.com',
                'email_verified_at' => now(),

                'profilepic' => 'profiles/default-avatar.png',

                'referralCode' => strtoupper(Str::random(8)),
                'uplineCode' => null,

                'branchCode' => null,

                'RoleID' => $administratorRoleId,

                'status' => 'Active',

                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Secretary
            [
                'username' => 'princes.manliguez',
                'password' => Hash::make('Admin@2026'),

                'lastname' => 'Manliguez',
                'firstname' => 'Princes',
                'middlename' => 'Pugasan',

                'birthdate' => '2003-05-04',

                'contactno' => '09266958032',

                'address' => null,

                'email' => 'manliguezprinces00@gmail.com',
                'email_verified_at' => now(),

                'profilepic' => 'profiles/default-avatar.png',

                'referralCode' => strtoupper(Str::random(8)),
                'uplineCode' => null,

                'branchCode' => null,

                'RoleID' => $botRoleId,

                'status' => 'Active',

                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Treasurer
            [
                'username' => 'cherry.dagala',
                'password' => Hash::make('Admin@2026'),

                'lastname' => 'Dagala',
                'firstname' => 'Cherry Lynn',
                'middlename' => 'Nierras',

                'birthdate' => '1977-03-26',

                'contactno' => '09055596558',

                'address' => null,

                'email' => 'ccnierras1977@gmail.com',
                'email_verified_at' => now(),

                'profilepic' => 'profiles/default-avatar.png',

                'referralCode' => strtoupper(Str::random(8)),
                'uplineCode' => null,

                'branchCode' => null,

                'RoleID' => $botRoleId,

                'status' => 'Active',

                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Auditor
            [
                'username' => 'maria.robles',
                'password' => Hash::make('Admin@2026'),

                'lastname' => 'Robles',
                'firstname' => 'Dr. Maria Carmelita',
                'middlename' => 'Dalisay',

                'birthdate' => null,

                'contactno' => '09565003849',

                'address' => null,

                'email' => 'mariacdrobles@gmail.com',
                'email_verified_at' => now(),

                'profilepic' => 'profiles/default-avatar.png',

                'referralCode' => strtoupper(Str::random(8)),
                'uplineCode' => null,

                'branchCode' => null,

                'RoleID' => $botRoleId,

                'status' => 'Active',

                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Board of Directors
            [
                'username' => 'alfe.duron',
                'password' => Hash::make('Admin@2026'),

                'lastname' => 'Duron',
                'firstname' => 'Alfe',
                'middlename' => 'Sinoy',

                // Source only provides "09/19/19", which is ambiguous.
                'birthdate' => null,

                'contactno' => '09933668594',

                'address' => null,

                'email' => 'alfeduron09@gmail.com',
                'email_verified_at' => now(),

                'profilepic' => 'profiles/default-avatar.png',

                'referralCode' => strtoupper(Str::random(8)),
                'uplineCode' => null,

                'branchCode' => null,

                'RoleID' => $botRoleId,

                'status' => 'Active',

                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
