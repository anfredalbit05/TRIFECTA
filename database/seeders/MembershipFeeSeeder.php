<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MembershipFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('membership_fees')->insert([
            [
                'FeeID' => 'FEE001',
                'FeeName' => 'Insurance',
                'Cost' => 450.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE002',
                'FeeName' => 'Direct Referral',
                'Cost' => 400.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE003',
                'FeeName' => 'Provident Fund',
                'Cost' => 50.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE004',
                'FeeName' => 'Pangkabuhayan',
                'Cost' => 50.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE005',
                'FeeName' => 'Dayong Share',
                'Cost' => 50.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE006',
                'FeeName' => 'Overrides',
                'Cost' => 150.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE007',
                'FeeName' => 'IT System',
                'Cost' => 50.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE008',
                'FeeName' => 'Satellite Office',
                'Cost' => 100.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE009',
                'FeeName' => 'Hospitalization',
                'Cost' => 100.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE010',
                'FeeName' => 'Outreach Program',
                'Cost' => 49.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE011',
                'FeeName' => 'Leadership Incentive',
                'Cost' => 150.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE012',
                'FeeName' => 'Investor',
                'Cost' => 200.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE013',
                'FeeName' => 'Admin',
                'Cost' => 200.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
