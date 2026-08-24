<?php

use App\Models\User;
use App\Models\Employee;
use Spatie\Permission\Models\Role;
use App\Models\Opportunity;
use App\Models\Proposal;

// 1. Create or get roles
$managerRole = Role::firstOrCreate(['name' => 'manager']);
$managerRole->givePermissionTo('opportunities.view.all');
$managerRole->givePermissionTo('proposals.view.all');

$salesRole = Role::firstOrCreate(['name' => 'sales']);
$salesRole->givePermissionTo('opportunities.view.own');
$salesRole->givePermissionTo('proposals.view.own');

// 2. Create users
$managerUser = clone(User::where('email', 'manager@example.com')->first() ?? User::factory()->create(['email' => 'manager@example.com', 'name' => 'Manager User']));
$managerUser->assignRole($managerRole);
if (!$managerUser->employee) {
    Employee::factory()->create(['user_id' => $managerUser->id]);
}
$managerUser->refresh();

$salesUser = clone(User::where('email', 'sales@example.com')->first() ?? User::factory()->create(['email' => 'sales@example.com', 'name' => 'Sales User']));
$salesUser->assignRole($salesRole);
if (!$salesUser->employee) {
    Employee::factory()->create(['user_id' => $salesUser->id]);
}
$salesUser->refresh();

// 3. Create some pipeline opportunities
$wonStage = \App\Models\PipelineStage::firstOrCreate(['name' => 'Qualified', 'type' => 'opportunity'], ['is_won' => false]);

$salesEmployeeId = Employee::where('user_id', $salesUser->id)->first()->id;
$managerEmployeeId = Employee::where('user_id', $managerUser->id)->first()->id;

$company = \App\Models\Company::first() ?? \App\Models\Company::factory()->create();
$lead = \App\Models\Lead::firstOrCreate(['company_id' => $company->id], \App\Models\Lead::factory()->make(['company_id' => $company->id])->toArray());

Opportunity::factory()->create(['assigned_to' => $salesEmployeeId, 'value' => 15000, 'stage_id' => $wonStage->id, 'lead_id' => $lead->id]);
Opportunity::factory()->create(['assigned_to' => $managerEmployeeId, 'value' => 50000, 'stage_id' => $wonStage->id, 'lead_id' => $lead->id]);

// 4. Create a draft proposal assigned to the sales user
$client = \App\Models\Client::first() ?? \App\Models\Client::factory()->create();
$proposal = Proposal::factory()->create([
    'client_id' => $client->id,
    'status' => 'draft',
    'total' => 1000
]);

echo "Manager Email: manager@example.com\n";
echo "Sales Email: sales@example.com\n";
echo "Proposal ID: " . $proposal->id . "\n";
