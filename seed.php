<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Client;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Proposal;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

$role = Role::firstOrCreate(['name' => 'Super Admin']);

$user = User::firstOrCreate(['email' => 'admin@algohub.com'], [
    'name' => 'Admin',
    'password' => Hash::make('password')
]);
$user->assignRole($role);

$company = Company::firstOrCreate(['name' => 'Test Company']);
$contact = Contact::firstOrCreate(['email' => 'client@test.com'], ['company_id' => $company->id, 'name' => 'Client User']);
$client = Client::firstOrCreate(['company_id' => $company->id]);

Proposal::create([
    'client_id' => $client->id, 
    'project_title' => 'Test UI Send', 
    'status' => 'draft', 
    'subtotal' => 100, 
    'total' => 100, 
    'proposal_number' => 'PRP-UI-' . rand(1000, 9999)
]);
echo "Created admin user and draft proposal.\n";
