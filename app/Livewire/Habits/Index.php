<?php

namespace App\Livewire\Habits;

use App\Livewire\Concerns\RefreshesOnQuickAdd;
use App\Livewire\Concerns\TogglesHabitCompletion;
use App\Models\Category;
use App\Support\IconLibrary;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Index extends Component
{
    use RefreshesOnQuickAdd, TogglesHabitCompletion;

    public ?int $editingId = null;

    public string $name = '';

    public string $description = '';

    public ?int $category_id = null;

    public string $icon = 'repeat';

    public string $tracking_type = 'boolean';

    public ?float $target_value = null;

    public ?string $target_unit = null;

    public ?string $target_time = null;

    public string $schedule_type = 'daily';

    public array $schedule_days = [];

    public ?int $schedule_times_per_week = null;

    public string $priority = 'medium';

    public ?string $reminder_time = null;

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
            'tracking_type' => 'required|in:boolean,quantity,duration,count,time',
            'target_value' => 'nullable|numeric|min:0',
            'target_unit' => 'nullable|string|max:50',
            'target_time' => 'nullable|date_format:H:i',
            'schedule_type' => 'required|in:daily,specific_days,x_per_week,monthly',
            'schedule_days' => 'nullable|array',
            'schedule_times_per_week' => 'nullable|integer|min:1|max:7',
            'priority' => 'required|in:low,medium,high',
            'reminder_time' => 'nullable|date_format:H:i',
            'is_mandatory' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->dispatch('open-modal', 'habit-form');
    }

    public function openEdit(int $id): void
    {
        $habit = auth()->user()->habits()->findOrFail($id);
        $this->authorize('update', $habit);

        $this->editingId = $habit->id;
        $this->name = $habit->name;
        $this->description = $habit->description ?? '';
        $this->category_id = $habit->category_id;
        $this->icon = $habit->icon;
        $this->tracking_type = $habit->tracking_type;
        $this->target_value = $habit->target_value;
        $this->target_unit = $habit->target_unit;
        $this->target_time = $habit->target_time;
        $this->schedule_type = $habit->schedule_type;
        $this->schedule_days = $habit->schedule_days ?? [];
        $this->schedule_times_per_week = $habit->schedule_times_per_week;
        $this->priority = $habit->priority;
        $this->reminder_time = $habit->reminder_time;
        $this->is_mandatory = $habit->is_mandatory;
        $this->start_date = $habit->start_date->toDateString();
        $this->end_date = $habit->end_date?->toDateString();

        $this->dispatch('open-modal', 'habit-form');
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->editingId) {
            $habit = auth()->user()->habits()->findOrFail($this->editingId);
            $this->authorize('update', $habit);
            $habit->update($data);
        } else {
            auth()->user()->habits()->create($data);
        }

        $this->dispatch('close-modal', 'habit-form');
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $habit = auth()->user()->habits()->findOrFail($id);
        $this->authorize('delete', $habit);
        $habit->delete();
    }

    public function toggleActive(int $id): void
    {
        $habit = auth()->user()->habits()->findOrFail($id);
        $this->authorize('update', $habit);
        $habit->update(['is_active' => ! $habit->is_active]);
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId', 'name', 'description', 'category_id', 'target_value',
            'target_unit', 'target_time', 'schedule_days', 'schedule_times_per_week',
            'reminder_time', 'end_date',
        ]);

        $this->icon = 'repeat';
        $this->tracking_type = 'boolean';
        $this->schedule_type = 'daily';
        $this->priority = 'medium';
        $this->is_mandatory = true;
        $this->start_date = now()->toDateString();
    }

    public function render()
    {
        $user = auth()->user();
        $today = Carbon::today();

        $habits = $user->habits()->with(['category', 'logs' => function ($query) use ($today) {
            $query->whereDate('date', $today);
        }])
            ->orderByDesc('is_active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('livewire.habits.index', [
            'habits' => $habits,
            'categories' => Category::forUser($user)->orderBy('sort_order')->get(),
            'iconOptions' => IconLibrary::options(),
        ]);
    }
}
