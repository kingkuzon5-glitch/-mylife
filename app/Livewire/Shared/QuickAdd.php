<?php

namespace App\Livewire\Shared;

use App\Models\Category;
use Livewire\Component;

class QuickAdd extends Component
{
    public string $type = 'task';

    public string $name = '';

    public ?int $category_id = null;

    public bool $is_mandatory = false;

    protected function rules(): array
    {
        return [
            'type' => 'required|in:task,habit,goal,project',
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'is_mandatory' => 'boolean',
        ];
    }

    public function save(): void
    {
        $data = $this->validate();
        $user = auth()->user();
        $today = now()->toDateString();

        match ($data['type']) {
            'task' => $user->tasks()->create([
                'name' => $data['name'],
                'category_id' => $data['category_id'],
                'is_mandatory' => $data['is_mandatory'],
                'repeat_type' => 'daily',
                'start_date' => $today,
            ]),
            'habit' => $user->habits()->create([
                'name' => $data['name'],
                'category_id' => $data['category_id'],
                'is_mandatory' => $data['is_mandatory'],
                'tracking_type' => 'boolean',
                'schedule_type' => 'daily',
                'start_date' => $today,
            ]),
            'goal' => $user->goals()->create([
                'name' => $data['name'],
                'category_id' => $data['category_id'],
            ]),
            'project' => $user->projects()->create([
                'name' => $data['name'],
                'category_id' => $data['category_id'],
            ]),
        };

        $this->dispatch('close-modal', 'quick-add');
        $this->dispatch('quick-added', type: $data['type']);
        $this->reset(['name', 'category_id', 'is_mandatory']);
    }

    public function render()
    {
        return view('livewire.shared.quick-add', [
            'categories' => Category::forUser(auth()->user())->orderBy('sort_order')->get(),
        ]);
    }
}
