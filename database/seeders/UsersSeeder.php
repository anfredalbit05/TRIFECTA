<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // Generate referral codes first.
            $users = [
                [
                    'username' => 'superadmin',
                    'password' => 'SuperAdmin@2026',
                    'lastname' => 'Admin',
                    'firstname' => 'Super',
                    'email' => 'anfredalbit110525@gmail.com',
                    'RoleID' => 1,
                    'branchCode' => 1,
                ],
                [
                    'username' => 'ceo',
                    'password' => 'CEO@2026',
                    'lastname' => 'Executive',
                    'firstname' => 'Chief',
                    'email' => 'ceo@gmail.com',
                    'RoleID' => 2,
                    'branchCode' => 1,
                ],
                [
                    'username' => 'agency',
                    'password' => 'Agency@2026',
                    'lastname' => 'Agency',
                    'firstname' => 'Default',
                    'email' => 'agency@gmail.com',
                    'RoleID' => 3,
                    'branchCode' => 1,
                ],
                [
                    'username' => 'admin',
                    'password' => 'Admin@2026',
                    'lastname' => 'Administrator',
                    'firstname' => 'System',
                    'email' => 'admin@gmail.com',
                    'RoleID' => 4,
                    'branchCode' => 1,
                ],
                [
                    'username' => 'sanfranzbranchmanager',
                    'password' => 'SanFranzBranch@2026',
                    'lastname' => 'Manager',
                    'firstname' => 'Branch',
                    'email' => 'sanfranzbranchmanager@gmail.com',
                    'RoleID' => 5,
                    'branchCode' => 1,
                ],
                [
                    'username' => 'digosbranchmanager',
                    'password' => 'DigosBranch@2026',
                    'lastname' => 'Manager',
                    'firstname' => 'Branch',
                    'email' => 'digosbranchmanager@gmail.com',
                    'RoleID' => 6,
                    'branchCode' => null,
                ],
                [
                    'username' => 'matibranchmanager',
                    'password' => 'MatiBranch@2026',
                    'lastname' => 'Manager',
                    'firstname' => 'Branch',
                    'email' => 'matibranchmanager@gmail.com',
                    'RoleID' => 7,
                    'branchCode' => null,
                ],
            ];

            // Generate a unique referral code for every user.
            foreach ($users as &$user) {
                do {
                    $code = strtoupper(Str::random(8));
                } while (DB::table('users')
                    ->where('referralCode', $code)
                    ->exists());

                $user['referralCode'] = $code;
            }
            unset($user);

            // Establish the referral hierarchy.
            $users[0]['uplineCode'] = null;                         // Super Admin
            $users[1]['uplineCode'] = null;   // CEO
            $users[2]['uplineCode'] = $users[1]['referralCode'];   // Agency
            $users[3]['uplineCode'] = $users[2]['referralCode'];   // Admin

            // All three Branch Managers belong under Admin.
            $users[4]['uplineCode'] = $users[3]['referralCode'];
            $users[5]['uplineCode'] = $users[3]['referralCode'];
            $users[6]['uplineCode'] = $users[3]['referralCode'];

            foreach ($users as $user) {
                DB::table('users')->insert([
                    'username' => $user['username'],
                    'password' => Hash::make($user['password']),

                    'lastname' => $user['lastname'],
                    'firstname' => $user['firstname'],
                    'middlename' => null,

                    'birthdate' => null,
                    'contactno' => null,
                    'address' => null,

                    'email' => $user['email'],
                    'email_verified_at' => null,

                    'profilepic' => 'images/profiles/default-avatar.png',

                    'referralCode' => $user['referralCode'],
                    'uplineCode' => $user['uplineCode'],

                    'branchCode' => $user['branchCode'],
                    'RoleID' => $user['RoleID'],

                    'status' => 'Active',
                    'remember_token' => null,

                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }
}
