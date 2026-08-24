<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Opportunity;
use App\Models\PipelineStage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Livewire;
use App\Livewire\Opportunities\OpportunityKanban;

class OpportunityScopingTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_exec_can_only_drag_own_opportunities()
    {
        $this->withoutExceptionHandling();
        
        Permission::findOrCreate('opportunities.view.own');
        Permission::findOrCreate('opportunities.view.all');
        Permission::findOrCreate('opportunities.update.own');
        Permission::findOrCreate('opportunities.update.all');
        
        $role = Role::findOrCreate('Sales Executive');
        $role->givePermissionTo(['opportunities.view.own', 'opportunities.update.own']);

        $salesExec = User::factory()->create();
        $salesExec->assignRole('Sales Executive');
        $employee = Employee::create([
            'user_id' => $salesExec->id,
            'employee_code' => 'EMP' . mt_rand(1000, 9999),
            'department' => 'Sales',
            'designation' => 'Sales Exec',
            'status' => 'active'
        ]);

        $otherExec = User::factory()->create();
        $otherEmployee = Employee::create([
            'user_id' => $otherExec->id,
            'employee_code' => 'EMP' . mt_rand(1000, 9999),
            'department' => 'Sales',
            'designation' => 'Sales Exec',
            'status' => 'active'
        ]);
        
        $stage1 = PipelineStage::create(['name' => 'New', 'order' => 1, 'type' => 'opportunity']);
        $stage2 = PipelineStage::create(['name' => 'Qualified', 'order' => 2, 'type' => 'opportunity']);

        $ownOpp = Opportunity::create(['title' => 'My Opp', 'assigned_to' => $employee->id, 'stage_id' => $stage1->id]);
        $otherOpp = Opportunity::create(['title' => 'Other Opp', 'assigned_to' => $otherEmployee->id, 'stage_id' => $stage1->id]);

        // Assert Livewire index only shows own opportunities
        Livewire::actingAs($salesExec)
            ->test(OpportunityKanban::class)
            ->assertSee('My Opp')
            ->assertDontSee('Other Opp');

        // Assert can drag own opportunity
        Livewire::actingAs($salesExec)
            ->test(OpportunityKanban::class)
            ->call('updateOpportunityStage', $ownOpp->id, $stage2->id, [$ownOpp->id]);

        $this->assertEquals($stage2->id, $ownOpp->fresh()->stage_id);
    }

    public function test_sales_exec_cannot_drag_others_opportunities()
    {
        Permission::findOrCreate('opportunities.view.own');
        Permission::findOrCreate('opportunities.view.all');
        Permission::findOrCreate('opportunities.update.own');
        Permission::findOrCreate('opportunities.update.all');
        
        $role = Role::findOrCreate('Sales Executive');
        $role->givePermissionTo(['opportunities.view.own', 'opportunities.update.own']);

        $salesExec = User::factory()->create();
        $salesExec->assignRole('Sales Executive');
        $employee = Employee::create([
            'user_id' => $salesExec->id,
            'employee_code' => 'EMP' . mt_rand(1000, 9999),
            'department' => 'Sales',
            'designation' => 'Sales Exec',
            'status' => 'active'
        ]);

        $otherExec = User::factory()->create();
        $otherEmployee = Employee::create([
            'user_id' => $otherExec->id,
            'employee_code' => 'EMP' . mt_rand(1000, 9999),
            'department' => 'Sales',
            'designation' => 'Sales Exec',
            'status' => 'active'
        ]);
        
        $stage1 = PipelineStage::create(['name' => 'New', 'order' => 1, 'type' => 'opportunity']);
        $stage2 = PipelineStage::create(['name' => 'Qualified', 'order' => 2, 'type' => 'opportunity']);

        $otherOpp = Opportunity::create(['title' => 'Other Opp', 'assigned_to' => $otherEmployee->id, 'stage_id' => $stage1->id]);

        // Assert dragging other's opportunity fails with 403
        Livewire::actingAs($salesExec)
            ->test(OpportunityKanban::class)
            ->call('updateOpportunityStage', $otherOpp->id, $stage2->id, [$otherOpp->id])
            ->assertForbidden();

        $this->assertEquals($stage1->id, $otherOpp->fresh()->stage_id);
    }
}
