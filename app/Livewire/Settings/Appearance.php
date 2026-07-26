<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class Appearance extends Component
{
    public string $theme = 'system';

    public function mount(): void
    {
        $this->theme = auth()->user()->settings['theme'] ?? 'system';
    }

    public function setTheme(string $theme): void
    {
        if (! in_array($theme, ['light', 'dark', 'system'], true)) {
            return;
        }

        $this->theme = $theme;

        $user = auth()->user();
        $user->settings = [...($user->settings ?? []), 'theme' => $theme];
        $user->save();

        $this->dispatch('theme-changed', theme: $theme);
    }

    public function render()
    {
        return view('livewire.settings.appearance');
    }
}
