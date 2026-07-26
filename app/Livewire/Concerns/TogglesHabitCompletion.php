<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Carbon;

trait TogglesHabitCompletion
{
    public function toggleHabit(int $habitId): void
    {
        $habit = auth()->user()->habits()->findOrFail($habitId);
        $this->authorize('view', $habit);

        $today = Carbon::today()->toDateString();
        $log = $habit->logs()->whereDate('date', $today)->first();

        if ($log) {
            $log->delete();
        } else {
            $habit->logs()->create([
                'user_id' => auth()->id(),
                'date' => $today,
                'value' => $habit->target_value,
                'completed' => true,
                'logged_at' => now(),
            ]);
        }
    }
}
