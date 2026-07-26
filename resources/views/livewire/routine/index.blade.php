<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-headline-md font-bold text-on-surface">Daily Routine</h2>
            <p class="text-sm text-on-surface-variant mt-0.5">The backbone of your day — shown as a timeline on Today.</p>
        </div>
        <button
            x-on:click="$dispatch('open-modal', 'routine-form')"
            wire:click="openCreate"
            class="bg-primary text-on-primary px-4 py-2.5 text-xs font-bold uppercase tracking-widest rounded-lg hover:opacity-90 transition-all shadow-lg flex items-center gap-2"
        >
            <x-icon name="add" class="text-base" /> Add Block
        </button>
    </div>

    @if ($items->isEmpty())
        <x-glass-panel class="p-10 text-center">
            <p class="text-sm text-on-surface-variant">No routine yet. Map out your ideal day, one block at a time.</p>
        </x-glass-panel>
    @else
        <div class="space-y-3">
            @foreach ($items as $item)
                <x-glass-panel wire:key="routine-{{ $item->id }}" class="p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-command-blue/10 flex items-center justify-center text-command-blue shrink-0">
                        <x-icon :name="$item->icon" fill />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-on-surface truncate">{{ $item->title }}</p>
                        <p class="text-xs text-on-surface-variant">
                            {{ \Illuminate\Support\Carbon::parse($item->start_time)->format('g:i A') }}
                            @if ($item->end_time) – {{ \Illuminate\Support\Carbon::parse($item->end_time)->format('g:i A') }} @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <button wire:click="openEdit({{ $item->id }})" x-on:click="$dispatch('open-modal', 'routine-form')" class="text-on-surface-variant hover:text-command-blue transition-colors">
                            <x-icon name="edit" class="text-base" />
                        </button>
                        <button wire:click="delete({{ $item->id }})" wire:confirm="Remove this block?" class="text-on-surface-variant hover:text-error transition-colors">
                            <x-icon name="delete" class="text-base" />
                        </button>
                    </div>
                </x-glass-panel>
            @endforeach
        </div>
    @endif

    <x-modal name="routine-form" max-width="md">
        <form wire:submit="save" class="p-6 space-y-4">
            <h3 class="text-headline-md font-bold text-on-surface">{{ $editingId ? 'Edit Block' : 'New Block' }}</h3>

            <div>
                <x-input-label value="Title" />
                <x-text-input wire:model="title" type="text" class="w-full" placeholder="e.g. Deep Work" />
                <x-input-error :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label value="Icon" />
                <select wire:model="icon" class="w-full bg-surface-container-low border-outline-variant/40 text-on-surface rounded-lg shadow-sm text-sm focus:border-command-blue focus:ring-command-blue">
                    @foreach ($iconOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label value="Start Time" />
                    <x-text-input wire:model="start_time" type="time" class="w-full" />
                    <x-input-error :messages="$errors->get('start_time')" />
                </div>
                <div>
                    <x-input-label value="End Time (optional)" />
                    <x-text-input wire:model="end_time" type="time" class="w-full" />
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', 'routine-form')">Cancel</x-secondary-button>
                <x-primary-button>{{ $editingId ? 'Save Changes' : 'Add Block' }}</x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
