<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

App\Models\User::query()
    ->with('roles')
    ->orderBy('id')
    ->get()
    ->each(function ($user) {
        echo 'ID: ' . $user->id . PHP_EOL;
        echo 'Name: ' . $user->name . PHP_EOL;
        echo 'Email: ' . $user->email . PHP_EOL;
        echo 'Roles: ' . $user->roles->pluck('name')->implode(', ') . PHP_EOL;
        echo '------------------------' . PHP_EOL;
    });
    