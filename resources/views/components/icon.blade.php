@props(['name', 'fill' => false])

<span {{ $attributes->merge(['class' => 'material-symbols-outlined select-none'.($fill ? ' icon-fill' : '')]) }}>{{ $name }}</span>
