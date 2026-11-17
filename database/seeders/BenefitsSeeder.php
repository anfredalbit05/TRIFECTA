<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BenefitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $benefits = [
            // Primary Owner Benefits (18-65 yrs old)
            [
                'name' => 'TRIFECTA Unnatural Fatality (Disablement) Cash Assistance',
                'details' => null,
                'amount' => 65000.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'TRIFECTA Health "Support Accident" Reimbursement',
                'details' => 'Per occurrence',
                'amount' => 13000.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'TRIFECTA Financial Support sa Di Tinuyong Pagkamatay',
                'details' => null,
                'amount' => 30000.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'Motorcycling Accident Financial Assistance',
                'details' => null,
                'amount' => 15000.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'TRIFECTA Admission Support Pag Maaksidente',
                'details' => 'Good for 15 days, ₱500.00 per day',
                'amount' => 500.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'TRIFECTA Fire Cash Assistance',
                'details' => null,
                'amount' => 5000.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'TRIFECTA Accidental Burial Assistance',
                'details' => null,
                'amount' => 10000.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'Dental Checkup Cash Reimbursement',
                'details' => null,
                'amount' => 1000.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'Natural Death Cash Assistance',
                'details' => '3 months contestability',
                'amount' => 20000.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'Critical Illness Financial Assistance for Medicine',
                'details' => 'Consumable, 6 mos contestability',
                'amount' => 5000.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'Annual P.E. Health Support Reimbursement',
                'details' => null,
                'amount' => 500.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'TRIFECTA Hospitalization Assistance',
                'details' => '₱15,000 consumable, upon renew next year no contestability',
                'amount' => 15000.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => '2x Pregnant Evaluation (Ultrasound for Pregnant Women)',
                'details' => '6 mos contestability',
                'amount' => 2000.00,
                'holdertype' => 'Primary Owner',
            ],

            // 3 Beneficiaries Benefits (0-100 yrs old)
            [
                'name' => 'TRIFECTA Natural Death Claim Financial Assistance',
                'details' => '3 mos contestability',
                'amount' => 10000.00,
                'holdertype' => 'Beneficiaries',
            ],
            [
                'name' => 'TRIFECTA Hospitalization Assistance',
                'details' => '6 mos contestability, upon renewal there will be no contestability, consumable',
                'amount' => 15000.00,
                'holdertype' => 'Beneficiaries',
            ],
            [
                'name' => '2x Pregnant Evaluation (Ultrasound for Pregnant Women)',
                'details' => '6 mos contestability',
                'amount' => 2000.00,
                'holdertype' => 'Beneficiaries',
            ],
            [
                'name' => 'Death Burial Assistance',
                'details' => null,
                'amount' => 5000.00,
                'holdertype' => 'Beneficiaries',
            ],
        ];

        foreach ($benefits as $benefit) {
            DB::table('benefits')->updateOrInsert(
                [
                    'name' => $benefit['name'],
                    'holdertype' => $benefit['holdertype'],
                ],
                [
                    'details' => $benefit['details'],
                    'amount' => $benefit['amount'],
                    'status' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}