<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Dashboard\MainDashboard;
use App\Models\User;
use App\Models\Employee;
use App\Models\Opportunity;
use App\Models\Company;
use App\Models\Lead;
use Spatie\Permission\Models\Permission;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_dashboard_scopes_opportunities_to_own_for_restricted_user()
    {
        Permission::findOrCreate('opportunities.view.own');
        Permission::findOrCreate('opportunities.view.all');
        
        $user1 = User::factory()->create();
        $employee1 = Employee::factory()->create(['user_id' => $user1->id]);
        $user1->givePermissionTo('opportunities.view.own');

        $user2 = User::factory()->create();
        $employee2 = Employee::factory()->create(['user_id' => $user2->id]);

        $company = Company::factory()->create();
        $lead = Lead::factory()->create(['company_id' => $company->id]);
        
        Opportunity::factory()->create(['lead_id' => $lead->id, 'assigned_to' => $employee1->id, 'value' => 1000]);
        Opportunity::factory()->create(['lead_id' => $lead->id, 'assigned_to' => $employee2->id, 'value' => 5000]);

        $this->actingAs($user1);
        Livewire::test(MainDashboard::class)
            ->assertViewHas('pipelineValue', function ($value) {
                return $value == 1000;
            });
    }

    public function test_dashboard_shows_all_opportunities_for_all_permission()
    {
        Permission::findOrCreate('opportunities.view.all');
        
        $user1 = User::factory()->create();
        $employee1 = Employee::factory()->create(['user_id' => $user1->id]);
        $user1->givePermissionTo('opportunities.view.all');

        $user2 = User::factory()->create();
        $employee2 = Employee::factory()->create(['user_id' => $user2->id]);

        $company = Company::factory()->create();
        $lead = Lead::factory()->create(['company_id' => $company->id]);
        
        Opportunity::factory()->create(['lead_id' => $lead->id, 'assigned_to' => $employee1->id, 'value' => 1000]);
        Opportunity::factory()->create(['lead_id' => $lead->id, 'assigned_to' => $employee2->id, 'value' => 5000]);

        $this->actingAs($user1);
        Livewire::test(MainDashboard::class)
            ->assertViewHas('pipelineValue', function ($value) {
                return $value == 6000;
            });
    }

    public function test_dashboard_hides_widgets_without_view_permission()
    {
        $user = User::factory()->create();
        $this->actingAs($user); // No permissions

        Livewire::test(MainDashboard::class)
            ->assertSet('canViewPipeline', false)
            ->assertSet('canViewInvoices', false)
            ->assertSet('canViewTasks', false)
            ->assertSet('canViewTickets', false);
    }

    public function test_dashboard_filters_activity_feed_based_on_permissions()
    {
        Permission::findOrCreate('opportunities.view.all');
        Permission::findOrCreate('invoices.view.all');

        $user = User::factory()->create();
        // Give user opportunity permission, but NOT invoice permission
        $user->givePermissionTo('opportunities.view.all');

        $this->actingAs($user);

        // Create an Opportunity audit log
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'action' => 'marked_won',
            'module' => 'Opportunities',
            'record_type' => 'App\Models\Opportunity',
            'record_id' => '01m03spvav3kym69meypykdpqp', // dummy UUID
            'old_values' => [],
            'new_values' => []
        ]);

        // Create an Invoice audit log
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'action' => 'payment_recorded',
            'module' => 'Invoices',
            'record_type' => 'App\Models\Invoice',
            'record_id' => '01m03spvav3kym69meypykdpqp', // dummy UUID
            'old_values' => [],
            'new_values' => []
        ]);

        Livewire::test(MainDashboard::class)
            ->assertViewHas('recentActivity', function ($activities) {
                // Should contain Opportunities, but NOT Invoices
                $hasOpportunity = $activities->where('module', 'Opportunities')->count() > 0;
                $hasInvoice = $activities->where('module', 'Invoices')->count() > 0;
                
                return $hasOpportunity && !$hasInvoice;
            });
    }
}
