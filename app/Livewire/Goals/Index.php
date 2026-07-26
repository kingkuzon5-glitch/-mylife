<?php

namespace App\Livewire\Goals;

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

    public ?string $deadline = null;

    public string $priority = 'medium';

    public string $status = 'not_started';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'category_id' => 'nullable|exists:categories,id',
            'deadline' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:not_started,in_progress,completed,abandoned',
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->dispatch('open-modal', 'goal-form');
    }

    public function openEdit(int $id): void
    {
        $goal = auth()->user()->goals()->findOrFail($id);
        $this->authorize('update', $goal);

        $this->editingId = $goal->id;
        $this->name = $goal->name;
        $this->description = $goal->description ?? '';
        $this->category_id = $goal->category_id;
        $this->deadline = $goal->deadline?->toDateString();
        $this->priority = $goal->priority;
        $this->status = $goal->status;

        $this->dispatch('open-modal', 'goal-form');
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->editingId) {
            $goal = auth()->user()->goals()->findOrFail($this->editingId);
            $this->authorize('update', $goal);
            $goal->update($data);
        } else {
            auth()->user()->goals()->create($data);
        }

        $this->dispatch('close-modal', 'goal-form');
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $goal = auth()->user()->goals()->findOrFail($id);
        $this->authorize('delete', $goal);
        $goal->delete();
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'description', 'category_id', 'deadline']);
        $this->priority = 'medium';
        $this->status = 'not_started';
    }

    public function render()
    {
        $user = auth()->user();

        $statusOrder = ['in_progress' => 0, 'not_started' => 1, 'completed' => 2, 'abandoned' => 3];

        $goals = $user->goals()->with(['category', 'milestones'])
            ->orderBy('deadline')
            ->get()
            ->sortBy(fn ($goal) => $statusOrder[$goal->status] ?? 99)
            ->values();

        return view('livewire.goals.index', [
            'goals' => $goals,
            'categories' => Category::forUser($user)->orderBy('sort_order')->get(),
        ]);
    }
}
