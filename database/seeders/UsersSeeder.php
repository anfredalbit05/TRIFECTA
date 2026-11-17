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
        $users = [
            // =====================================
            // 1. SUPER ADMIN
            // =====================================
            [
                'username' => 'superadmin',
                'password' => 'SuperAdmin@2026',
                'lastname' => 'Admin',
                'firstname' => 'Super',
                'middlename' => null,
                'birthdate' => null,
                'contactno' => null,
                'address' => null,
                'email' => 'anfredalbit110525@gmail.com',
                'RoleID' => 1,
                'branchCode' => 1,
                'status' => 'Active',

            ],

            // =====================================
            // 2. CEO
            // =====================================
            [
                'username' => 'ceo',
                'password' => 'CEO@2026',
                'lastname' => 'Executive',
                'firstname' => 'Chief',
                'middlename' => null,
                'birthdate' => null,
                'contactno' => null,
                'address' => null,
                'email' => 'ceo@gmail.com',
                'RoleID' => 2,
                'branchCode' => 1,
                'status' => 'Active',
            ],

            // =====================================
            // 3. AGENCY
            // =====================================
            [
                'username' => 'agency',
                'password' => 'Agency@2026',
                'lastname' => 'Agency',
                'firstname' => 'Default',
                'middlename' => null,
                'birthdate' => null,
                'contactno' => null,
                'address' => null,
                'email' => 'agency@gmail.com',
                'RoleID' => 3,
                'branchCode' => 1,
                'status' => 'Active',
            ],

            // =====================================
            // 4. ADMIN
            // =====================================
            [
                'username' => 'admin',
                'password' => 'Admin@2026',
                'lastname' => 'Administrator',
                'firstname' => 'System',
                'middlename' => null,
                'birthdate' => null,
                'contactno' => null,
                'address' => null,
                'email' => 'admin@gmail.com',
                'RoleID' => 4,
                'branchCode' => 1,
                'status' => 'Active',
            ],

            // =====================================
            // 5. BRANCH MANAGER
            // =====================================
            [
                'username' => 'sanfranzbranchmanager',
                'password' => 'SanFranzBranch@2026',
                'lastname' => 'Manager',
                'firstname' => 'Branch',
                'middlename' => null,
                'birthdate' => null,
                'contactno' => null,
                'address' => null,
                'email' => 'sanfranzbranchmanager@gmail.com',
                'RoleID' => 5,
                'branchCode' => 1,
                'status' => 'Active',
            ],

            [
                'username' => 'digosbranchmanager',
                'password' => 'DigosBranch@2026',
                'lastname' => 'Manager',
                'firstname' => 'Branch',
                'middlename' => null,
                'birthdate' => null,
                'contactno' => null,
                'address' => null,
                'email' => 'digosbranchmanager@gmail.com',
                'RoleID' => 6,
            ],

            [
                'username' => 'matibranchmanager',
                'password' => 'MatiBranch@2026',
                'lastname' => 'Manager',
                'firstname' => 'Branch',
                'middlename' => null,
                'birthdate' => null,
                'contactno' => null,
                'address' => null,
                'email' => 'matibranchmanager@gmail.com',
                'RoleID' => 7,
            ],

            // =====================================
            // 6. TRIFECTA MEMBER
            // =====================================
            [
                'username' => 'member',
                'password' => 'Member@2026',
                'lastname' => 'Member',
                'firstname' => 'Default',
                'middlename' => null,
                'birthdate' => null,
                'contactno' => null,
                'address' => null,
                'email' => 'member@trifecta.test',
                'RoleID' => 8,
            ],
        ];

        foreach ($users as $user) {

            DB::table('users')->insert([

                // Account Information
                'username' => $user['username'],
                'password' => Hash::make($user['password']),

                // Personal Information
                'lastname' => $user['lastname'],
                'firstname' => $user['firstname'],
                'middlename' => $user['middlename'],

                'birthdate' => $user['birthdate'],
                'contactno' => $user['contactno'],
                'address' => $user['address'],

                // Email Information
                'email' => $user['email'],
                'email_verified_at' => null,

                // Profile Information
                'profilepic' => 'images/profiles/default-avatar.png',

                // Referral Information
                'referralCode' => strtoupper(Str::random(8)),
                'uplineCode' => null,
                'branchCode' => null,

                // Role Assignment
                'RoleID' => $user['RoleID'],

                // Account Status
                'status' => 'Active',
                'remember_token' => null,

                // Timestamps
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
