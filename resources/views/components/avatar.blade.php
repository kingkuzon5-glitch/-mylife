@props(['name'])

@php
    $initials = collect(explode(' ', trim($name)))
        ->filter()
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->take(2)
        ->implode('');
@endphp

<div {{ $attributes->merge(['class' => 'w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center text-xs font-extrabold shrink-0']) }}>
    {{ $initials ?: '?' }}
</div>
