<?php

namespace App\Livewire\Settings;

use App\Models\Category;
use App\Support\IconLibrary;
use Livewire\Component;

class Categories extends Component
{
    public ?int $editingId = null;

    public string $name = '';

    public string $icon = 'category';

    public string $color = 'command-blue';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:100',
            'color' => 'required|in:command-blue,discipline-green,focus-orange',
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'name']);
        $this->icon = 'category';
        $this->color = 'command-blue';
        $this->dispatch('open-modal', 'category-form');
    }

    public function openEdit(int $id): void
    {
        $category = auth()->user()->categories()->findOrFail($id);
        $this->authorize('update', $category);

        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->icon = $category->icon;
        $this->color = $category->color;

        $this->dispatch('open-modal', 'category-form');
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']).'-'.auth()->id();

        if ($this->editingId) {
            $category = auth()->user()->categories()->findOrFail($this->editingId);
            $this->authorize('update', $category);
            $category->update($data);
        } else {
            auth()->user()->categories()->create($data);
        }

        $this->dispatch('close-modal', 'category-form');
    }

    public function delete(int $id): void
    {
        $category = auth()->user()->categories()->findOrFail($id);
        $this->authorize('delete', $category);
        $category->delete();
    }

    public function render()
    {
        return view('livewire.settings.categories', [
            'systemCategories' => Category::whereNull('user_id')->orderBy('sort_order')->get(),
            'customCategories' => auth()->user()->categories()->orderBy('name')->get(),
            'iconOptions' => IconLibrary::options(),
        ]);
    }
}
