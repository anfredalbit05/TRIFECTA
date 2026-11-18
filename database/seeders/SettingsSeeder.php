<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Centralized system settings.
     *
     * BranchID = 0 means these are global system settings.
     *
     * updateOrInsert prevents duplicates, but will update existing values
     * whenever this seeder is executed.
     */
    public function run(): void
    {
        $settings = [

            // ===========================
            // COMPANY INFORMATION
            // ===========================
            'company_name' => 'TRIFECTA HEALTH - WELLNESS - PROTECTION',
            'company_short_name' => 'TRIFECTA',
            'company_email' => 'trifecta@gmail.com',
            'company_contact' => '09278381647',

            // ===========================
            // MEMBERSHIP & COVERAGE
            // ===========================
            'membership_validity_months' => '12',

            'satellite_bronze_coverage' => '75000',
            'satellite_silver_coverage' => '150000',
            'satellite_gold_coverage' => '250000',

            'satellite_bronze_allocated' => '20',
            'satellite_silver_allocated' => '40',
            'satellite_gold_allocated' => '80',

            'primary_owner_min_age' => '18',
            'primary_owner_max_age' => '65',
            'beneficiary_max_age' => '100',

            // ===========================
            // CLAIMS & WALLET
            // ===========================
            'claim_processing_days' => '7',
            'minimum_wallet_balance' => '0',
            'minimum_withdraw_amount' => '500',
            'activate_withdrawal' => 'true',

            // ===========================
            // PAYMENT CHANNELS
            // ===========================

            // GCash
            'gcash_payment_enabled' => 'false',
            'gcash_convenience_fee_enabled' => 'false',
            'gcash_number1' => '09266958032',
            'gcash_number1_name' => 'Princes Manliguez',

            // BPI
            'bank_payment_enabled' => 'false',
            'bpi_account_name' => 'RENANTE S. ROBLES',
            'bpi_account_number' => '2079539565',

            // General bank account
            'bank_name' => 'BPI',
            'bank_account_name' => 'Anfred',
            'bank_account_number' => '12312312',

            // Maya
            'paymaya_payment_enabled' => 'true',
            'paymaya_convenience_fee_enabled' => 'false',
            'paymaya_account_name' => 'Princes Manliguez',
            'paymaya_account_number' => '09266958032',

            // Cashier
            'cashier_name' => 'Princes Pugasan Manliguez',
            'cashier_signature' => 'images/CASHIER_SIG_20260926_092346_6ab78f2249d90.jpg',

            // ===========================
            // SPIN THE WHEEL
            // ===========================
            'activate_spin_pangkabuhayan' => 'true',
            'spin_prize_kilos' => '5',
            'spin_prize_price_per_kilo' => '56',
            'spin_draw_day' => '28',

            'spin_claim_instructions' =>
                'Present this email together with a valid government-issued ID at our office to claim your prize or contact us to arrange pickup. Prizes are released to the winning member only.',

            // ===========================
            // SATELLITE / JOIN
            // ===========================
            'activate_satellite_application' => 'false',
            'join_allow' => 'true',

            // ===========================
            // SYSTEM
            // ===========================
            'system_name' => 'TRIFECTA Membership Management System',
            'system_version' => '1.0.0',
            'maintenance_mode' => 'false',

            // ===========================
            // DEFAULT FILES
            // ===========================
            'default_profile_picture' => 'storage/profiles/default-avatar.png',
            'default_news_updates_picture' => 'storage/news-images/default-news.png',
        ];

        foreach ($settings as $name => $value) {
            DB::table('settings')->updateOrInsert(
                [
                    'BranchID' => 0,
                    'name' => $name,
                ],
                [
                    'value' => $value,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
