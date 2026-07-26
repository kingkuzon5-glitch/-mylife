@props(['percent' => 0, 'color' => 'discipline-green'])

@php
    $percent = max(0, min(100, (int) $percent));

    $barColor = match ($color) {
        'command-blue' => 'bg-command-blue',
        'focus-orange' => 'bg-focus-orange',
        'error' => 'bg-error',
        default => 'bg-discipline-green',
    };
@endphp

<div {{ $attributes->merge(['class' => 'h-3 bg-surface-container-highest/60 w-full rounded-full overflow-hidden shadow-inner']) }}>
    <div
        class="h-full {{ $barColor }} transition-all duration-700 ease-out rounded-full"
        style="width: {{ $percent }}%"
    ></div>
</div>
