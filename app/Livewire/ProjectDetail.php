<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Actions\Projects\CreateMilestoneAction;

class ProjectDetail extends Component
{
    public Project $project;

    // Milestone creation form
    public $showMilestoneForm = false;
    public $milestoneName = '';
    public $milestoneDueDate = '';

    public function mount(Project $project)
    {
        $this->project = $project->load(['client.company', 'projectManager', 'contract', 'milestones']);
    }

    public function toggleMilestoneForm()
    {
        $this->showMilestoneForm = !$this->showMilestoneForm;
        $this->reset(['milestoneName', 'milestoneDueDate']);
    }

    public function createMilestone(CreateMilestoneAction $action)
    {
        $action->execute($this->project, [
            'name' => $this->milestoneName,
            'due_date' => $this->milestoneDueDate ?: null,
        ]);

        $this->reset(['milestoneName', 'milestoneDueDate', 'showMilestoneForm']);
        $this->project->load('milestones');
    }

    public function render()
    {
        return view('livewire.project-detail')->layout('layouts.app');
    }
}

