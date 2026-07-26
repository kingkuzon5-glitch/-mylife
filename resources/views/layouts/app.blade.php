<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($header) ? $header . ' — ' : '' }}{{ config('app.name', 'Axiom') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">

        <script>
            function applyAxiomTheme(pref) {
                const isDark = pref === 'dark' || (pref !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', isDark);
            }

            (function () {
                const stored = @json(auth()->user()?->settings['theme'] ?? null);
                const pref = stored ?? localStorage.theme ?? 'system';
                localStorage.theme = pref;
                applyAxiomTheme(pref);
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body
        x-data
        x-on:theme-changed.window="
            localStorage.theme = $event.detail.theme;
            applyAxiomTheme($event.detail.theme);
        "
        class="text-on-surface font-sans antialiased overflow-x-hidden min-h-screen bg-background"
    >
        <div x-data="{ sidebarOpen: false }" class="min-h-screen">
            <div
                x-show="sidebarOpen"
                x-transition.opacity
                @click="sidebarOpen = false"
                class="fixed inset-0 bg-on-surface/40 z-40 lg:hidden"
                style="display: none"
            ></div>

            <aside
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="h-screen w-64 fixed left-0 top-0 flex flex-col glass-panel z-50 border-r border-on-surface-variant/10 transition-transform duration-300 lg:translate-x-0"
            >
                <div class="flex flex-col h-full py-6 px-4">
                    <div class="mb-8 px-2 flex items-center justify-between">
                        <div>
                            <a href="{{ route('dashboard') }}" wire:navigate class="font-extrabold tracking-tight text-2xl text-primary">AXIOM</a>
                            <span class="block text-[10px] font-bold text-on-surface-variant tracking-widest opacity-60">V1.0</span>
                        </div>
                        <button @click="sidebarOpen = false" class="lg:hidden text-on-surface-variant">
                            <x-icon name="close" />
                        </button>
                    </div>

                    <nav class="flex-1 space-y-1 overflow-y-auto hide-scrollbar">
                        @foreach ($axiomNav ?? [] as $item)
                            @php $active = request()->routeIs($item['active']); @endphp
                            <a
                                href="{{ $item['route'] }}"
                                wire:navigate
                                @click="sidebarOpen = false"
                                @class([
                                    'flex items-center gap-3 px-3 py-2 transition-colors duration-150 rounded-lg text-sm',
                                    'text-primary font-bold bg-surface-container-low/80 shadow-sm' => $active,
                                    'text-on-surface-variant hover:bg-surface-container-high/50 font-medium' => ! $active,
                                ])
                            >
                                <x-icon :name="$item['icon']" :fill="$active" class="text-[20px]" />
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>

                    <div class="mt-auto space-y-1 pt-4 border-t border-on-surface-variant/10">
                        <a href="{{ route('support') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-sm text-on-surface-variant hover:text-on-surface transition-colors">
                            <x-icon name="help" class="text-[20px]" /> Support
                        </a>
                        <a href="{{ route('archive') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-sm text-on-surface-variant hover:text-on-surface transition-colors">
                            <x-icon name="archive" class="text-[20px]" /> Archive
                        </a>

                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="w-full flex items-center gap-3 px-3 py-3 mt-2 rounded-lg hover:bg-surface-container-high/50 transition-colors">
                                <x-avatar :name="auth()->user()->name" />
                                <div class="flex flex-col items-start min-w-0">
                                    <span class="text-xs font-extrabold leading-none truncate max-w-[9rem]">{{ auth()->user()->name }}</span>
                                    <span class="text-[10px] text-on-surface-variant tracking-wider font-medium">VIEW ACCOUNT</span>
                                </div>
                            </button>
                            <div
                                x-show="open"
                                x-transition
                                class="absolute bottom-full mb-2 left-0 w-full rounded-lg glass-panel shadow-lg overflow-hidden"
                                style="display: none"
                            >
                                <a href="{{ route('profile') }}" wire:navigate class="block px-4 py-2.5 text-sm text-on-surface hover:bg-surface-container-high/50">Profile</a>
                                <a href="{{ route('settings.appearance') }}" wire:navigate class="block px-4 py-2.5 text-sm text-on-surface hover:bg-surface-container-high/50">Settings</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-start block px-4 py-2.5 text-sm text-error hover:bg-error/10">Log Out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <header class="sticky top-0 z-30 glass-panel border-b border-on-surface-variant/10 flex justify-between items-center w-full px-4 sm:px-6 lg:px-10 h-16 lg:ml-64">
                <div class="flex items-center gap-4 min-w-0">
                    <button @click="sidebarOpen = true" class="lg:hidden text-on-surface-variant">
                        <x-icon name="menu" />
                    </button>
                    <span class="font-black uppercase tracking-tighter text-lg truncate">{{ $header ?? 'Axiom' }}</span>
                </div>

                <div class="flex items-center gap-3 sm:gap-6">
                    <div class="relative hidden sm:block">
                        <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-base" />
                        <button
                            x-on:click="$dispatch('open-modal', 'quick-add')"
                            class="pl-9 pr-4 py-1.5 bg-surface-container-low/50 border border-outline-variant/30 rounded-lg text-xs w-56 text-left text-on-surface-variant hover:border-command-blue/40 transition-all"
                        >Quick add…</button>
                    </div>
                    <a href="{{ route('dashboard') }}" wire:navigate class="text-on-surface-variant hover:text-primary transition-colors" title="Focus Mode">
                        <x-icon name="timer" />
                    </a>
                    <button
                        x-on:click="$dispatch('open-modal', 'quick-add')"
                        class="bg-primary text-on-primary px-4 sm:px-5 py-2 text-xs font-bold uppercase tracking-widest rounded-lg hover:opacity-90 transition-all shadow-lg"
                    >+ Add</button>
                </div>
            </header>

            <main class="lg:ml-64 p-4 sm:p-6 lg:p-10 space-y-6 max-w-[1280px] mx-auto">
                {{ $slot }}
            </main>
        </div>

        <livewire:shared.quick-add />

        <x-app-loader />

        <script>
            document.addEventListener('livewire:init', () => {
                window.addEventListener('keydown', (e) => {
                    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                        e.preventDefault();
                        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'quick-add' }));
                    }
                });

                Alpine.store('axiomLoader', { visible: false });

                const SHOW_DELAY = 150;
                const MIN_VISIBLE = 250;
                let pending = 0;
                let showTimer = null;
                let shownAt = null;

                function startLoading() {
                    pending++;
                    if (showTimer || Alpine.store('axiomLoader').visible) return;
                    showTimer = setTimeout(() => {
                        showTimer = null;
                        shownAt = Date.now();
                        Alpine.store('axiomLoader').visible = true;
                    }, SHOW_DELAY);
                }

                function stopLoading() {
                    pending = Math.max(0, pending - 1);
                    if (pending > 0) return;

                    if (showTimer) {
                        clearTimeout(showTimer);
                        showTimer = null;
                        return;
                    }

                    if (!Alpine.store('axiomLoader').visible) return;

                    const remaining = Math.max(0, MIN_VISIBLE - (Date.now() - shownAt));
                    setTimeout(() => { Alpine.store('axiomLoader').visible = false; }, remaining);
                }

                Livewire.hook('request', ({ succeed, fail }) => {
                    startLoading();
                    succeed(() => stopLoading());
                    fail(() => stopLoading());
                });

                document.addEventListener('livewire:navigating', startLoading);
                document.addEventListener('livewire:navigated', stopLoading);
            });
        </script>
    </body>
</html>
