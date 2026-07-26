<?php
$circumference = round(2 * M_PI * 74, 2);
$scoreOffset = round($circumference * (1 - ($disciplineScore->overall_score / 100)), 2);
$hour = now()->hour;
$greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
?>
<div x-data="{
    focusActive: false, remaining: 0, total: 0, label: '', timer: null,
    startFocus(minutes, label) {
        this.total = minutes * 60; this.remaining = this.total; this.label = label; this.focusActive = true;
        clearInterval(this.timer);
        this.timer = setInterval(() => {
            this.remaining--;
            if (this.remaining <= 0) { this.finishFocus(); }
        }, 1000);
    },
    finishFocus() {
        clearInterval(this.timer);
        this.focusActive = false;
        $wire.logFocusSession(Math.round(this.total / 60));
    },
    cancelFocus() { clearInterval(this.timer); this.focusActive = false; },
    get display() {
        const m = Math.floor(this.remaining / 60).toString().padStart(2, '0');
        const s = (this.remaining % 60).toString().padStart(2, '0');
        return m + ':' + s;
    }
}" class="space-y-6">

    {{-- Focus timer overlay --}}
    <div
        x-show="focusActive"
        x-transition.opacity
        class="fixed inset-0 z-[60] bg-on-surface/70 backdrop-blur-md flex flex-col items-center justify-center gap-6"
        style="display: none"
    >
        <span class="text-label-caps uppercase tracking-widest text-white/70 font-bold" x-text="label"></span>
        <span class="text-7xl sm:text-8xl font-black tracking-tighter text-white tabular-nums" x-text="display"></span>
        <button @click="cancelFocus()" class="px-6 py-2.5 rounded-lg border border-white/30 text-white text-xs font-bold uppercase tracking-widest hover:bg-white/10 transition-colors">
            Stop
        </button>
    </div>

    {{-- Greeting + Progress + Score --}}
    <section class="grid grid-cols-12 gap-6 items-stretch">
        <x-glass-panel class="col-span-12 lg:col-span-8 p-6 sm:p-8 flex flex-col justify-between relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-headline-lg sm:text-headline-xl font-extrabold tracking-tight text-on-surface mb-1">{{ strtoupper($greeting) }}, {{ strtoupper(explode(' ', auth()->user()->name)[0]) }}</h2>
                <p class="text-body-lg text-on-surface-variant">{{ now()->format('l, F j') }} — System Active.</p>

                <div class="mt-8 sm:mt-12 flex flex-col sm:flex-row sm:items-end gap-8">
                    <div class="flex-1 min-w-[180px]">
                        <div class="flex justify-between items-end mb-3">
                            <span class="text-label-caps uppercase tracking-widest font-bold text-on-surface-variant">Daily Progress</span>
                            <span class="font-bold text-lg text-discipline-green">{{ $dailyProgress }}%</span>
                        </div>
                        <x-progress-bar :percent="$dailyProgress" class="h-4" />
                    </div>
                    <div class="flex gap-8 sm:border-l border-on-surface-variant/10 sm:pl-8">
                        <div class="text-center">
                            <span class="block text-[10px] uppercase tracking-widest text-on-surface-variant mb-1 font-bold">Completed</span>
                            <span class="block text-2xl font-bold text-on-surface">{{ $completedCount }} / {{ $totalCount }}</span>
                        </div>
                        <div class="text-center">
                            <span class="block text-[10px] uppercase tracking-widest text-on-surface-variant mb-1 font-bold">Streak</span>
                            <span class="block text-2xl font-bold text-discipline-green">{{ $streak }} {{ $streak === 1 ? 'Day' : 'Days' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </x-glass-panel>

        <x-glass-panel class="col-span-12 lg:col-span-4 p-6 sm:p-8 flex flex-col items-center justify-center text-center">
            <div class="relative w-40 h-40 flex items-center justify-center mb-4">
                <svg class="absolute inset-0 w-full h-full -rotate-90">
                    <circle class="text-surface-container-highest/60" cx="50%" cy="50%" fill="none" r="74" stroke="currentColor" stroke-width="8" />
                    <circle
                        class="text-discipline-green transition-all duration-1000 ease-out"
                        cx="50%" cy="50%" fill="none" r="74"
                        stroke="currentColor" stroke-width="8" stroke-linecap="round"
                        stroke-dasharray="{{ $circumference }}"
                        stroke-dashoffset="{{ $scoreOffset }}"
                    />
                </svg>
                <div class="flex flex-col items-center z-10">
                    <span class="text-[44px] sm:text-[48px] font-black tracking-tighter text-on-surface">{{ $disciplineScore->overall_score }}</span>
                    <span class="text-[10px] uppercase tracking-widest text-on-surface-variant -mt-1 font-bold">Score</span>
                </div>
            </div>
            <h3 class="text-label-caps uppercase tracking-widest mb-2 font-bold text-on-surface">Discipline Index</h3>
            @if (count($disciplineScore->breakdown ?? []) > 0)
                <div class="flex flex-wrap justify-center gap-x-3 gap-y-1 px-2">
                    @foreach ($disciplineScore->breakdown as $category => $percent)
                        <span class="text-[11px] text-on-surface-variant">{{ $category }} <span class="font-bold text-on-surface">{{ $percent }}%</span></span>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-on-surface-variant px-4 leading-relaxed">Nothing scheduled yet — add a habit or task to start tracking.</p>
            @endif
        </x-glass-panel>
    </section>

    <div class="grid grid-cols-12 gap-6">
        {{-- Left column --}}
        <div class="col-span-12 lg:col-span-8 space-y-6">
            @if ($recoveryStatus === 'inactive')
                <div class="p-5 sm:p-6 bg-surface-container-lowest/60 border border-dashed border-focus-orange/40 rounded-2xl">
                    <div class="flex items-start gap-4">
                        <x-icon name="history_edu" class="text-focus-orange mt-0.5" />
                        <div>
                            <span class="block text-label-caps uppercase text-focus-orange mb-1 font-bold tracking-widest">Recovery Mode</span>
                            <p class="text-sm text-on-surface-variant leading-relaxed">You've been away for a few days. That's okay — don't try to fix everything at once. Here's a short reset list to get moving again. <span class="font-semibold text-on-surface">Never miss twice.</span></p>
                        </div>
                    </div>
                </div>
            @elseif ($recoveryStatus === 'easing_back')
                <div class="p-4 bg-discipline-green/5 border border-discipline-green/20 rounded-xl text-sm text-on-surface-variant">
                    <span class="font-semibold text-discipline-green">You're building it back.</span> Keep today going — three days in a row resets the streak fully.
                </div>
            @endif

            <div class="flex items-center justify-between px-1">
                <h3 class="text-label-caps uppercase tracking-widest text-on-surface-variant font-bold">
                    {{ $recoveryStatus === 'inactive' ? "Today's Reset" : "Today's Focus" }}
                </h3>
                <span class="text-xs text-on-surface-variant">{{ $completedCount }}/{{ $totalCount }}</span>
            </div>

            @if ($totalCount === 0)
                <x-glass-panel class="p-8 text-center">
                    <p class="text-sm text-on-surface-variant">Nothing due today. Add a habit or task to get started.</p>
                </x-glass-panel>
            @else
                <div class="space-y-3">
                    @foreach ($checklist as $item)
                        <x-checklist-item
                            wire:key="{{ $item['type'] }}-{{ $item['id'] }}"
                            wire:click="{{ $item['type'] === 'task' ? 'toggleTask' : 'toggleHabit' }}({{ $item['id'] }})"
                            :icon="$item['icon']"
                            :title="$item['name']"
                            :subtitle="$item['subtitle']"
                            :checked="$item['completed']"
                            :color="$item['completed'] ? 'discipline-green' : 'command-blue'"
                        />
                    @endforeach
                </div>
            @endif

            @if ($routineItems->isNotEmpty())
                <div class="pt-4">
                    <h3 class="text-label-caps uppercase tracking-widest text-on-surface-variant mb-4 px-1 font-bold">Daily Routine</h3>
                    <x-glass-panel class="p-6 sm:p-8">
                        <div class="flex items-start gap-0 overflow-x-auto hide-scrollbar pb-2 relative">
                            <div class="absolute top-[27px] left-0 h-[2px] bg-surface-container-highest w-full -z-0"></div>
                            @foreach ($routineItems as $item)
                                @php $isCurrent = $currentRoutineItem && $currentRoutineItem->id === $item->id; @endphp
                                <div class="flex-none w-40 text-center relative z-10 {{ $isCurrent ? 'pb-2 border-b-2 border-command-blue' : 'opacity-60' }}">
                                    <span class="block text-xs mb-2 tracking-wider {{ $isCurrent ? 'text-command-blue font-bold' : 'text-on-surface-variant' }}">{{ \Illuminate\Support\Carbon::parse($item->start_time)->format('H:i') }}</span>
                                    <div class="w-3.5 h-3.5 rounded-full mx-auto mb-3 border-4 border-surface-container-lowest {{ $isCurrent ? 'bg-command-blue shadow-[0_0_10px_rgba(37,99,235,0.6)]' : 'bg-on-surface-variant' }}"></div>
                                    <span class="text-[10px] uppercase tracking-widest font-bold {{ $isCurrent ? 'text-on-surface' : 'text-on-surface-variant' }}">{{ $item->title }}</span>
                                </div>
                            @endforeach
                        </div>
                    </x-glass-panel>
                </div>
            @endif
        </div>

        {{-- Right column --}}
        <div class="col-span-12 lg:col-span-4 space-y-6">
            <div class="bg-primary text-on-primary rounded-2xl p-6 sm:p-8 space-y-5 shadow-2xl relative overflow-hidden">
                <div class="flex items-center gap-3 relative z-10">
                    <x-icon name="bolt" class="text-discipline-green" />
                    <h3 class="text-label-caps uppercase tracking-widest font-bold">Focus Mode</h3>
                </div>
                <p class="text-sm text-on-primary/80 relative z-10 leading-relaxed">Eliminate distractions and enter a flow state immediately.</p>
                <div class="grid grid-cols-1 gap-3 relative z-10">
                    <button @click="startFocus(25, 'Focus')" class="w-full bg-on-primary text-primary text-xs uppercase py-3.5 rounded-xl flex items-center justify-center gap-2 hover:opacity-90 transition-all font-bold tracking-widest">
                        <x-icon name="timer" class="text-[18px]" /> Start 25m Focus
                    </button>
                    <button @click="startFocus(50, 'Deep Work')" class="w-full border border-on-primary/20 bg-on-primary/5 text-on-primary text-xs uppercase py-3.5 rounded-xl flex items-center justify-center gap-2 hover:bg-on-primary/10 transition-all font-bold tracking-widest">
                        <x-icon name="military_tech" class="text-[18px]" /> Start 50m Deep Work
                    </button>
                </div>
            </div>

            @if (count($vitals) > 0)
                <div class="space-y-3">
                    <h3 class="text-label-caps uppercase tracking-widest text-on-surface-variant px-1 font-bold">System Vitals</h3>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach ($vitals as $vital)
                            <x-stat-tile
                                :icon="$vital['icon']"
                                :label="$vital['category']"
                                :value="$vital['value']"
                                :badge="$vital['percentage'].'%'"
                                :color="$vital['percentage'] >= 100 ? 'discipline-green' : ($vital['percentage'] >= 50 ? 'command-blue' : 'focus-orange')"
                            />
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
