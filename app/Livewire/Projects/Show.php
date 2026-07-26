<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;

class Show extends Component
{
    public Project $project;

    public string $newTaskName = '';

    public string $newMilestoneName = '';

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);
        $this->project = $project;
    }

    public function addTask(): void
    {
        $this->validate(['newTaskName' => 'required|string|max:255']);

        $this->project->tasks()->create([
            'name' => $this->newTaskName,
            'sort_order' => $this->project->tasks()->count(),
        ]);

        $this->newTaskName = '';
    }

    public function toggleTask(int $id): void
    {
        $task = $this->project->tasks()->findOrFail($id);
        $completed = ! $task->is_completed;

        $task->update([
            'is_completed' => $completed,
            'completed_at' => $completed ? now() : null,
        ]);
    }

    public function deleteTask(int $id): void
    {
        $this->project->tasks()->findOrFail($id)->delete();
        $this->project->recalculateProgress();
    }

    public function addMilestone(): void
    {
        $this->validate(['newMilestoneName' => 'required|string|max:255']);

        $this->project->milestones()->create([
            'name' => $this->newMilestoneName,
            'sort_order' => $this->project->milestones()->count(),
        ]);

        $this->newMilestoneName = '';
    }

    public function toggleMilestone(int $id): void
    {
        $milestone = $this->project->milestones()->findOrFail($id);
        $completed = ! $milestone->is_completed;

        $milestone->update([
            'is_completed' => $completed,
            'completed_at' => $completed ? now() : null,
        ]);
    }

    public function deleteMilestone(int $id): void
    {
        $this->project->milestones()->findOrFail($id)->delete();
    }

    public function updateNotes(): void
    {
        $this->project->save();
    }

    public function render()
    {
        $this->project->refresh();

        return view('livewire.projects.show', [
            'tasks' => $this->project->tasks,
            'milestones' => $this->project->milestones,
        ]);
    }
}
