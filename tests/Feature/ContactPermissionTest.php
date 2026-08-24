<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactPermissionTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    public function test_user_without_contact_permissions_cannot_view_create_form()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $user = \App\Models\User::factory()->create(); // No roles assigned
        
        $company = \App\Models\Company::factory()->create();
        
        $response = $this->actingAs($user)->get(route('companies.contacts.create', $company));
        $response->assertStatus(403);
    }

    public function test_user_with_contact_create_permission_can_view_create_form()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $user = \App\Models\User::factory()->create();
        $user->givePermissionTo('contacts.create');
        
        $company = \App\Models\Company::factory()->create();
        
        $response = $this->actingAs($user)->get(route('companies.contacts.create', $company));
        $response->assertStatus(200);
    }

    public function test_sales_exec_can_only_update_own_contacts()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $user = \App\Models\User::factory()->create();
        $employee = \App\Models\Employee::factory()->create(['user_id' => $user->id]);
        $user->employee_id = $employee->id;
        $user->assignRole('Sales Executive');
        
        $company = \App\Models\Company::factory()->create();
        $contact = \App\Models\Contact::factory()->create(['company_id' => $company->id]);
        
        // At this point, the user doesn't own any leads/opportunities for this company
        $this->assertFalse($user->can('update', $contact));

        // Now let's assign a lead to the user
        \App\Models\Lead::factory()->create([
            'company_id' => $company->id,
            'assigned_to' => $employee->id
        ]);
        
        $this->assertTrue($user->can('update', $contact));
    }

    public function test_sales_manager_can_update_any_contact()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $user = \App\Models\User::factory()->create();
        $user->assignRole('Sales Manager');
        
        $company = \App\Models\Company::factory()->create();
        $contact = \App\Models\Contact::factory()->create(['company_id' => $company->id]);
        
        $this->assertTrue($user->can('update', $contact));
    }
}
