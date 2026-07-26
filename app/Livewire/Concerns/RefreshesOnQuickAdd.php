<?php

namespace App\Livewire\Concerns;

use Livewire\Attributes\On;

trait RefreshesOnQuickAdd
{
    #[On('quick-added')]
    public function refreshAfterQuickAdd(): void
    {
        // Intentionally empty — receiving the event is enough to trigger a re-render.
    }
}
