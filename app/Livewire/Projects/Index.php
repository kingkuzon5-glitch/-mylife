<?php

namespace App\Livewire\Projects;

use App\Livewire\Concerns\RefreshesOnQuickAdd;
use App\Models\Category;
use Livewire\Component;

class Index extends Component
{
    use RefreshesOnQuickAdd;

    public ?int $editingId = null;

    public string $name = '';

    public string $description = '';

    public ?int $category_id = null;

    public string $status = 'not_started';

    public ?string $deadline = null;

    public string $priority = 'medium';

    public string $notes = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:not_started,planning,in_progress,on_hold,completed',
            'deadline' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'notes' => 'nullable|string|max:5000',
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->dispatch('open-modal', 'project-form');
    }

    public function openEdit(int $id): void
    {
        $project = auth()->user()->projects()->findOrFail($id);
        $this->authorize('update', $project);

        $this->editingId = $project->id;
        $this->name = $project->name;
        $this->description = $project->description ?? '';
        $this->category_id = $project->category_id;
        $this->status = $project->status;
        $this->deadline = $project->deadline?->toDateString();
        $this->priority = $project->priority;
        $this->notes = $project->notes ?? '';

        $this->dispatch('open-modal', 'project-form');
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->editingId) {
            $project = auth()->user()->projects()->findOrFail($this->editingId);
            $this->authorize('update', $project);
            $project->update($data);
        } else {
            auth()->user()->projects()->create($data);
        }

        $this->dispatch('close-modal', 'project-form');
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $project = auth()->user()->projects()->findOrFail($id);
        $this->authorize('delete', $project);
        $project->delete();
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'description', 'category_id', 'deadline', 'notes']);
        $this->status = 'not_started';
        $this->priority = 'medium';
    }

    public function render()
    {
        $user = auth()->user();

        $projects = $user->projects()->with('category')->orderBy('priority')->get();

        $columns = [
            'not_started' => 'Not Started',
            'planning' => 'Planning',
            'in_progress' => 'In Progress',
            'on_hold' => 'On Hold',
            'completed' => 'Completed',
        ];

        return view('livewire.projects.index', [
            'projectsByStatus' => $projects->groupBy('status'),
            'columns' => $columns,
            'categories' => Category::forUser($user)->orderBy('sort_order')->get(),
        ]);
    }
}
