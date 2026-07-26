<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-5 py-2.5 bg-surface-container-low border border-outline-variant/40 rounded-lg font-bold text-xs text-on-surface uppercase tracking-widest hover:bg-surface-container-high focus:outline-none focus:ring-2 focus:ring-command-blue focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
