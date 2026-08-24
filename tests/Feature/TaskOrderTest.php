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
use App\Livewire\Projects\TaskBoard;
use Livewire\Livewire;

class TaskOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_reorder_tasks_and_persist_to_db()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->assignRole('Project Manager');

        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id, 'project_manager_id' => $employee->id]);

        $task1 = Task::factory()->create(['project_id' => $project->id, 'title' => 'Task A', 'status' => 'todo', 'order' => 1]);
        $task2 = Task::factory()->create(['project_id' => $project->id, 'title' => 'Task B', 'status' => 'todo', 'order' => 2]);
        $task3 = Task::factory()->create(['project_id' => $project->id, 'title' => 'Task C', 'status' => 'todo', 'order' => 3]);

        // Send new order: C, A, B within 'todo' status
        Livewire::actingAs($user)
            ->test(TaskBoard::class, ['project' => $project])
            ->call('updateTaskStatus', $task3->id, 'todo', [$task3->id, $task1->id, $task2->id]);
        
        $this->assertDatabaseHas('tasks', ['id' => $task3->id, 'status' => 'todo', 'order' => 1]);
        $this->assertDatabaseHas('tasks', ['id' => $task1->id, 'status' => 'todo', 'order' => 2]);
        $this->assertDatabaseHas('tasks', ['id' => $task2->id, 'status' => 'todo', 'order' => 3]);
    }
}
