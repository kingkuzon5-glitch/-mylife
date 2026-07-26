@props(['icon', 'label', 'value', 'badge' => null, 'color' => 'discipline-green'])

@php
    $iconWrap = match ($color) {
        'command-blue' => 'bg-command-blue/5 group-hover:bg-command-blue/10 text-command-blue',
        'focus-orange' => 'bg-focus-orange/5 group-hover:bg-focus-orange/10 text-focus-orange',
        default => 'bg-discipline-green/5 group-hover:bg-discipline-green/10 text-discipline-green',
    };
@endphp

<div {{ $attributes->merge(['class' => 'glass-panel rounded-xl p-5 hover:bg-surface-container-lowest/60 transition-colors group']) }}>
    <div class="flex items-center justify-between mb-4">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ $iconWrap }}">
            <x-icon :name="$icon" class="text-lg" />
        </div>
        @if ($badge)
            <x-badge :color="$color">{{ $badge }}</x-badge>
        @endif
    </div>
    <span class="block text-[10px] uppercase tracking-widest text-on-surface-variant/80 mb-1 font-bold">{{ $label }}</span>
    <span class="block text-[15px] font-black tracking-tight text-on-surface">{{ $value }}</span>
</div>
