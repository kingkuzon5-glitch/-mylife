<?php

namespace App\Livewire\Goals;

use App\Models\Goal;
use Livewire\Component;

class Show extends Component
{
    public Goal $goal;

    public string $newMilestoneName = '';

    public array $selectedHabitIds = [];

    public array $selectedTaskIds = [];

    public function mount(Goal $goal): void
    {
        $this->authorize('view', $goal);
        $this->goal = $goal;
        $this->selectedHabitIds = $goal->habits()->pluck('habits.id')->map(fn ($id) => (string) $id)->toArray();
        $this->selectedTaskIds = $goal->tasks()->pluck('tasks.id')->map(fn ($id) => (string) $id)->toArray();
    }

    public function addMilestone(): void
    {
        $this->validate(['newMilestoneName' => 'required|string|max:255']);

        $this->goal->milestones()->create([
            'name' => $this->newMilestoneName,
            'sort_order' => $this->goal->milestones()->count(),
        ]);

        $this->newMilestoneName = '';
    }

    public function toggleMilestone(int $id): void
    {
        $milestone = $this->goal->milestones()->findOrFail($id);
        $completed = ! $milestone->is_completed;

        $milestone->update([
            'is_completed' => $completed,
            'completed_at' => $completed ? now() : null,
        ]);
    }

    public function deleteMilestone(int $id): void
    {
        $this->goal->milestones()->findOrFail($id)->delete();
        $this->goal->recalculateProgress();
    }

    public function updatedSelectedHabitIds(): void
    {
        $this->goal->habits()->sync($this->selectedHabitIds);
    }

    public function updatedSelectedTaskIds(): void
    {
        $this->goal->tasks()->sync($this->selectedTaskIds);
    }

    public function render()
    {
        $this->goal->refresh();

        return view('livewire.goals.show', [
            'milestones' => $this->goal->milestones,
            'allHabits' => auth()->user()->habits()->active()->orderBy('name')->get(),
            'allTasks' => auth()->user()->tasks()->active()->orderBy('name')->get(),
        ]);
    }
}
