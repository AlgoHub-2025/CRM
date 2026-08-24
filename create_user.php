<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::firstOrCreate(
    ['email' => 'ceo@algohub.com'], 
    ['name' => 'CEO', 'password' => \Illuminate\Support\Facades\Hash::make('password')]
);

if(class_exists('\Spatie\Permission\Models\Role')){
    $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'finance']);
    $user->assignRole($role);
}
echo 'User created.';
