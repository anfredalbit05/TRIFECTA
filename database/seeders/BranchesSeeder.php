<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $createdBy = DB::table('users')
            ->where('email', 'selboretnan@gmail.com')
            ->value('UserID');

        DB::table('branches')->insert([
            [
                'branchname' => 'Main Branch',
                'address' => 'Tagum City, Davao del Norte',
                'status' => true,
                'UserID' => $createdBy,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'branchname' => 'Kapalong Branch',
                'address' => 'Kapalong, Davao del Norte',
                'status' => true,
                'UserID' => $createdBy,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
