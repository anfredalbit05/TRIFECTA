<?php

$root = __DIR__ . '/resources/views/livewire';

$files = [
    'admin-pages/masterlist/members.blade.php',
    'admin-pages/masterlist/satellite-members.blade.php',
    'admin-pages/masterlist/admin-users.blade.php',
    'admin-pages/masterlist/branches.blade.php',
    'admin-pages/masterlist/benefits.blade.php',
    'admin-pages/masterlist/faqs.blade.php',
    'admin-pages/masterlist/inquiries.blade.php',
    'admin-pages/masterlist/roles.blade.php',
    'admin-pages/post/news-updates.blade.php',
    'admin-pages/post/testimonials.blade.php',
    'admin-pages/report-settings/users-accounts.blade.php',
    'admin-pages/report-settings/variants.blade.php',
    'admin-pages/report-settings/branch-variant-grants.blade.php',
    'admin-pages/report-settings/general.blade.php',
    'admin-pages/transaction-list/disbursements.blade.php',
    'admin-pages/transaction-list/withdrawals.blade.php',
    'admin-pages/transaction-list/activations.blade.php',
    'admin-pages/dashboard/dashboard.blade.php',
    'member-pages/satellite/satellite-members-self.blade.php',
];

foreach ($files as $rel) {
    $path = $root . '/' . $rel;
    if (! is_file($path)) {
        echo "### MISSING {$rel}\n";
        continue;
    }
    $src = file_get_contents($path);
    echo "\n########## {$rel} ##########\n";

    // Find <td ...> blocks whose class mentions action, or class contains action
    if (preg_match_all('/<td[^>]*class="[^"]*action[^"]*"[^>]*>(.*?)<\/td>/is', $src, $m)) {
        $i = 0;
        foreach ($m[1] as $block) {
            $i++;
            if ($i > 2) {
                break;
            }
            $trim = trim(preg_replace('/\n\s*\n/', "\n", $block));
            echo "--- action cell {$i} ---\n" . substr($trim, 0, 2600) . "\n";
        }
    } else {
        echo "(no td.action blocks found)\n";
    }
}
