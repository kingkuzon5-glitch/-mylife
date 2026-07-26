<x-app-layout>
    <x-slot name="header">Settings</x-slot>

    <x-settings-layout>
        <x-glass-panel class="p-6 sm:p-8">
            <livewire:profile.update-profile-information-form />
        </x-glass-panel>

        <x-glass-panel class="p-6 sm:p-8">
            <livewire:profile.update-password-form />
        </x-glass-panel>

        <x-glass-panel class="p-6 sm:p-8">
            <livewire:profile.delete-user-form />
        </x-glass-panel>
    </x-settings-layout>
</x-app-layout>
