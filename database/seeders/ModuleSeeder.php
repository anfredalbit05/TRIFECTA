<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * The module list currently stored in the `modules` table, in ID order.
     *
     * Module IDs are what `App\Support\Modules` and the privileges table
     * reference, so the seeder keeps the same order and names as the live
     * data. updateOrCreate by name keeps re-runs idempotent.
     */
    public function run(): void
    {
        $modules = [
            ['modulename' => 'Masterlist - Admin Users', 'description' => 'Manage system users and view dashboards'],
            ['modulename' => 'Masterlist - Members', 'description' => 'Manage system member records'],
            ['modulename' => 'Masterlist - Satellite Members', 'description' => 'Manage satellite member records'],
            ['modulename' => 'Masterlist - Branches', 'description' => 'Manage branch locations and details'],
            ['modulename' => 'Masterlist - Benefits', 'description' => 'Manage organizational benefits'],
            ['modulename' => 'Masterlist - FAQs', 'description' => 'Manage frequently asked questions'],
            ['modulename' => 'Masterlist - Inquiries', 'description' => 'Manage user and member inquiries'],
            ['modulename' => 'Transaction List - Withdrawal', 'description' => 'Manage member withdrawal transactions'],
            ['modulename' => 'Transaction List - Activations', 'description' => 'Manage member activation transactions'],
            ['modulename' => 'Post - News & Update', 'description' => 'Manage news and update announcements'],
            ['modulename' => 'Post - Testimonials', 'description' => 'Manage member and user testimonials'],
            ['modulename' => 'Report & Settings - General', 'description' => 'Manage system general reports and settings'],
            ['modulename' => 'Report & Settings - Users Reports', 'description' => 'Manage user reports'],
            ['modulename' => 'Report & Settings - Ledgers', 'description' => 'Manage system ledgers and financial records'],
            ['modulename' => 'Report & Settings - Pangkabuhayan', 'description' => 'Manage Pangkabuhayan reports and records'],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['modulename' => $module['modulename']],
                [
                    'description' => $module['description'],
                    'status' => true,
                ]
            );
        }
    }
}
