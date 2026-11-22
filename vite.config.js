import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/maintenance.css',
                'resources/css/admin/print/report-settings.css',
                'resources/css/admin/print/transaction-list.css',
                'resources/css/admin/print/masterlist.css',
                'resources/css/member/print/genealogy.css',
                'resources/js/app.js',
                'resources/js/passkeys.js',
                'resources/js/layouts/app.js',
                'resources/js/components/navbar.js',
                'resources/js/livewire/auth/reset-password.js',
                'resources/js/livewire/components/spin-fund-report.js',
                'resources/js/livewire/landing-page/team.js',
                'resources/js/livewire/landing-page/home.js',
                'resources/js/livewire/member-pages/genealogy/board-members.js',
                'resources/js/livewire/member-pages/genealogy/invitee.js',
                'resources/js/livewire/member-pages/my-profile/account.js',
                'resources/js/livewire/member-pages/my-profile/profile.js',
                'resources/js/livewire/member-pages/my-profile/security.js',
                'resources/js/livewire/share-pages/dashboard/overview.js',
                'resources/js/livewire/application-modal.js',
                'resources/js/livewire/admin-pages/post/news-updates.js',
                'resources/js/livewire/admin-pages/report-settings/users-accounts.js',
                'resources/js/livewire/admin-pages/report-settings/overview.js',
                'resources/js/livewire/admin-pages/masterlist/satellite-members-components/satellite-members-edit.js',
                'resources/js/livewire/admin-pages/masterlist/members-modals/satelite-modal.js',
                'resources/js/livewire/admin-pages/masterlist/dashboard.js',
                'resources/js/livewire/admin-pages/dashboard/overview.js',
                'resources/js/pages/admin-pages/report-settings.js',
                'resources/js/pages/admin-pages/transaction-list.js',
            ],
            refresh: true,
        }),

        tailwindcss(),
    ],
});
