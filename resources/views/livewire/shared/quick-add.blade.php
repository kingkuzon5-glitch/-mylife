<div>
    <x-modal name="quick-add" max-width="md">
        <form wire:submit="save" class="p-6 space-y-4">
            <h3 class="text-headline-md font-bold text-on-surface">Quick Add</h3>

            <div class="grid grid-cols-4 gap-2">
                @foreach (['task' => ['Task', 'task_alt'], 'habit' => ['Habit', 'repeat'], 'goal' => ['Goal', 'emoji_events'], 'project' => ['Project', 'account_tree']] as $value => [$label, $icon])
                    <button
                        type="button"
                        wire:click="$set('type', '{{ $value }}')"
                        @class([
                            'flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 transition-all',
                            'border-command-blue bg-command-blue/5' => $type === $value,
                            'border-outline-variant/30 hover:border-outline-variant/60' => $type !== $value,
                        ])
                    >
                        <x-icon :name="$icon" class="{{ $type === $value ? 'text-command-blue' : 'text-on-surface-variant' }}" />
                        <span class="text-[11px] font-bold {{ $type === $value ? 'text-command-blue' : 'text-on-surface-variant' }}">{{ $label }}</span>
                    </button>
                @endforeach
            </div>

            <div>
                <x-input-label value="Name" />
                <x-text-input wire:model="name" type="text" class="w-full" placeholder="What do you want to add?" autofocus />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label value="Category" />
                <select wire:model="category_id" class="w-full bg-surface-container-low border-outline-variant/40 text-on-surface rounded-lg shadow-sm text-sm focus:border-command-blue focus:ring-command-blue">
                    <option value="">General</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            @if (in_array($type, ['task', 'habit']))
                <label class="flex items-center gap-2 text-sm text-on-surface">
                    <input type="checkbox" wire:model="is_mandatory" class="rounded border-outline-variant/40 text-command-blue focus:ring-command-blue" />
                    Mandatory
                </label>
            @endif

            <p class="text-xs text-on-surface-variant">You can fine-tune schedule, targets, and more from the {{ $type }}'s own page after creating it.</p>

            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', 'quick-add')">Cancel</x-secondary-button>
                <x-primary-button>Add</x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
