<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2.5 bg-primary border border-transparent rounded-lg font-bold text-xs text-on-primary uppercase tracking-widest hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-command-blue focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-40']) }}>
    {{ $slot }}
</button>
