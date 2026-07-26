<?php

namespace App\Services;

use App\Models\DisciplineScore;
use App\Models\HabitLog;
use App\Models\TaskCompletion;
use App\Models\User;
use Illuminate\Support\Carbon;

class DisciplineScoreCalculator
{
    private const MANDATORY_WEIGHT = 2;

    private const OPTIONAL_WEIGHT = 1;

    /**
     * @return array{overall: int, breakdown: array<string, int>}
     */
    public function forUser(User $user, ?Carbon $date = null): array
    {
        $date = ($date ?? Carbon::today())->copy()->startOfDay();

        $tasks = $user->tasks()->active()->with('category')->get()
            ->filter(fn ($task) => $task->isDueOn($date));

        $habits = $user->habits()->active()->with('category')->get()
            ->filter(fn ($habit) => $habit->isScheduledOn($date));

        $completedTaskIds = TaskCompletion::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->where('status', 'completed')
            ->pluck('task_id')->flip();

        $completedHabitIds = HabitLog::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->where('completed', true)
            ->pluck('habit_id')->flip();

        $categoryTotals = [];
        $totalEarned = 0;
        $totalPossible = 0;

        $accumulate = function ($item, bool $isCompleted) use (&$categoryTotals, &$totalEarned, &$totalPossible) {
            $weight = $item->is_mandatory ? self::MANDATORY_WEIGHT : self::OPTIONAL_WEIGHT;
            $categoryName = $item->category->name ?? 'General';

            $categoryTotals[$categoryName]['possible'] = ($categoryTotals[$categoryName]['possible'] ?? 0) + $weight;
            $totalPossible += $weight;

            if ($isCompleted) {
                $categoryTotals[$categoryName]['earned'] = ($categoryTotals[$categoryName]['earned'] ?? 0) + $weight;
                $totalEarned += $weight;
            }
        };

        foreach ($tasks as $task) {
            $accumulate($task, $completedTaskIds->has($task->id));
        }

        foreach ($habits as $habit) {
            $accumulate($habit, $completedHabitIds->has($habit->id));
        }

        $overall = $totalPossible > 0 ? (int) round(($totalEarned / $totalPossible) * 100) : 100;

        $breakdown = [];

        foreach ($categoryTotals as $name => $totals) {
            $possible = $totals['possible'] ?? 0;
            $earned = $totals['earned'] ?? 0;
            $breakdown[$name] = $possible > 0 ? (int) round(($earned / $possible) * 100) : 0;
        }

        return ['overall' => $overall, 'breakdown' => $breakdown];
    }

    public function computeAndStore(User $user, ?Carbon $date = null): DisciplineScore
    {
        $date = ($date ?? Carbon::today())->copy()->startOfDay();
        $result = $this->forUser($user, $date);

        return DisciplineScore::updateOrCreate(
            ['user_id' => $user->id, 'date' => $date->toDateString()],
            [
                'overall_score' => $result['overall'],
                'breakdown' => $result['breakdown'],
                'computed_at' => now(),
            ],
        );
    }
}
