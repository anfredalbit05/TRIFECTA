<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MembershipFeeSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('membership_fees')->insert([
            [
                'FeeID' => 'FEE001',
                'FeeName' => 'Insurance',
                'Cost' => 360.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE002',
                'FeeName' => 'Commission',
                'Cost' => 400.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE003',
                'FeeName' => 'Overrides',
                'Cost' => 135.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE004',
                'FeeName' => 'Direct and Override Commission Tax',
                'Cost' => 55.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE005',
                'FeeName' => 'Pangkabuhayan',
                'Cost' => 49.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE006',
                'FeeName' => 'Provident Fund',
                'Cost' => 50.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE007',
                'FeeName' => 'Leadership Program / Office Staff',
                'Cost' => 120.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE008',
                'FeeName' => 'Travel / Car Incentive',
                'Cost' => 100.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE009',
                'FeeName' => 'Hospitalization',
                'Cost' => 300.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE010',
                'FeeName' => 'Dayong',
                'Cost' => 100.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE011',
                'FeeName' => 'IT',
                'Cost' => 30.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE012',
                'FeeName' => 'Admin',
                'Cost' => 300.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
