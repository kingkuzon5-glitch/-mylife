@props(['color' => 'discipline-green'])

@php
    $classes = match ($color) {
        'command-blue' => 'text-command-blue bg-command-blue/10',
        'focus-orange' => 'text-focus-orange bg-focus-orange/10',
        'error' => 'text-error bg-error/10',
        'muted' => 'text-on-surface-variant bg-surface-container-high',
        default => 'text-discipline-green bg-discipline-green/10',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 text-[11px] font-extrabold tracking-wider px-2 py-0.5 rounded-full $classes"]) }}>
    {{ $slot }}
</span>
