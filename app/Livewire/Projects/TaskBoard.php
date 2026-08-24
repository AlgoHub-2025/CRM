<?php

namespace App\Livewire\Projects;

use Livewire\Component;
use App\Models\Project;
use App\Models\Task;
use App\Actions\Projects\CreateTaskAction;

class TaskBoard extends Component
{
    public Project $project;

    // Task creation form
    public $showTaskForm = false;
    public $taskTitle = '';
    public $taskDescription = '';
    public $taskPriority = 'medium';
    public $taskAssignedTo = '';
    public $taskMilestoneId = '';
    public $taskDeadline = '';

    public function mount(Project $project)
    {
        $this->project = $project;
    }

    public function toggleTaskForm()
    {
        $this->showTaskForm = !$this->showTaskForm;
        $this->reset(['taskTitle', 'taskDescription', 'taskPriority', 'taskAssignedTo', 'taskMilestoneId', 'taskDeadline']);
    }

    public function createTask(CreateTaskAction $action)
    {
        $action->execute($this->project, [
            'title' => $this->taskTitle,
            'description' => $this->taskDescription ?: null,
            'priority' => $this->taskPriority,
            'assigned_to' => $this->taskAssignedTo ?: null,
            'milestone_id' => $this->taskMilestoneId ?: null,
            'deadline' => $this->taskDeadline ?: null,
            'status' => 'todo',
        ]);

        $this->reset(['taskTitle', 'taskDescription', 'taskPriority', 'taskAssignedTo', 'taskMilestoneId', 'taskDeadline', 'showTaskForm']);
    }

    public function updateTaskStatus($taskId, $newStatus, $orderedIds)
    {
        $task = Task::findOrFail($taskId);
        
        // Ensure user can update this task
        if (!auth()->user()->can('update', $task)) {
            abort(403);
        }

        $task->update(['status' => $newStatus]);

        // Reorder tasks in the target column
        foreach ($orderedIds as $index => $id) {
            Task::where('id', $id)->update(['order' => $index + 1]);
        }
    }

    public function render()
    {
        return view('livewire.projects.task-board', [
            'tasks' => $this->project->tasks()->orderBy('order')->get(),
            'milestones' => $this->project->milestones()->orderBy('order')->get(),
            'employees' => \App\Models\Employee::orderBy('id')->get(['id', 'employee_code', 'designation']),
        ]);
    }
}

