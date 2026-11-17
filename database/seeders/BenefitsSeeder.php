<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BenefitsSeeder extends Seeder
{
    /**
     * The benefit set printed on the TRIFECTA poster:
     *
     *   Six-Point Health Benefits (6 months contestability)
     *   Additional Benefits
     *   Plus! 3 Beneficiaries Benefits (age 0–100)
     *
     * Six-point rows carry the waiting period in `details`; the additional
     * benefits are the cash-assistance amounts. Every variant gets the same
     * list, because the poster describes the single ₱1,999 annual plan.
     */
    public function run(): void
    {
        $benefits = [
            // ============================================
            // SIX-POINT HEALTH BENEFITS
            // ============================================
            [
                'name' => 'Preventive Healthcare',
                'details' => 'Reimbursement of ₱500 · CBC, Urinalysis, Fecalysis, Chest X-ray · 6 months contestability',
                'amount' => 500.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'In-Patient Care',
                'details' => '₱15,000 consumable · private or semi-private room, doctor\'s fee',
                'amount' => 15000.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'Out-Patient Care',
                'details' => '₱5,000 consumable · check-up and medicine allowance',
                'amount' => 5000.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'Emergency Care Through Accident',
                'details' => '₱13,000 per incident · ambulance, physician services, minor surgery · usable within 72 hours upon approval',
                'amount' => 13000.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'Dental Care',
                'details' => '₱1,000 reimbursement · consultation and examination, single tooth extraction',
                'amount' => 1000.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'Maternity Care and Ultrasound Services',
                'details' => 'Reimbursement up to ₱2,000 · physician\'s fee/visit, tetanus injection, ultrasound, vitamins and medicines',
                'amount' => 2000.00,
                'holdertype' => 'Primary Owner',
            ],

            // ============================================
            // ADDITIONAL BENEFITS
            // ============================================
            [
                'name' => 'Accidental Death and Disability',
                'details' => 'Cash assistance',
                'amount' => 65000.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'Unprovoked Murder and Assault',
                'details' => 'Cash assistance',
                'amount' => 30000.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'Natural Death Benefits',
                'details' => 'Cash assistance · 6 months contestability',
                'amount' => 20000.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'Motorcycling Accident',
                'details' => 'Financial assistance',
                'amount' => 15000.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'Hospital Income Benefit',
                'details' => '₱500 per day for 15 days if admitted through accident',
                'amount' => 7500.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'Fire Cash Assistance',
                'details' => 'Cash assistance',
                'amount' => 5000.00,
                'holdertype' => 'Primary Owner',
            ],
            [
                'name' => 'Burial Cash Assistance',
                'details' => 'Cash assistance',
                'amount' => 10000.00,
                'holdertype' => 'Primary Owner',
            ],

            // ============================================
            // PLUS! 3 BENEFICIARIES BENEFITS (AGE 0–100)
            // ============================================
            [
                'name' => 'Natural Death Benefit',
                'details' => 'For each of the 3 beneficiaries · 3 months contestability',
                'amount' => 10000.00,
                'holdertype' => 'Beneficiaries',
            ],
            [
                'name' => 'Medical Hospitalization',
                'details' => '₱15,000 consumable · 6 months contestability, no contestability upon renewal',
                'amount' => 15000.00,
                'holdertype' => 'Beneficiaries',
            ],
            [
                'name' => 'Maternity Care & Ultrasound Services',
                'details' => '₱2,000 consumable · 6 months contestability',
                'amount' => 2000.00,
                'holdertype' => 'Beneficiaries',
            ],
            [
                'name' => 'Burial Cash Assistance',
                'details' => 'For each of the 3 beneficiaries',
                'amount' => 5000.00,
                'holdertype' => 'Beneficiaries',
            ],
        ];

        $variantIDs = DB::table('variants')->orderBy('VID')->pluck('VID')->all();

        if ($variantIDs === []) {
            $variantIDs = [1];
        }

        foreach ($variantIDs as $variantID) {
            // Replace this variant's list so re-running the seeder cannot
            // leave the old unrelated benefits behind.
            DB::table('benefits')->where('VID', $variantID)->delete();

            foreach ($benefits as $benefit) {
                DB::table('benefits')->insert([
                    'name' => $benefit['name'],
                    'details' => $benefit['details'],
                    'VID' => $variantID,
                    'amount' => $benefit['amount'],
                    'holdertype' => $benefit['holdertype'],
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
