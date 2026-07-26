<?php

namespace App\Livewire\Routine;

use App\Support\IconLibrary;
use Livewire\Component;

class Index extends Component
{
    public ?int $editingId = null;

    public string $title = '';

    public string $icon = 'schedule';

    public string $start_time = '';

    public ?string $end_time = null;

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'icon' => 'required|string|max:100',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'title', 'end_time']);
        $this->icon = 'schedule';
        $this->start_time = '';
        $this->dispatch('open-modal', 'routine-form');
    }

    public function openEdit(int $id): void
    {
        $item = auth()->user()->routineItems()->findOrFail($id);
        $this->authorize('update', $item);

        $this->editingId = $item->id;
        $this->title = $item->title;
        $this->icon = $item->icon;
        $this->start_time = $item->start_time;
        $this->end_time = $item->end_time;

        $this->dispatch('open-modal', 'routine-form');
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->editingId) {
            $item = auth()->user()->routineItems()->findOrFail($this->editingId);
            $this->authorize('update', $item);
            $item->update($data);
        } else {
            $data['sort_order'] = auth()->user()->routineItems()->count();
            auth()->user()->routineItems()->create($data);
        }

        $this->dispatch('close-modal', 'routine-form');
    }

    public function delete(int $id): void
    {
        $item = auth()->user()->routineItems()->findOrFail($id);
        $this->authorize('delete', $item);
        $item->delete();
    }

    public function render()
    {
        return view('livewire.routine.index', [
            'items' => auth()->user()->routineItems()->orderBy('start_time')->get(),
            'iconOptions' => IconLibrary::options(),
        ]);
    }
}
