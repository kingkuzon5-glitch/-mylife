<x-settings-layout>
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-headline-md font-bold text-on-surface">Categories</h3>
            <p class="text-sm text-on-surface-variant mt-0.5">The life areas your habits, tasks, goals, and projects belong to.</p>
        </div>
        <button
            x-on:click="$dispatch('open-modal', 'category-form')"
            wire:click="openCreate"
            class="bg-primary text-on-primary px-4 py-2.5 text-xs font-bold uppercase tracking-widest rounded-lg hover:opacity-90 transition-all shadow-lg flex items-center gap-2"
        >
            <x-icon name="add" class="text-base" /> New Category
        </button>
    </div>

    <div>
        <h4 class="text-label-caps uppercase tracking-widest text-on-surface-variant font-bold mb-3">Default</h4>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach ($systemCategories as $category)
                <div class="glass-panel rounded-xl p-4 flex items-center gap-3">
                    <x-icon :name="$category->icon" class="text-on-surface-variant" />
                    <span class="text-sm text-on-surface truncate">{{ $category->name }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div>
        <h4 class="text-label-caps uppercase tracking-widest text-on-surface-variant font-bold mb-3">Custom</h4>
        @if ($customCategories->isEmpty())
            <p class="text-sm text-on-surface-variant">No custom categories yet.</p>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach ($customCategories as $category)
                    <div wire:key="cat-{{ $category->id }}" class="glass-panel rounded-xl p-4 flex items-center gap-3">
                        <x-icon :name="$category->icon" class="text-on-surface-variant" />
                        <span class="text-sm text-on-surface truncate flex-1">{{ $category->name }}</span>
                        <button wire:click="openEdit({{ $category->id }})" x-on:click="$dispatch('open-modal', 'category-form')" class="text-on-surface-variant hover:text-command-blue transition-colors">
                            <x-icon name="edit" class="text-sm" />
                        </button>
                        <button wire:click="delete({{ $category->id }})" wire:confirm="Delete this category?" class="text-on-surface-variant hover:text-error transition-colors">
                            <x-icon name="delete" class="text-sm" />
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <x-modal name="category-form" max-width="md">
        <form wire:submit="save" class="p-6 space-y-4">
            <h3 class="text-headline-md font-bold text-on-surface">{{ $editingId ? 'Edit Category' : 'New Category' }}</h3>

            <div>
                <x-input-label value="Name" />
                <x-text-input wire:model="name" type="text" class="w-full" placeholder="e.g. Side Business" />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label value="Icon" />
                    <select wire:model="icon" class="w-full bg-surface-container-low border-outline-variant/40 text-on-surface rounded-lg shadow-sm text-sm focus:border-command-blue focus:ring-command-blue">
                        @foreach ($iconOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label value="Color" />
                    <select wire:model="color" class="w-full bg-surface-container-low border-outline-variant/40 text-on-surface rounded-lg shadow-sm text-sm focus:border-command-blue focus:ring-command-blue">
                        <option value="command-blue">Blue</option>
                        <option value="discipline-green">Green</option>
                        <option value="focus-orange">Orange</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', 'category-form')">Cancel</x-secondary-button>
                <x-primary-button>{{ $editingId ? 'Save Changes' : 'Create Category' }}</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-settings-layout>
