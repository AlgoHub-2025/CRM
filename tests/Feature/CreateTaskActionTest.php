<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Project;
use App\Models\Task;
use App\Models\Milestone;
use App\Actions\Projects\CreateTaskAction;

class CreateTaskActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_task_with_correct_status_scoped_order()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $project = Project::factory()->create();

        $action = new CreateTaskAction();

        $t1 = $action->execute($project, ['title' => 'Task 1', 'priority' => 'medium', 'status' => 'todo']);
        $t2 = $action->execute($project, ['title' => 'Task 2', 'priority' => 'medium', 'status' => 'todo']);
        $t3 = $action->execute($project, ['title' => 'Task 3', 'priority' => 'high', 'status' => 'in_progress']);

        // Two 'todo' tasks should be ordered 1, 2
        $this->assertEquals(1, $t1->order);
        $this->assertEquals(2, $t2->order);

        // 'in_progress' column starts its own ordering at 1
        $this->assertEquals(1, $t3->order);
    }

    public function test_defaults_to_todo_status()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $project = Project::factory()->create();

        $action = new CreateTaskAction();
        $task = $action->execute($project, ['title' => 'Default status task', 'priority' => 'low']);

        $this->assertEquals('todo', $task->status);
    }

    public function test_task_can_be_assigned_to_milestone()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $project = Project::factory()->create();
        $milestone = Milestone::factory()->create(['project_id' => $project->id]);

        $action = new CreateTaskAction();
        $task = $action->execute($project, [
            'title' => 'Milestone-bound task',
            'priority' => 'medium',
            'milestone_id' => $milestone->id,
        ]);

        $this->assertEquals($milestone->id, $task->milestone_id);
    }

    public function test_task_fields_are_set_correctly()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $project = Project::factory()->create();

        $action = new CreateTaskAction();
        $task = $action->execute($project, [
            'title' => 'Implement API',
            'description' => 'Build the REST endpoints',
            'priority' => 'high',
            'deadline' => '2026-10-01',
        ]);

        $this->assertEquals($project->id, $task->project_id);
        $this->assertEquals('Implement API', $task->title);
        $this->assertEquals('Build the REST endpoints', $task->description);
        $this->assertEquals('high', $task->priority);
        $this->assertEquals('2026-10-01', $task->deadline->format('Y-m-d'));
    }
}
