<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;
use App\Models\Client;

class TaskPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_developer_can_update_assigned_task()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->assignRole('Developer');

        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id, 'assigned_to' => $employee->id]);
        
        $this->assertTrue($user->can('update', $task));
    }

    public function test_developer_cannot_update_unassigned_task()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->assignRole('Developer');

        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id, 'assigned_to' => null]);
        
        $this->assertFalse($user->can('update', $task));
    }
}
