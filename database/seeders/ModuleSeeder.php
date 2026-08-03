<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'modulename' => 'Dashboard - All Users',
                'description' => 'Manage system users and view dashboards',
                'status' => true,
            ],
            [
                'modulename' => 'Masterlist - Members',
                'description' => 'Manage system members records',
                'status' => true,
            ],
            [
                'modulename' => 'Masterlist - Branches',
                'description' => 'Manage branch locations and details',
                'status' => true,
            ],
            [
                'modulename' => 'Masterlist - Benefits',
                'description' => 'Manage organizational benefits',
                'status' => true,
            ],
            [
                'modulename' => 'Masterlist - Inquiries',
                'description' => 'Manage user and member inquiries',
                'status' => true,
            ],
            [
                'modulename' => 'Masterlist - FAQs',
                'description' => 'Manage frequently asked questions',
                'status' => true,
            ],
            [
                'modulename' => 'Post - News & Updates',
                'description' => 'Manage news and update announcements',
                'status' => true,
            ],
            [
                'modulename' => 'Post - Testimonials',
                'description' => 'Manage member and user testimonials',
                'status' => true,
            ],
            [
                'modulename' => 'Report & Settings - General',
                'description' => 'Manage system general reports and settings',
                'status' => true,
            ],
        ];

        foreach ($modules as $module) {
            Module::firstOrCreate(
                ['modulename' => $module['modulename']],
                $module
            );
        }
    }
}
