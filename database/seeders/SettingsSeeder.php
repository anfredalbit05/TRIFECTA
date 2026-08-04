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
                'value' => 'TrueLife Dayongan Solidaritas Social Welfare Services, Inc.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'company_short_name',
                'value' => 'DAYONGAN',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'company_email',
                'value' => 'dayongansolidaritas26@gmail.com',
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
                'name' => 'membership_fee',
                'value' => '1999',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'membership_validity_months',
                'value' => '12',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'maximum_beneficiaries',
                'value' => '3',
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
                'value' => 'Dayongan Membership Management System',
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
                'value' => 'profiles/default-avatar.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'default_news_updates_logo',
                'value' => 'news/default-news.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ===========================
            // REFERRAL
            // ===========================
            [
                'name' => 'referral_prefix',
                'value' => 'DAY',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
