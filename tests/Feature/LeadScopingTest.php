<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Employee;
use App\Models\Lead;
use App\Models\Company;
use App\Models\AuditLog;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Event;
use App\Events\AuditableAction;

class LeadScopingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup permissions
        Permission::findOrCreate('leads.view.own');
        Permission::findOrCreate('leads.view.all');
        Permission::findOrCreate('leads.create');
        Permission::findOrCreate('leads.update.own');
        Permission::findOrCreate('leads.update.all');
        
        $role = Role::findOrCreate('Sales Executive');
        $role->givePermissionTo(['leads.view.own', 'leads.create', 'leads.update.own']);
    }

    public function test_sales_exec_can_only_view_own_leads()
    {
        $salesExec = User::factory()->create();
        $salesExec->assignRole('Sales Executive');
        $employee = Employee::create([
            'user_id' => $salesExec->id, 
            'employee_code' => 'EMP-001', 
            'designation' => 'Sales', 
            'department' => 'Sales', 
            'status' => 'active'
        ]);
        
        $otherEmployee = Employee::create([
            'employee_code' => 'EMP-002', 
            'designation' => 'Sales', 
            'department' => 'Sales', 
            'status' => 'active'
        ]);
        
        $stage = \App\Models\PipelineStage::create(['name' => 'New', 'order' => 1, 'type' => 'lead']);

        $ownLead = Lead::create(['name' => 'My Lead', 'assigned_to' => $employee->id, 'status_id' => $stage->id]);
        $otherLead = Lead::create(['name' => 'Other Lead', 'assigned_to' => $otherEmployee->id, 'status_id' => $stage->id]);
        
        // Test Scope directly
        $visibleLeads = Lead::query()->visibleTo($salesExec)->get();
        $this->assertCount(1, $visibleLeads);
        $this->assertEquals($ownLead->id, $visibleLeads->first()->id);
        
        // Test Policy (Show Route)
        $this->actingAs($salesExec)->get(route('leads.show', $ownLead))
            ->assertOk();
            
        $this->actingAs($salesExec)->get(route('leads.show', $otherLead))
            ->assertForbidden();
            
        // Test Index renders properly
        $this->actingAs($salesExec)->get(route('leads.index'))
            ->assertOk();
    }
    
    public function test_inline_company_creation_and_audit_logging()
    {
        $this->withoutExceptionHandling();
        
        $salesExec = User::factory()->create();
        $salesExec->assignRole('Sales Executive');
        $employee = Employee::create([
            'user_id' => $salesExec->id, 
            'employee_code' => 'EMP-003', 
            'designation' => 'Sales', 
            'department' => 'Sales', 
            'status' => 'active'
        ]);
        
        $stage = \App\Models\PipelineStage::create(['name' => 'New', 'order' => 1, 'type' => 'lead']);

        $response = $this->actingAs($salesExec)->post(route('leads.store'), [
            'name' => 'New Lead With Company',
            'email' => 'lead@example.com',
            'company_name' => 'Acme Corp',
            'priority' => 'high',
            'assigned_to' => $employee->id,
            'status_id' => $stage->id,
        ]);
        
        $response->assertRedirect(route('leads.index'));
        
        // Verify Company was created
        $company = Company::where('name', 'Acme Corp')->first();
        $this->assertNotNull($company);
        
        // Verify Lead was created and attached
        $lead = Lead::where('name', 'New Lead With Company')->first();
        $this->assertNotNull($lead);
        $this->assertEquals($company->id, $lead->company_id);
        
        // Verify Audit Logs
        $auditLogs = AuditLog::all();
        $this->assertCount(2, $auditLogs); // One for company, one for lead
        
        $companyLog = AuditLog::where('module', 'companies')->first();
        $this->assertEquals('created', $companyLog->action);
        $this->assertEquals(Company::class, $companyLog->record_type);
        $this->assertEquals($company->id, $companyLog->record_id);
        
        $leadLog = AuditLog::where('module', 'leads')->first();
        $this->assertEquals('created', $leadLog->action);
        $this->assertEquals(Lead::class, $leadLog->record_type);
        $this->assertEquals($lead->id, $leadLog->record_id);
    }
}
