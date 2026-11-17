<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->insert([

            // ===========================
            // COMPANY INFORMATION
            // ===========================
            [
                'name' => 'company_name',
                'value' => 'TRIFECTA Health, Wellness & Protection Services, Inc.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'company_short_name',
                'value' => 'TRIFECTA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'company_email',
                'value' => 'care@trifecta.ph',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'company_contact',
                'value' => '09278381647',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===========================
            // MEMBERSHIP
            // ===========================
            [
                'name' => 'membership_validity_months',
                'value' => '12',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===========================
            // COVERAGE
            // ===========================
            [
                'name' => 'bronze_coverage',
                'value' => '75000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'silver_coverage',
                'value' => '150000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'gold_coverage',
                'value' => '250000',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===========================
            // AGE LIMITS
            // ===========================
            [
                'name' => 'primary_owner_min_age',
                'value' => '18',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'primary_owner_max_age',
                'value' => '65',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'beneficiary_max_age',
                'value' => '100',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===========================
            // CLAIMS & WALLET
            // ===========================
            [
                'name' => 'claim_processing_days',
                'value' => '7',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'minimum_wallet_balance',
                'value' => '0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'minimum_withdraw_amount',
                'value' => '500',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===========================
            // SYSTEM
            // ===========================
            [
                'name' => 'system_name',
                'value' => 'TRIFECTA Membership Management System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'system_version',
                'value' => '1.0.0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'maintenance_mode',
                'value' => 'false',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===========================
            // DEFAULT FILES
            // ===========================
            [
                'name' => 'default_profile_picture',
                'value' => 'storage/profiles/default-avatar.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'default_news_updates_picture',
                'value' => 'storage/news-images/default-news.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],


        ]);
    }
}
