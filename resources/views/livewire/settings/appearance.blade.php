<x-settings-layout>
    <x-glass-panel class="p-6 sm:p-8 space-y-4 max-w-xl">
        <h3 class="text-headline-md font-bold text-on-surface">Appearance</h3>
        <p class="text-sm text-on-surface-variant">Choose how Axiom looks on this device.</p>

        <div class="grid grid-cols-3 gap-3">
            @foreach (['light' => ['Light', 'light_mode'], 'dark' => ['Dark', 'dark_mode'], 'system' => ['System', 'contrast']] as $value => [$label, $icon])
                <button
                    wire:click="setTheme('{{ $value }}')"
                    @class([
                        'flex flex-col items-center gap-2 p-5 rounded-xl border-2 transition-all',
                        'border-command-blue bg-command-blue/5' => $theme === $value,
                        'border-outline-variant/30 hover:border-outline-variant/60' => $theme !== $value,
                    ])
                >
                    <x-icon :name="$icon" class="text-2xl {{ $theme === $value ? 'text-command-blue' : 'text-on-surface-variant' }}" />
                    <span class="text-xs font-bold {{ $theme === $value ? 'text-command-blue' : 'text-on-surface-variant' }}">{{ $label }}</span>
                </button>
            @endforeach
        </div>
    </x-glass-panel>
</x-settings-layout>
