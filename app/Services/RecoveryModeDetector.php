<?php

namespace App\Services;

use App\Models\HabitLog;
use App\Models\TaskCompletion;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class RecoveryModeDetector
{
    public const RESET_CHECKLIST_LIMIT = 5;

    /**
     * @return 'none'|'inactive'|'easing_back'
     */
    public function status(User $user): string
    {
        $activeDates = $this->activeDates($user);

        if ($activeDates->isEmpty()) {
            return 'none';
        }

        $today = Carbon::today();
        $mostRecent = Carbon::parse($activeDates->first());
        $daysSince = $mostRecent->diffInDays($today);

        if ($daysSince >= 3) {
            return 'inactive';
        }

        $consecutive = $this->consecutiveActiveDays($activeDates, $mostRecent);

        return $consecutive < 3 ? 'easing_back' : 'none';
    }

    /**
     * Consecutive days (ending today or yesterday) with at least one completion —
     * a "did you show up" streak, distinct from any single habit/task's own streak.
     */
    public function currentActiveStreak(User $user): int
    {
        $activeDates = $this->activeDates($user);

        if ($activeDates->isEmpty()) {
            return 0;
        }

        $mostRecent = Carbon::parse($activeDates->first());

        if ($mostRecent->diffInDays(Carbon::today()) > 1) {
            return 0;
        }

        return $this->consecutiveActiveDays($activeDates, $mostRecent);
    }

    private function activeDates(User $user): Collection
    {
        $habitDates = HabitLog::where('user_id', $user->id)
            ->where('completed', true)
            ->pluck('date');

        $taskDates = TaskCompletion::where('user_id', $user->id)
            ->where('status', 'completed')
            ->pluck('date');

        return $habitDates->concat($taskDates)
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->unique()
            ->sortDesc()
            ->values();
    }

    private function consecutiveActiveDays(Collection $activeDates, Carbon $from): int
    {
        $set = $activeDates->flip();
        $count = 0;
        $cursor = $from->copy();

        while ($set->has($cursor->toDateString())) {
            $count++;
            $cursor->subDay();
        }

        return $count;
    }
}
