<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Company;
use App\Models\PipelineStage;
use App\Models\LeadSource;

$user = User::where('email', 'ceo@algohub.com')->first();
$employee = $user->employee ?? \App\Models\Employee::create([
    'user_id' => $user->id,
    'employee_code' => 'EMP-001',
    'department' => 'Sales',
    'designation' => 'CEO',
    'status' => 'active'
]);

$user->assignRole('CEO');

// Get some stages
$stages = PipelineStage::where('type', 'opportunity')->orderBy('order')->get();
if ($stages->isEmpty()) {
    PipelineStage::create(['name' => 'New', 'order' => 1, 'type' => 'opportunity']);
    PipelineStage::create(['name' => 'Qualified', 'order' => 2, 'type' => 'opportunity']);
    PipelineStage::create(['name' => 'Proposal', 'order' => 3, 'type' => 'opportunity']);
    PipelineStage::create(['name' => 'Negotiation', 'order' => 4, 'type' => 'opportunity']);
    $stages = PipelineStage::where('type', 'opportunity')->orderBy('order')->get();
}

$source = LeadSource::firstOrCreate(['name' => 'Website']);

// Get a lead status
$leadStage = PipelineStage::firstOrCreate(['name' => 'New Lead', 'type' => 'lead', 'order' => 1]);

// Create Dummy Leads
for ($i = 1; $i <= 5; $i++) {
    $company = Company::create(['name' => "Fake Corp $i", 'industry' => 'Tech']);
    Lead::create([
        'name' => "John Doe $i",
        'company_id' => $company->id,
        'email' => "john$i@fakecorp.com",
        'status_id' => $leadStage->id,
        'source_id' => $source->id,
        'assigned_to' => $employee->id,
        'interested_service' => 'Web Development',
        'priority' => 'high'
    ]);
}

// Create Dummy Opportunities
$stageIds = $stages->pluck('id')->toArray();

for ($i = 1; $i <= 8; $i++) {
    $company = Company::create(['name' => "Opportunity Corp $i", 'industry' => 'Finance']);
    Opportunity::create([
        'title' => "Enterprise Deal $i",
        'value' => mt_rand(10000, 50000),
        'probability' => mt_rand(1, 9) * 10,
        'stage_id' => $stageIds[array_rand($stageIds)],
        'assigned_to' => $employee->id
    ]);
}

echo "Dummy data seeded successfully!\n";
