<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

$user = App\Models\User::query()
    ->where('name', 'مشرف النظام')
    ->first();

if (! $user) {
    echo 'User مشرف النظام not found.' . PHP_EOL;
    exit;
}

$user->update([
    'password' => Hash::make('P@$$w0rd!'),
     'email' => 'admin@library.com'
]);

$user->syncRoles(['admin']);

echo 'Password reset successfully.' . PHP_EOL;
echo 'Email: ' . $user->email . PHP_EOL;
echo 'Password: P@$$w0rd!' . PHP_EOL;