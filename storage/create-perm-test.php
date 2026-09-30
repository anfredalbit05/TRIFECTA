<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$existing = User::where('username', 'permtest.r7')->first();
if ($existing) {
    echo "EXISTS: {$existing->UserID}\n";
    exit;
}

$user = User::create([
    'username' => 'permtest.r7',
    'email' => 'permtest.r7@example.com',
    'password' => Hash::make('Test@12345'),
    'RoleID' => 7,
    'status' => 'Active',
    'referralCode' => 'R7PERMTEST',
    'lastname' => 'PermTest',
    'firstname' => 'Perm',
]);

file_put_contents(__DIR__ . '/perm-test-ids.json', json_encode(['userId' => $user->UserID]));

echo "CREATED: {$user->UserID}\n";
