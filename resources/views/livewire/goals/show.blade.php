<div class="space-y-6">
    <a href="{{ route('goals.index') }}" wire:navigate class="inline-flex items-center gap-2 text-sm text-on-surface-variant hover:text-on-surface transition-colors">
        <x-icon name="arrow_back" class="text-base" /> Back to Goals
    </a>

    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-headline-lg font-bold text-on-surface">{{ $goal->name }}</h2>
            <p class="text-sm text-on-surface-variant mt-1">
                {{ $goal->category->name ?? 'General' }}
                @if ($goal->deadline) · Deadline {{ $goal->deadline->format('M j, Y') }} @endif
            </p>
        </div>
        <x-badge :color="match($goal->status) { 'completed' => 'discipline-green', 'abandoned' => 'muted', 'in_progress' => 'command-blue', default => 'focus-orange' }">
            {{ ucfirst(str_replace('_', ' ', $goal->status)) }}
        </x-badge>
    </div>

    @if ($goal->description)
        <p class="text-sm text-on-surface-variant leading-relaxed">{{ $goal->description }}</p>
    @endif

    <x-glass-panel class="p-6">
        <div class="flex justify-between text-sm mb-2">
            <span class="font-bold text-on-surface">Progress</span>
            <span class="font-bold text-command-blue">{{ $goal->progress_percentage }}%</span>
        </div>
        <x-progress-bar :percent="$goal->progress_percentage" color="command-blue" class="h-3" />
    </x-glass-panel>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Milestones --}}
        <x-glass-panel class="p-6 space-y-4">
            <h3 class="text-label-caps uppercase tracking-widest text-on-surface-variant font-bold">Milestones</h3>

            <form wire:submit="addMilestone" class="flex gap-2">
                <x-text-input wire:model="newMilestoneName" type="text" class="flex-1" placeholder="Add a milestone..." />
                <x-primary-button type="submit">Add</x-primary-button>
            </form>

            <div class="space-y-2">
                @forelse ($milestones as $milestone)
                    <div wire:key="milestone-{{ $milestone->id }}" class="flex items-center gap-3 p-3 rounded-lg bg-surface-container-low/60">
                        <button
                            wire:click="toggleMilestone({{ $milestone->id }})"
                            @class([
                                'w-6 h-6 rounded-md flex items-center justify-center shrink-0 border-2 transition-all',
                                'border-discipline-green bg-discipline-green' => $milestone->is_completed,
                                'border-outline-variant/50' => ! $milestone->is_completed,
                            ])
                        >
                            <x-icon name="check" class="text-sm {{ $milestone->is_completed ? 'text-white' : 'text-transparent' }}" />
                        </button>
                        <span class="flex-1 text-sm {{ $milestone->is_completed ? 'line-through text-on-surface-variant' : 'text-on-surface' }}">{{ $milestone->name }}</span>
                        <button wire:click="deleteMilestone({{ $milestone->id }})" class="text-on-surface-variant hover:text-error transition-colors">
                            <x-icon name="close" class="text-base" />
                        </button>
                    </div>
                @empty
                    <p class="text-sm text-on-surface-variant text-center py-4">No milestones yet — break this goal into steps.</p>
                @endforelse
            </div>
        </x-glass-panel>

        {{-- Linked habits & tasks --}}
        <div class="space-y-6">
            <x-glass-panel class="p-6 space-y-3">
                <h3 class="text-label-caps uppercase tracking-widest text-on-surface-variant font-bold">Linked Habits</h3>
                <p class="text-xs text-on-surface-variant">See how your daily habits roll up into this goal.</p>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @forelse ($allHabits as $habit)
                        <label class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-surface-container-low/60 cursor-pointer">
                            <input type="checkbox" wire:model.live="selectedHabitIds" value="{{ $habit->id }}" class="rounded border-outline-variant/40 text-command-blue focus:ring-command-blue" />
                            <x-icon :name="$habit->icon" class="text-base text-on-surface-variant" />
                            <span class="text-sm text-on-surface">{{ $habit->name }}</span>
                        </label>
                    @empty
                        <p class="text-sm text-on-surface-variant">No active habits yet.</p>
                    @endforelse
                </div>
            </x-glass-panel>

            <x-glass-panel class="p-6 space-y-3">
                <h3 class="text-label-caps uppercase tracking-widest text-on-surface-variant font-bold">Linked Tasks</h3>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @forelse ($allTasks as $task)
                        <label class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-surface-container-low/60 cursor-pointer">
                            <input type="checkbox" wire:model.live="selectedTaskIds" value="{{ $task->id }}" class="rounded border-outline-variant/40 text-command-blue focus:ring-command-blue" />
                            <x-icon :name="$task->icon" class="text-base text-on-surface-variant" />
                            <span class="text-sm text-on-surface">{{ $task->name }}</span>
                        </label>
                    @empty
                        <p class="text-sm text-on-surface-variant">No active tasks yet.</p>
                    @endforelse
                </div>
            </x-glass-panel>
        </div>
    </div>
</div>
