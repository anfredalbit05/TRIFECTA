<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersBoardSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'username' => 'superadmin',
                'password' => Hash::make('Admin@c0nf3d3nt!47'), // TODO: set real password / force reset on first login

                'lastname'   => 'System',
                'firstname'  => 'Superadmin',
                'middlename' => null,

                'birthdate' => null, // TODO

                'contactno' => null, // TODO
                'address'   => null, // TODO

                'email' => 'dtechmsolutions@gmail.com', // TODO: real email
                'email_verified_at' => now(),

                'referralCode' => 'SYS0001',
                'uplineCode'   => null,

                'branchCode' => null,

                'RoleID' => 1, 

                'status' => 'Active',

                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'bobbie.dee',
                'password' => Hash::make('Dayongan@2026'), // TODO: set real password / force reset on first login

                'lastname'   => 'Dee',
                'firstname'  => 'Bobbie',
                'middlename' => null,

                'birthdate' => null, // TODO

                'contactno' => null, // TODO
                'address'   => null, // TODO

                'email' => 'bobbie.dee@dayongan.test', // TODO: real email
                'email_verified_at' => now(),

                'referralCode' => 'CEO0001',
                'uplineCode'   => null,

                'branchCode' => null,

                'RoleID' => 3,

                'status' => 'Active',

                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'princes.manliguez',
                'password' => Hash::make('Dayongan@2026'),

                'lastname'   => 'Manliguez',
                'firstname'  => 'Princes',
                'middlename' => null,

                'birthdate' => null,

                'contactno' => null,
                'address'   => null,

                'email' => 'princes.manliguez@dayongan.test',
                'email_verified_at' => now(),

                'referralCode' => 'BOARD0001',
                'uplineCode'   => null,

                'branchCode' => null,

                'RoleID' => 4, // Board Member

                'status' => 'Active',

                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'ma.carnelita.robles',
                'password' => Hash::make('Dayongan@2026'),

                'lastname'   => 'Robles',
                'firstname'  => 'Ma. Carnelita', // "Dr." title not stored - no prefix field in schema
                'middlename' => null,

                'birthdate' => null,

                'contactno' => null,
                'address'   => null,

                'email' => 'carnelita.robles@dayongan.test',
                'email_verified_at' => now(),

                'referralCode' => 'BOARD0002',
                'uplineCode'   => null,

                'branchCode' => null,

                'RoleID' => 4, // Board Member

                'status' => 'Active',

                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'cherry.nierras-dagala',
                'password' => Hash::make('Dayongan@2026'),

                'lastname'   => 'Nierras-Dagala',
                'firstname'  => 'Cherry Lynn', // "Dr." title not stored - no prefix field in schema
                'middlename' => null,

                'birthdate' => null,

                'contactno' => null,
                'address'   => null,

                'email' => 'cherrylynn.dagala@dayongan.test',
                'email_verified_at' => now(),

                'referralCode' => 'BOARD0003',
                'uplineCode'   => null,

                'branchCode' => null,

                'RoleID' => 4, // Board Member

                'status' => 'Active',

                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'alfe.duron',
                'password' => Hash::make('Dayongan@2026'),

                'lastname'   => 'Duron',
                'firstname'  => 'Alfe',
                'middlename' => null,

                'birthdate' => null,

                'contactno' => null,
                'address'   => null,

                'email' => 'alfe.duron@dayongan.test',
                'email_verified_at' => now(),

                'referralCode' => 'BOARD0004',
                'uplineCode'   => null,

                'branchCode' => null,

                'RoleID' => 4, // Board Member

                'status' => 'Active',

                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'ma.daisy.colegado',
                'password' => Hash::make('Dayongan@2026'),

                'lastname'   => 'Colegado',
                'firstname'  => 'Ma. Daisy',
                'middlename' => null,

                'birthdate' => null,

                'contactno' => null,
                'address'   => null,

                'email' => 'daisy.colegado@dayongan.test',
                'email_verified_at' => now(),

                'referralCode' => 'BOARD0005',
                'uplineCode'   => null,

                'branchCode' => null,

                'RoleID' => 4, // Board Member

                'status' => 'Active',

                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'bernard.laurente',
                'password' => Hash::make('Dayongan@2026'),

                'lastname'   => 'Laurente',
                'firstname'  => 'Bernard',
                'middlename' => null,

                'birthdate' => null,

                'contactno' => null,
                'address'   => null,

                'email' => 'bernard.laurente@dayongan.test',
                'email_verified_at' => now(),

                'referralCode' => 'BOARD0006',
                'uplineCode'   => null,

                'branchCode' => null,

                'RoleID' => 4, // Board Member

                'status' => 'Active',

                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'pierre.hordista',
                'password' => Hash::make('Dayongan@2026'),

                'lastname'   => 'Hordista',
                'firstname'  => 'Pierre',
                'middlename' => null,

                'birthdate' => null,

                'contactno' => null,
                'address'   => null,

                'email' => 'pierre.hordista@dayongan.test',
                'email_verified_at' => now(),

                'referralCode' => 'BOARD0007',
                'uplineCode'   => null,

                'branchCode' => null,

                'RoleID' => 4, // Board Member

                'status' => 'Active',

                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'cheryl.eliot',
                'password' => Hash::make('Dayongan@2026'),

                'lastname'   => 'Eliot',
                'firstname'  => 'Cheryl', // "RN" credential not stored - no suffix field in schema
                'middlename' => null,

                'birthdate' => null,

                'contactno' => null,
                'address'   => null,

                'email' => 'cheryl.eliot@dayongan.test',
                'email_verified_at' => now(),

                'referralCode' => 'BOARD0008',
                'uplineCode'   => null,

                'branchCode' => null,

                'RoleID' => 4, // Board Member

                'status' => 'Active',

                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
