<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-headline-md font-bold text-on-surface">Goals</h2>
            <p class="text-sm text-on-surface-variant mt-0.5">Where your daily habits and tasks are actually taking you.</p>
        </div>
        <button
            x-on:click="$dispatch('open-modal', 'goal-form')"
            wire:click="openCreate"
            class="bg-primary text-on-primary px-4 py-2.5 text-xs font-bold uppercase tracking-widest rounded-lg hover:opacity-90 transition-all shadow-lg flex items-center gap-2"
        >
            <x-icon name="add" class="text-base" /> New Goal
        </button>
    </div>

    @if ($goals->isEmpty())
        <x-glass-panel class="p-10 text-center">
            <p class="text-sm text-on-surface-variant">No goals yet. Turn one big ambition into milestones and daily habits.</p>
        </x-glass-panel>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach ($goals as $goal)
                <x-glass-panel wire:key="goal-{{ $goal->id }}" class="p-5 flex flex-col gap-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <a href="{{ route('goals.show', $goal) }}" wire:navigate class="font-bold text-on-surface hover:text-command-blue transition-colors block truncate">{{ $goal->name }}</a>
                            <span class="text-xs text-on-surface-variant">{{ $goal->category->name ?? 'General' }}{{ $goal->deadline ? ' · Due '.$goal->deadline->format('M j, Y') : '' }}</span>
                        </div>
                        <x-badge :color="match($goal->status) { 'completed' => 'discipline-green', 'abandoned' => 'muted', 'in_progress' => 'command-blue', default => 'focus-orange' }">
                            {{ ucfirst(str_replace('_', ' ', $goal->status)) }}
                        </x-badge>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="text-on-surface-variant">Progress</span>
                            <span class="font-bold text-on-surface">{{ $goal->progress_percentage }}%</span>
                        </div>
                        <x-progress-bar :percent="$goal->progress_percentage" color="command-blue" />
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-on-surface-variant/10 text-xs">
                        <span class="text-on-surface-variant">{{ $goal->milestones->where('is_completed', true)->count() }}/{{ $goal->milestones->count() }} milestones</span>
                        <div class="flex items-center gap-3">
                            <button wire:click="openEdit({{ $goal->id }})" x-on:click="$dispatch('open-modal', 'goal-form')" class="text-on-surface-variant hover:text-command-blue transition-colors">
                                <x-icon name="edit" class="text-base" />
                            </button>
                            <button wire:click="delete({{ $goal->id }})" wire:confirm="Delete this goal?" class="text-on-surface-variant hover:text-error transition-colors">
                                <x-icon name="delete" class="text-base" />
                            </button>
                        </div>
                    </div>
                </x-glass-panel>
            @endforeach
        </div>
    @endif

    <x-modal name="goal-form" max-width="lg">
        <form wire:submit="save" class="p-6 space-y-4">
            <h3 class="text-headline-md font-bold text-on-surface">{{ $editingId ? 'Edit Goal' : 'New Goal' }}</h3>

            <div>
                <x-input-label value="Name" />
                <x-text-input wire:model="name" type="text" class="w-full" placeholder="e.g. Become a Full-Stack Developer" />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label value="Description" />
                <textarea wire:model="description" rows="3" class="bg-surface-container-low border-outline-variant/40 text-on-surface rounded-lg shadow-sm text-sm w-full focus:border-command-blue focus:ring-command-blue"></textarea>
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
                    <x-input-label value="Deadline" />
                    <x-text-input wire:model="deadline" type="date" class="w-full" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label value="Priority" />
                    <select wire:model="priority" class="w-full bg-surface-container-low border-outline-variant/40 text-on-surface rounded-lg shadow-sm text-sm focus:border-command-blue focus:ring-command-blue">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div>
                    <x-input-label value="Status" />
                    <select wire:model="status" class="w-full bg-surface-container-low border-outline-variant/40 text-on-surface rounded-lg shadow-sm text-sm focus:border-command-blue focus:ring-command-blue">
                        <option value="not_started">Not Started</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="abandoned">Abandoned</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', 'goal-form')">Cancel</x-secondary-button>
                <x-primary-button>{{ $editingId ? 'Save Changes' : 'Create Goal' }}</x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
