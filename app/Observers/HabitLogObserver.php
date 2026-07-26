<?php

namespace App\Observers;

use App\Models\HabitLog;
use App\Services\StreakCalculator;

class HabitLogObserver
{
    public function __construct(private StreakCalculator $streaks) {}

    public function saved(HabitLog $habitLog): void
    {
        $this->streaks->recalculateHabit($habitLog->habit);
    }

    public function deleted(HabitLog $habitLog): void
    {
        $this->streaks->recalculateHabit($habitLog->habit);
    }
}
