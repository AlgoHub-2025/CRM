<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'ceo@algohub.com')->first();
$role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
$user->assignRole($role);
echo 'Admin assigned';
