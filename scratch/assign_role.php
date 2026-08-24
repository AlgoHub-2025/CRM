<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'manager@example.com')->first();
if (\Spatie\Permission\Models\Role::where('name', 'Sales Manager')->exists()) {
    $user->assignRole('Sales Manager');
    echo 'Assigned!';
}
