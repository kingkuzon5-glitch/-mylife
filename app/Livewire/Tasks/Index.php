<?php

namespace App\Livewire\Tasks;

use App\Livewire\Concerns\RefreshesOnQuickAdd;
use App\Livewire\Concerns\TogglesTaskCompletion;
use App\Models\Category;
use App\Support\IconLibrary;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Index extends Component
{
    use RefreshesOnQuickAdd, TogglesTaskCompletion;

    public ?int $editingId = null;

    public string $name = '';

    public string $description = '';

    public ?int $category_id = null;

    public string $icon = 'task_alt';

    public string $priority = 'medium';

    public ?string $scheduled_time = null;

    public ?int $estimated_duration_minutes = null;

    public ?int $reminder_minutes_before = null;

    public string $repeat_type = 'daily';

    public array $repeat_days_of_week = [];

    public bool $is_mandatory = true;

    public string $start_date = '';

    public ?string $end_date = null;

    public function mount(): void
    {
        $this->start_date = now()->toDateString();
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'nullable|exists:categories,id',
            'icon' => 'required|string|max:100',
            'priority' => 'required|in:low,medium,high',
            'scheduled_time' => 'nullable|date_format:H:i',
            'estimated_duration_minutes' => 'nullable|integer|min:1',
            'reminder_minutes_before' => 'nullable|integer|min:0',
            'repeat_type' => 'required|in:none,daily,weekly,specific_days,monthly',
            'repeat_days_of_week' => 'nullable|array',
            'is_mandatory' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->dispatch('open-modal', 'task-form');
    }

    public function openEdit(int $id): void
    {
        $task = auth()->user()->tasks()->findOrFail($id);
        $this->authorize('update', $task);

        $this->editingId = $task->id;
        $this->name = $task->name;
        $this->description = $task->description ?? '';
        $this->category_id = $task->category_id;
        $this->icon = $task->icon;
        $this->priority = $task->priority;
        $this->scheduled_time = $task->scheduled_time;
        $this->estimated_duration_minutes = $task->estimated_duration_minutes;
        $this->reminder_minutes_before = $task->reminder_minutes_before;
        $this->repeat_type = $task->repeat_type;
        $this->repeat_days_of_week = $task->repeat_days_of_week ?? [];
        $this->is_mandatory = $task->is_mandatory;
        $this->start_date = $task->start_date->toDateString();
        $this->end_date = $task->end_date?->toDateString();

        $this->dispatch('open-modal', 'task-form');
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->editingId) {
            $task = auth()->user()->tasks()->findOrFail($this->editingId);
            $this->authorize('update', $task);
            $task->update($data);
        } else {
            auth()->user()->tasks()->create($data);
        }

        $this->dispatch('close-modal', 'task-form');
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $task = auth()->user()->tasks()->findOrFail($id);
        $this->authorize('delete', $task);
        $task->delete();
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId', 'name', 'description', 'category_id', 'scheduled_time',
            'estimated_duration_minutes', 'reminder_minutes_before', 'repeat_days_of_week', 'end_date',
        ]);

        $this->icon = 'task_alt';
        $this->priority = 'medium';
        $this->repeat_type = 'daily';
        $this->is_mandatory = true;
        $this->start_date = now()->toDateString();
    }

    public function render()
    {
        $user = auth()->user();
        $today = Carbon::today();

        $tasks = $user->tasks()->with(['category', 'completions' => function ($query) use ($today) {
            $query->whereDate('date', $today);
        }])
            ->orderByDesc('is_active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('livewire.tasks.index', [
            'tasks' => $tasks,
            'categories' => Category::forUser($user)->orderBy('sort_order')->get(),
            'iconOptions' => IconLibrary::options(),
        ]);
    }
}
