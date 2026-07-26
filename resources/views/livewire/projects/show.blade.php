<div class="space-y-6">
    <a href="{{ route('projects.index') }}" wire:navigate class="inline-flex items-center gap-2 text-sm text-on-surface-variant hover:text-on-surface transition-colors">
        <x-icon name="arrow_back" class="text-base" /> Back to Projects
    </a>

    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-headline-lg font-bold text-on-surface">{{ $project->name }}</h2>
            <p class="text-sm text-on-surface-variant mt-1">
                {{ $project->category->name ?? 'General' }}
                @if ($project->deadline) · Deadline {{ $project->deadline->format('M j, Y') }} @endif
            </p>
        </div>
        <x-badge :color="$project->status === 'completed' ? 'discipline-green' : ($project->status === 'on_hold' ? 'muted' : 'command-blue')">
            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
        </x-badge>
    </div>

    @if ($project->description)
        <p class="text-sm text-on-surface-variant leading-relaxed">{{ $project->description }}</p>
    @endif

    <x-glass-panel class="p-6">
        <div class="flex justify-between text-sm mb-2">
            <span class="font-bold text-on-surface">Progress</span>
            <span class="font-bold text-command-blue">{{ $project->progress_percentage }}%</span>
        </div>
        <x-progress-bar :percent="$project->progress_percentage" color="command-blue" class="h-3" />
    </x-glass-panel>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-glass-panel class="p-6 space-y-4">
            <h3 class="text-label-caps uppercase tracking-widest text-on-surface-variant font-bold">Tasks</h3>

            <form wire:submit="addTask" class="flex gap-2">
                <x-text-input wire:model="newTaskName" type="text" class="flex-1" placeholder="Add a task..." />
                <x-primary-button type="submit">Add</x-primary-button>
            </form>

            <div class="space-y-2">
                @forelse ($tasks as $task)
                    <div wire:key="ptask-{{ $task->id }}" class="flex items-center gap-3 p-3 rounded-lg bg-surface-container-low/60">
                        <button
                            wire:click="toggleTask({{ $task->id }})"
                            @class([
                                'w-6 h-6 rounded-md flex items-center justify-center shrink-0 border-2 transition-all',
                                'border-discipline-green bg-discipline-green' => $task->is_completed,
                                'border-outline-variant/50' => ! $task->is_completed,
                            ])
                        >
                            <x-icon name="check" class="text-sm {{ $task->is_completed ? 'text-white' : 'text-transparent' }}" />
                        </button>
                        <span class="flex-1 text-sm {{ $task->is_completed ? 'line-through text-on-surface-variant' : 'text-on-surface' }}">{{ $task->name }}</span>
                        <button wire:click="deleteTask({{ $task->id }})" class="text-on-surface-variant hover:text-error transition-colors">
                            <x-icon name="close" class="text-base" />
                        </button>
                    </div>
                @empty
                    <p class="text-sm text-on-surface-variant text-center py-4">No tasks yet.</p>
                @endforelse
            </div>
        </x-glass-panel>

        <div class="space-y-6">
            <x-glass-panel class="p-6 space-y-4">
                <h3 class="text-label-caps uppercase tracking-widest text-on-surface-variant font-bold">Milestones</h3>

                <form wire:submit="addMilestone" class="flex gap-2">
                    <x-text-input wire:model="newMilestoneName" type="text" class="flex-1" placeholder="Add a milestone..." />
                    <x-primary-button type="submit">Add</x-primary-button>
                </form>

                <div class="space-y-2">
                    @forelse ($milestones as $milestone)
                        <div wire:key="pmilestone-{{ $milestone->id }}" class="flex items-center gap-3 p-3 rounded-lg bg-surface-container-low/60">
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
                        <p class="text-sm text-on-surface-variant text-center py-4">No milestones yet.</p>
                    @endforelse
                </div>
            </x-glass-panel>

            <x-glass-panel class="p-6 space-y-3">
                <h3 class="text-label-caps uppercase tracking-widest text-on-surface-variant font-bold">Notes</h3>
                <textarea
                    wire:model.blur="project.notes"
                    wire:blur="updateNotes"
                    rows="5"
                    class="bg-surface-container-low border-outline-variant/40 text-on-surface rounded-lg shadow-sm text-sm w-full focus:border-command-blue focus:ring-command-blue"
                    placeholder="Freeform notes about this project..."
                ></textarea>
            </x-glass-panel>
        </div>
    </div>
</div>
