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
            ->where('email', 'anfredalbit110525@gmail.com')
            ->value('UserID');

        DB::table('branches')->insert([
            [
                'branchname' => 'San Franch Main Branch',
                'address' => 'Tagum City, Davao del Norte',
                'status' => true,
                'UserID' => $createdBy,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'branchname' => 'Digos Branch',
                'address' => 'Digos City, Davao del Sur',
                'status' => true,
                'UserID' => $createdBy,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'branchname' => 'Mati Branch',
                'address' => 'Mati City, Davao del Sur',
                'status' => true,
                'UserID' => $createdBy,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
