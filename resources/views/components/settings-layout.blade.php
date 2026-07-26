@php
    $tabs = [
        ['label' => 'Profile', 'icon' => 'person', 'route' => route('profile'), 'active' => 'profile'],
        ['label' => 'Categories', 'icon' => 'category', 'route' => route('settings.categories'), 'active' => 'settings.categories'],
        ['label' => 'Appearance', 'icon' => 'palette', 'route' => route('settings.appearance'), 'active' => 'settings.appearance'],
    ];
@endphp

<div class="grid grid-cols-1 lg:grid-cols-[220px_1fr] gap-6">
    <nav class="flex lg:flex-col gap-1 overflow-x-auto hide-scrollbar">
        @foreach ($tabs as $tab)
            @php $active = request()->routeIs($tab['active']); @endphp
            <a
                href="{{ $tab['route'] }}"
                wire:navigate
                @class([
                    'flex items-center gap-3 px-3 py-2 rounded-lg text-sm whitespace-nowrap transition-colors',
                    'bg-surface-container-low font-bold text-on-surface' => $active,
                    'text-on-surface-variant hover:bg-surface-container-high/50 font-medium' => ! $active,
                ])
            >
                <x-icon :name="$tab['icon']" :fill="$active" class="text-[20px]" />
                {{ $tab['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="space-y-6 min-w-0">
        {{ $slot }}
    </div>
</div>
