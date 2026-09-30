<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Privilege;
use App\Models\User;
use App\Support\Modules;
use Illuminate\Support\Facades\Auth;

// One user per seeded role
$users = [];
foreach ([1, 2, 5, 7, 4, 8] as $roleId) {
    $users[$roleId] = User::where('RoleID', $roleId)->first();
}

$expect = [
    1 => ['create' => 1, 'read' => 1, 'update' => 1, 'delete' => 1, 'print' => 1, 'change_status' => 1],
    2 => ['create' => 1, 'read' => 1, 'update' => 1, 'delete' => 0, 'print' => 1, 'change_status' => 1],
    5 => ['create' => 1, 'read' => 1, 'update' => 1, 'delete' => 0, 'print' => 1, 'change_status' => 1],
    7 => ['create' => 0, 'read' => 1, 'update' => 0, 'delete' => 0, 'print' => 1, 'change_status' => 0],
];

$fail = 0;
foreach ($expect as $roleId => $actions) {
    $user = $users[$roleId];
    if (! $user) {
        echo "Role $roleId: NO USER FOUND\n";
        $fail++;
        continue;
    }

    // Spot-check all 15 modules for create + change_status, and every action on module 2
    foreach (range(1, 15) as $moduleId) {
        if (Modules::can($moduleId, 'create', $user) !== (bool) $actions['create']) { echo "MISMATCH Role $roleId module $moduleId create\n"; $fail++; }
        if (Modules::can($moduleId, 'change_status', $user) !== (bool) $actions['change_status']) { echo "MISMATCH Role $roleId module $moduleId change_status\n"; $fail++; }
    }
    foreach (['create', 'read', 'update', 'delete', 'print', 'change_status'] as $action) {
        if (Modules::can(Modules::MEMBERS, $action, $user) !== (bool) $actions[$action]) { echo "MISMATCH Role $roleId members $action\n"; $fail++; }
    }
    echo "Role $roleId ({$user->username}): OK (create=".($actions['create'] ? 'Y' : 'N')." delete=".($actions['delete'] ? 'Y' : 'N')." print=".($actions['print'] ? 'Y' : 'N').")\n";
}

// Unprivileged roles (4, 8) must be denied everything on module 1
foreach ([4, 8] as $roleId) {
    $user = $users[$roleId];
    if (! $user) { echo "Role $roleId: NO USER FOUND\n"; $fail++; continue; }
    foreach (['create', 'read', 'update', 'delete'] as $action) {
        if (Modules::can(Modules::ADMIN_USERS, $action, $user) !== false) { echo "MISMATCH Role $roleId should be denied $action\n"; $fail++; }
    }
    echo "Role $roleId ({$user->username}): correctly denied on Admin Users\n";
}

echo $fail === 0 ? "ALL PERMISSION CHECKS PASS\n" : "$fail FAILURES\n";
