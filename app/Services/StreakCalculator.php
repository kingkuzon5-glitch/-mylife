<?php

namespace App\Services;

use App\Models\Habit;
use App\Models\Task;
use Illuminate\Support\Carbon;

class StreakCalculator
{
    public function recalculateHabit(Habit $habit): void
    {
        $completedDates = $habit->logs()
            ->where('completed', true)
            ->pluck('date')
            ->map(fn ($date) => $date->toDateString())
            ->flip();

        [$current, $best] = $this->walk(
            $habit->start_date,
            fn (Carbon $date) => $habit->isScheduledOn($date),
            $completedDates,
        );

        $habit->forceFill([
            'current_streak' => $current,
            'best_streak' => max($best, $habit->best_streak),
        ])->saveQuietly();
    }

    public function recalculateTask(Task $task): void
    {
        $completedDates = $task->completions()
            ->where('status', 'completed')
            ->pluck('date')
            ->map(fn ($date) => $date->toDateString())
            ->flip();

        [$current, $best] = $this->walk(
            $task->start_date,
            fn (Carbon $date) => $task->isDueOn($date),
            $completedDates,
        );

        $task->forceFill([
            'current_streak' => $current,
            'best_streak' => max($best, $task->best_streak),
        ])->saveQuietly();
    }

    /**
     * @param  \Illuminate\Support\Collection<string, int>  $completedDates
     * @return array{0: int, 1: int} [$current, $best]
     */
    private function walk(Carbon $startDate, \Closure $isScheduled, $completedDates): array
    {
        $start = $startDate->copy()->startOfDay();
        $today = Carbon::today();

        $best = 0;
        $run = 0;

        for ($date = $start->copy(); $date->lte($today); $date->addDay()) {
            if (! $isScheduled($date)) {
                continue;
            }

            if ($completedDates->has($date->toDateString())) {
                $run++;
                $best = max($best, $run);
            } else {
                $run = 0;
            }
        }

        $current = 0;

        for ($date = $today->copy(); $date->gte($start); $date->subDay()) {
            if (! $isScheduled($date)) {
                continue;
            }

            if ($completedDates->has($date->toDateString())) {
                $current++;

                continue;
            }

            if ($date->isSameDay($today)) {
                continue;
            }

            break;
        }

        return [$current, $best];
    }
}
