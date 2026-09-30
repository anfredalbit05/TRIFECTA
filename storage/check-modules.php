<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== modules ===\n";
foreach (DB::table('modules')->orderBy('ModuleID')->get() as $m) {
    echo $m->ModuleID . ' | ' . $m->modulename . ' | status=' . $m->status . "\n";
}
echo "\n=== privileges count by role ===\n";
foreach (DB::table('privileges')->selectRaw('RoleID, COUNT(*) as c, SUM(can_update) as upd, SUM(can_delete) as del')->groupBy('RoleID')->orderBy('RoleID')->get() as $p) {
    echo 'Role ' . $p->RoleID . ': rows=' . $p->c . ' can_update_sum=' . $p->upd . ' can_delete_sum=' . $p->del . "\n";
}
echo "\n=== roles ===\n";
foreach (DB::table('roles')->orderBy('RoleID')->get() as $r) {
    echo $r->RoleID . ' | ' . ($r->RoleName ?? ($r->role_name ?? '?')) . "\n";
}
