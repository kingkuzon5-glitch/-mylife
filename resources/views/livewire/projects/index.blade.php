<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-headline-md font-bold text-on-surface">Projects</h2>
            <p class="text-sm text-on-surface-variant mt-0.5">Personal, career, and business projects in motion.</p>
        </div>
        <button
            x-on:click="$dispatch('open-modal', 'project-form')"
            wire:click="openCreate"
            class="bg-primary text-on-primary px-4 py-2.5 text-xs font-bold uppercase tracking-widest rounded-lg hover:opacity-90 transition-all shadow-lg flex items-center gap-2"
        >
            <x-icon name="add" class="text-base" /> New Project
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-start">
        @foreach ($columns as $key => $label)
            <div class="space-y-3">
                <h3 class="text-label-caps uppercase tracking-widest text-on-surface-variant font-bold px-1">
                    {{ $label }} <span class="text-on-surface-variant/60">({{ ($projectsByStatus[$key] ?? collect())->count() }})</span>
                </h3>
                <div class="space-y-3">
                    @forelse ($projectsByStatus[$key] ?? [] as $project)
                        <x-glass-panel wire:key="project-{{ $project->id }}" class="p-4 space-y-3">
                            <a href="{{ route('projects.show', $project) }}" wire:navigate class="font-bold text-sm text-on-surface hover:text-command-blue transition-colors block truncate">{{ $project->name }}</a>
                            <span class="text-xs text-on-surface-variant block">{{ $project->category->name ?? 'General' }}</span>
                            <x-progress-bar :percent="$project->progress_percentage" color="command-blue" class="h-1.5" />
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-on-surface-variant">{{ $project->progress_percentage }}%</span>
                                <div class="flex items-center gap-2">
                                    <button wire:click="openEdit({{ $project->id }})" x-on:click="$dispatch('open-modal', 'project-form')" class="text-on-surface-variant hover:text-command-blue transition-colors">
                                        <x-icon name="edit" class="text-sm" />
                                    </button>
                                    <button wire:click="delete({{ $project->id }})" wire:confirm="Delete this project?" class="text-on-surface-variant hover:text-error transition-colors">
                                        <x-icon name="delete" class="text-sm" />
                                    </button>
                                </div>
                            </div>
                        </x-glass-panel>
                    @empty
                        <div class="p-4 rounded-xl border border-dashed border-outline-variant/30 text-center">
                            <span class="text-xs text-on-surface-variant">Empty</span>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    <x-modal name="project-form" max-width="lg">
        <form wire:submit="save" class="p-6 space-y-4">
            <h3 class="text-headline-md font-bold text-on-surface">{{ $editingId ? 'Edit Project' : 'New Project' }}</h3>

            <div>
                <x-input-label value="Name" />
                <x-text-input wire:model="name" type="text" class="w-full" placeholder="e.g. Portfolio Website" />
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
                    <x-input-label value="Deadline" />
                    <x-text-input wire:model="deadline" type="date" class="w-full" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label value="Status" />
                    <select wire:model="status" class="w-full bg-surface-container-low border-outline-variant/40 text-on-surface rounded-lg shadow-sm text-sm focus:border-command-blue focus:ring-command-blue">
                        <option value="not_started">Not Started</option>
                        <option value="planning">Planning</option>
                        <option value="in_progress">In Progress</option>
                        <option value="on_hold">On Hold</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div>
                    <x-input-label value="Priority" />
                    <select wire:model="priority" class="w-full bg-surface-container-low border-outline-variant/40 text-on-surface rounded-lg shadow-sm text-sm focus:border-command-blue focus:ring-command-blue">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
            </div>

            <div>
                <x-input-label value="Notes" />
                <textarea wire:model="notes" rows="3" class="bg-surface-container-low border-outline-variant/40 text-on-surface rounded-lg shadow-sm text-sm w-full focus:border-command-blue focus:ring-command-blue"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', 'project-form')">Cancel</x-secondary-button>
                <x-primary-button>{{ $editingId ? 'Save Changes' : 'Create Project' }}</x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
