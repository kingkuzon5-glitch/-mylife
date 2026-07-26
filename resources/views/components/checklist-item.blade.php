@props(['icon', 'title', 'subtitle' => null, 'checked' => false, 'color' => 'command-blue'])

@php
    $iconWrap = match ($color) {
        'discipline-green' => 'bg-discipline-green/5 group-hover:bg-discipline-green/10 text-discipline-green',
        'focus-orange' => 'bg-focus-orange/5 group-hover:bg-focus-orange/10 text-focus-orange',
        default => 'bg-command-blue/5 group-hover:bg-command-blue/10 text-command-blue',
    };

    $borderHover = match ($color) {
        'discipline-green' => 'hover:border-l-discipline-green hover:shadow-discipline-green/10',
        'focus-orange' => 'hover:border-l-focus-orange hover:shadow-focus-orange/10',
        default => 'hover:border-l-command-blue hover:shadow-command-blue/10',
    };
@endphp

<div
    {{ $attributes->merge(['class' => "glass-panel rounded-xl p-4 sm:p-5 flex items-center gap-4 sm:gap-6 group hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300 cursor-pointer border-l-4 {$borderHover} " . ($checked ? 'border-l-discipline-green' : 'border-l-transparent')]) }}
>
    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center shrink-0 transition-colors {{ $iconWrap }}">
        <x-icon :name="$icon" fill class="text-xl sm:text-2xl" />
    </div>

    <div class="flex-1 min-w-0">
        <h4 class="font-bold text-on-surface truncate">{{ $title }}</h4>
        @if ($subtitle)
            <p class="text-sm text-on-surface-variant mt-0.5 truncate">{{ $subtitle }}</p>
        @endif
    </div>

    <div
        @class([
            'w-8 h-8 rounded-md flex items-center justify-center shrink-0 transition-all',
            'border-2 border-discipline-green bg-discipline-green shadow-[0_0_10px_rgba(16,185,129,0.3)]' => $checked,
            'border-2 border-outline-variant/50 group-hover:border-command-blue' => ! $checked,
        ])
    >
        <x-icon
            name="check"
            @class([
                'text-lg font-bold transition-all',
                'text-white scale-100' => $checked,
                'text-command-blue opacity-0 group-hover:opacity-100 scale-75 group-hover:scale-100' => ! $checked,
            ])
        />
    </div>
</div>
