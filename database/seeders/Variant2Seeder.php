<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Variant2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('membership_fees')->insert([
            [
                'FeeID' => 'FEE015',
                'FeeName' => 'Insurance',
                'Cost' => 450.00,
                'VID' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE016',
                'FeeName' => 'Direct Referral',
                'Cost' => 400.00,
                'VID' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE017',
                'FeeName' => 'Provident Fund',
                'Cost' => 50.00,
                'VID' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE018',
                'FeeName' => 'Pangkabuhayan',
                'Cost' => 50.00,
                'VID' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE019',
                'FeeName' => 'Dayong Share',
                'Cost' => 100.00,
                'VID' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE020',
                'FeeName' => 'Overrides',
                'Cost' => 150.00,
                'VID' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE021',
                'FeeName' => 'IT System',
                'Cost' => 50.00,
                'VID' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE022',
                'FeeName' => 'Representation & Travel allowance',
                'Cost' => 100.00,
                'VID' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE023',
                'FeeName' => 'Hospitalization',
                'Cost' => 250.00,
                'VID' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE024',
                'FeeName' => 'Outreach Program',
                'Cost' => 49.00,
                'VID' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE025',
                'FeeName' => 'Leadership Incentive',
                'Cost' => 100.00,
                'VID' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE026',
                'FeeName' => 'Business Partners Wallet',
                'Cost' => 100.00,
                'VID' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE027',
                'FeeName' => 'Admin',
                'Cost' => 250.00,
                'VID' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE028',
                'FeeName' => 'Car Incentives',
                'Cost' => 50.00,
                'VID' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE029',
                'FeeName' => 'Rice',
                'Cost' => 600.00,
                'VID' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE030',
                'FeeName' => 'Memorial Services (Accident)',
                'Cost' => 150.00,
                'VID' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'FeeID' => 'FEE031',
                'FeeName' => 'Investor Wallet',
                'Cost' => 100.00,
                'VID' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
