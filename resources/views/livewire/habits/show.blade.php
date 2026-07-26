<div class="space-y-6">
    <a href="{{ route('habits.index') }}" wire:navigate class="inline-flex items-center gap-2 text-sm text-on-surface-variant hover:text-on-surface transition-colors">
        <x-icon name="arrow_back" class="text-base" /> Back to Habits
    </a>

    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-discipline-green/10 flex items-center justify-center text-discipline-green">
                <x-icon :name="$habit->icon" fill class="text-2xl" />
            </div>
            <div>
                <h2 class="text-headline-lg font-bold text-on-surface">{{ $habit->name }}</h2>
                <p class="text-sm text-on-surface-variant">{{ $habit->category->name ?? 'General' }} · {{ ucfirst(str_replace('_', ' ', $habit->schedule_type)) }}</p>
            </div>
        </div>

        <button
            wire:click="toggleHabit({{ $habit->id }})"
            @class([
                'px-5 py-2.5 rounded-lg text-xs font-bold uppercase tracking-widest flex items-center gap-2 transition-all',
                'bg-discipline-green text-white shadow-lg' => $todayLog?->completed,
                'bg-primary text-on-primary' => ! $todayLog?->completed,
            ])
        >
            <x-icon name="check" class="text-base" /> {{ $todayLog?->completed ? 'Completed Today' : 'Mark Complete' }}
        </button>
    </div>

    @if ($habit->description)
        <p class="text-sm text-on-surface-variant">{{ $habit->description }}</p>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-glass-panel class="p-5">
            <span class="flex items-center gap-1.5 text-discipline-green font-black text-2xl">
                <x-icon name="local_fire_department" fill /> {{ $habit->current_streak }}
            </span>
            <span class="text-[10px] uppercase tracking-widest text-on-surface-variant font-bold">Current Streak</span>
        </x-glass-panel>
        <x-glass-panel class="p-5">
            <span class="flex items-center gap-1.5 text-on-surface font-black text-2xl">
                <x-icon name="emoji_events" fill class="text-focus-orange" /> {{ $habit->best_streak }}
            </span>
            <span class="text-[10px] uppercase tracking-widest text-on-surface-variant font-bold">Best Streak</span>
        </x-glass-panel>
        <x-glass-panel class="p-5">
            <span class="block text-on-surface font-black text-2xl">{{ $month['percent'] }}%</span>
            <span class="text-[10px] uppercase tracking-widest text-on-surface-variant font-bold">Monthly Completion</span>
        </x-glass-panel>
        <x-glass-panel class="p-5">
            <span class="block text-on-surface font-black text-2xl">{{ $totalCompletions }}</span>
            <span class="text-[10px] uppercase tracking-widest text-on-surface-variant font-bold">Total Completions</span>
        </x-glass-panel>
    </div>

    <x-glass-panel class="p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-label-caps uppercase tracking-widest text-on-surface-variant font-bold">Last 17 Weeks</h3>
            <div class="flex items-center gap-1.5 text-[10px] text-on-surface-variant">
                <span class="w-2.5 h-2.5 rounded-sm bg-surface-container-high"></span> Missed
                <span class="w-2.5 h-2.5 rounded-sm bg-discipline-green ml-2"></span> Done
            </div>
        </div>
        <div class="overflow-x-auto hide-scrollbar">
            <div class="grid grid-flow-col gap-1" style="grid-template-rows: repeat(7, minmax(0, 1fr));">
                @foreach ($heatmap as $heatmapWeek)
                    @foreach ($heatmapWeek as $day)
                        <div
                            title="{{ $day['label'] }}"
                            @class([
                                'w-3.5 h-3.5 rounded-sm',
                                'bg-discipline-green' => $day['completed'],
                                'bg-surface-container-high' => ! $day['completed'] && $day['scheduled'] && ! $day['future'],
                                'bg-transparent' => ! $day['scheduled'] || $day['future'],
                            ])
                        ></div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </x-glass-panel>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-glass-panel class="p-5">
            <span class="block text-on-surface font-bold text-lg">{{ $week['completed'] }}/{{ $week['scheduled'] }}</span>
            <span class="text-[10px] uppercase tracking-widest text-on-surface-variant font-bold">This Week</span>
        </x-glass-panel>
        <x-glass-panel class="p-5">
            <span class="block text-on-surface font-bold text-lg">{{ $month['completed'] }}/{{ $month['scheduled'] }}</span>
            <span class="text-[10px] uppercase tracking-widest text-on-surface-variant font-bold">This Month</span>
        </x-glass-panel>
        <x-glass-panel class="p-5">
            <span class="block text-on-surface font-bold text-lg">{{ $year['completed'] }}/{{ $year['scheduled'] }}</span>
            <span class="text-[10px] uppercase tracking-widest text-on-surface-variant font-bold">This Year</span>
        </x-glass-panel>
    </div>
</div>
