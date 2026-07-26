<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-headline-md font-bold text-on-surface">Tasks</h2>
            <p class="text-sm text-on-surface-variant mt-0.5">One-off and recurring checklist items.</p>
        </div>
        <button
            x-on:click="$dispatch('open-modal', 'task-form')"
            wire:click="openCreate"
            class="bg-primary text-on-primary px-4 py-2.5 text-xs font-bold uppercase tracking-widest rounded-lg hover:opacity-90 transition-all shadow-lg flex items-center gap-2"
        >
            <x-icon name="add" class="text-base" /> New Task
        </button>
    </div>

    @if ($tasks->isEmpty())
        <x-glass-panel class="p-10 text-center">
            <p class="text-sm text-on-surface-variant">No tasks yet. Add the things you need to do today.</p>
        </x-glass-panel>
    @else
        <div class="space-y-3">
            @foreach ($tasks as $task)
                @php $completedToday = $task->completions->isNotEmpty(); @endphp
                <x-glass-panel wire:key="task-{{ $task->id }}" class="p-4 sm:p-5 flex items-center gap-4 {{ ! $task->is_active ? 'opacity-50' : '' }}">
                    <button
                        wire:click="toggleTask({{ $task->id }})"
                        @class([
                            'w-9 h-9 rounded-lg flex items-center justify-center shrink-0 transition-all',
                            'bg-discipline-green text-white' => $completedToday,
                            'bg-surface-container-high text-on-surface-variant' => ! $completedToday,
                        ])
                    >
                        <x-icon :name="$task->icon" fill class="text-lg" />
                    </button>

                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-on-surface truncate {{ $completedToday ? 'line-through opacity-60' : '' }}">{{ $task->name }}</p>
                        <p class="text-xs text-on-surface-variant truncate">
                            {{ $task->category->name ?? 'General' }}
                            · {{ ucfirst($task->priority) }} priority
                            · {{ $task->repeat_type === 'none' ? 'One-time' : ucfirst(str_replace('_', ' ', $task->repeat_type)) }}
                            @if ($task->current_streak > 0)
                                · 🔥 {{ $task->current_streak }}
                            @endif
                        </p>
                    </div>

                    @if ($task->is_mandatory)
                        <x-badge color="focus-orange" class="hidden sm:inline-flex">Mandatory</x-badge>
                    @endif

                    <div class="flex items-center gap-3 shrink-0">
                        <button wire:click="openEdit({{ $task->id }})" x-on:click="$dispatch('open-modal', 'task-form')" class="text-on-surface-variant hover:text-command-blue transition-colors">
                            <x-icon name="edit" class="text-base" />
                        </button>
                        <button wire:click="delete({{ $task->id }})" wire:confirm="Delete this task?" class="text-on-surface-variant hover:text-error transition-colors">
                            <x-icon name="delete" class="text-base" />
                        </button>
                    </div>
                </x-glass-panel>
            @endforeach
        </div>
    @endif

    <x-modal name="task-form" max-width="lg">
        <form wire:submit="save" class="p-6 space-y-4 max-h-[85vh] overflow-y-auto">
            <h3 class="text-headline-md font-bold text-on-surface">{{ $editingId ? 'Edit Task' : 'New Task' }}</h3>

            <div>
                <x-input-label value="Name" />
                <x-text-input wire:model="name" type="text" class="w-full" placeholder="e.g. Reply to emails" />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label value="Description" />
                <textarea wire:model="description" rows="2" class="bg-surface-container-low border-outline-variant/40 text-on-surface rounded-lg shadow-sm text-sm w-full focus:border-command-blue focus:ring-command-blue"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label value="Category" />
                    <select wire:model="category_id" class="w-full bg-surface-container-low border-outline-variant/40 text-on-surface rounded-lg shadow-sm text-sm focus:border-command-blue focus:ring-command-blue">
                        <option value="">General</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label value="Icon" />
                    <select wire:model="icon" class="w-full bg-surface-container-low border-outline-variant/40 text-on-surface rounded-lg shadow-sm text-sm focus:border-command-blue focus:ring-command-blue">
                        @foreach ($iconOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <x-input-label value="Priority" />
                    <select wire:model="priority" class="w-full bg-surface-container-low border-outline-variant/40 text-on-surface rounded-lg shadow-sm text-sm focus:border-command-blue focus:ring-command-blue">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div>
                    <x-input-label value="Time" />
                    <x-text-input wire:model="scheduled_time" type="time" class="w-full" />
                </div>
                <div>
                    <x-input-label value="Duration (min)" />
                    <x-text-input wire:model="estimated_duration_minutes" type="number" min="1" class="w-full" />
                </div>
            </div>

            <div>
                <x-input-label value="Repeat" />
                <select wire:model.live="repeat_type" class="w-full bg-surface-container-low border-outline-variant/40 text-on-surface rounded-lg shadow-sm text-sm focus:border-command-blue focus:ring-command-blue">
                    <option value="none">One-time</option>
                    <option value="daily">Every day</option>
                    <option value="weekly">Weekly</option>
                    <option value="specific_days">Specific days</option>
                    <option value="monthly">Monthly</option>
                </select>
            </div>

            @if ($repeat_type === 'specific_days')
                <div class="flex flex-wrap gap-2">
                    @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $index => $day)
                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container-low text-xs cursor-pointer">
                            <input type="checkbox" wire:model="repeat_days_of_week" value="{{ $index }}" class="rounded border-outline-variant/40 text-command-blue focus:ring-command-blue" />
                            {{ $day }}
                        </label>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label value="Start Date" />
                    <x-text-input wire:model="start_date" type="date" class="w-full" />
                </div>
                <div>
                    <x-input-label value="End Date (optional)" />
                    <x-text-input wire:model="end_date" type="date" class="w-full" />
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm text-on-surface">
                <input type="checkbox" wire:model="is_mandatory" class="rounded border-outline-variant/40 text-command-blue focus:ring-command-blue" />
                Mandatory task
            </label>

            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', 'task-form')">Cancel</x-secondary-button>
                <x-primary-button>{{ $editingId ? 'Save Changes' : 'Create Task' }}</x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
