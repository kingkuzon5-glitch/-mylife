@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-surface-container-low border border-outline-variant/40 text-on-surface placeholder:text-on-surface-variant/60 focus:border-command-blue focus:ring-command-blue rounded-xl shadow-sm text-sm px-4 py-2.5']) }}>
