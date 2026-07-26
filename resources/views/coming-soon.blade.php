<x-app-layout>
    <div class="flex items-center justify-center min-h-[60vh]">
        <x-glass-panel class="max-w-md w-full p-10 text-center space-y-4">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-surface-container-high flex items-center justify-center">
                <x-icon :name="$icon" class="text-3xl text-on-surface-variant" />
            </div>
            <h2 class="text-headline-md font-bold text-on-surface">{{ $title }}</h2>
            <p class="text-sm text-on-surface-variant leading-relaxed">{{ $description }}</p>
            <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-bold text-command-blue hover:text-blue-700 transition-colors">
                <x-icon name="arrow_back" class="text-base" /> Back to Today
            </a>
        </x-glass-panel>
    </div>
</x-app-layout>
