<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-headline-md font-bold text-on-surface">Habits</h2>
            <p class="text-sm text-on-surface-variant mt-0.5">The behaviors you're building for the long run.</p>
        </div>
        <button
            x-on:click="$dispatch('open-modal', 'habit-form')"
            wire:click="openCreate"
            class="bg-primary text-on-primary px-4 py-2.5 text-xs font-bold uppercase tracking-widest rounded-lg hover:opacity-90 transition-all shadow-lg flex items-center gap-2"
        >
            <x-icon name="add" class="text-base" /> New Habit
        </button>
    </div>

    @if ($habits->isEmpty())
        <x-glass-panel class="p-10 text-center">
            <p class="text-sm text-on-surface-variant">No habits yet. Start with 3–5 that matter most.</p>
        </x-glass-panel>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($habits as $habit)
                @php
                    $log = $habit->logs->first();
                    $completedToday = (bool) ($log?->completed);
                @endphp
                <x-glass-panel wire:key="habit-{{ $habit->id }}" class="p-5 flex flex-col gap-4 {{ ! $habit->is_active ? 'opacity-50' : '' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-lg bg-discipline-green/10 flex items-center justify-center text-discipline-green shrink-0">
                                <x-icon :name="$habit->icon" fill />
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('habits.show', $habit) }}" wire:navigate class="font-bold text-on-surface truncate block hover:text-command-blue transition-colors">{{ $habit->name }}</a>
                                <span class="text-xs text-on-surface-variant">{{ $habit->category->name ?? 'General' }}</span>
                            </div>
                        </div>
                        <button
                            wire:click="toggleHabit({{ $habit->id }})"
                            @class([
                                'w-8 h-8 rounded-md flex items-center justify-center shrink-0 transition-all',
                                'border-2 border-discipline-green bg-discipline-green' => $completedToday,
                                'border-2 border-outline-variant/50' => ! $completedToday,
                            ])
                        >
                            <x-icon name="check" @class(['text-white' => $completedToday, 'text-transparent' => ! $completedToday]) />
                        </button>
                    </div>

                    <div class="flex items-center gap-4 text-xs">
                        <span class="flex items-center gap-1 text-discipline-green font-bold">
                            <x-icon name="local_fire_department" fill class="text-sm" /> {{ $habit->current_streak }}
                        </span>
                        <span class="text-on-surface-variant">Best {{ $habit->best_streak }}</span>
                        @if ($habit->is_mandatory)
                            <x-badge color="focus-orange">Mandatory</x-badge>
                        @endif
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-on-surface-variant/10 text-xs">
                        <span class="text-on-surface-variant capitalize">{{ str_replace('_', ' ', $habit->schedule_type) }}</span>
                        <div class="flex items-center gap-3">
                            <button wire:click="openEdit({{ $habit->id }})" x-on:click="$dispatch('open-modal', 'habit-form')" class="text-on-surface-variant hover:text-command-blue transition-colors">
                                <x-icon name="edit" class="text-base" />
                            </button>
                            <button wire:click="delete({{ $habit->id }})" wire:confirm="Delete this habit? This cannot be undone." class="text-on-surface-variant hover:text-error transition-colors">
                                <x-icon name="delete" class="text-base" />
                            </button>
                        </div>
                    </div>
                </x-glass-panel>
            @endforeach
        </div>
    @endif

    <x-modal name="habit-form" max-width="lg">
        <form wire:submit="save" class="p-6 space-y-4 max-h-[85vh] overflow-y-auto">
            <h3 class="text-headline-md font-bold text-on-surface">{{ $editingId ? 'Edit Habit' : 'New Habit' }}</h3>

            <div>
                <x-input-label value="Name" />
                <x-text-input wire:model="name" type="text" class="w-full" placeholder="e.g. Read Quran" />
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

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label value="Tracking Type" />
                    <select wire:model.live="tracking_type" class="w-full bg-surface-container-low border-outline-variant/40 text-on-surface rounded-lg shadow-sm text-sm focus:border-command-blue focus:ring-command-blue">
                        <option value="boolean">Done / Not done</option>
                        <option value="quantity">Quantity</option>
                        <option value="duration">Duration</option>
                        <option value="count">Count</option>
                        <option value="time">Time target</option>
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

            @if (in_array($tracking_type, ['quantity', 'duration', 'count']))
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Target Value" />
                        <x-text-input wire:model="target_value" type="number" step="0.01" class="w-full" placeholder="e.g. 2.5" />
                    </div>
                    <div>
                        <x-input-label value="Unit" />
                        <x-text-input wire:model="target_unit" type="text" class="w-full" placeholder="e.g. liters, pages, minutes" />
                    </div>
                </div>
            @elseif ($tracking_type === 'time')
                <div>
                    <x-input-label value="Target Time" />
                    <x-text-input wire:model="target_time" type="time" class="w-full" />
                </div>
            @endif

            <div>
                <x-input-label value="Schedule" />
                <select wire:model.live="schedule_type" class="w-full bg-surface-container-low border-outline-variant/40 text-on-surface rounded-lg shadow-sm text-sm focus:border-command-blue focus:ring-command-blue">
                    <option value="daily">Every day</option>
                    <option value="specific_days">Specific days</option>
                    <option value="x_per_week">X times per week</option>
                    <option value="monthly">Monthly</option>
                </select>
            </div>

            @if ($schedule_type === 'specific_days')
                <div class="flex flex-wrap gap-2">
                    @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $index => $day)
                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container-low text-xs cursor-pointer">
                            <input type="checkbox" wire:model="schedule_days" value="{{ $index }}" class="rounded border-outline-variant/40 text-command-blue focus:ring-command-blue" />
                            {{ $day }}
                        </label>
                    @endforeach
                </div>
            @elseif ($schedule_type === 'x_per_week')
                <div>
                    <x-input-label value="Times per week" />
                    <x-text-input wire:model="schedule_times_per_week" type="number" min="1" max="7" class="w-full" />
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
                Mandatory habit
            </label>

            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', 'habit-form')">Cancel</x-secondary-button>
                <x-primary-button>{{ $editingId ? 'Save Changes' : 'Create Habit' }}</x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
