<?php

namespace App\Livewire\Today;

use App\Livewire\Concerns\RefreshesOnQuickAdd;
use App\Livewire\Concerns\TogglesHabitCompletion;
use App\Livewire\Concerns\TogglesTaskCompletion;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\RoutineItem;
use App\Models\Task;
use App\Services\DisciplineScoreCalculator;
use App\Services\RecoveryModeDetector;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    use RefreshesOnQuickAdd, TogglesHabitCompletion, TogglesTaskCompletion;

    public function logFocusSession(int $minutes): void
    {
        auth()->user()->focusSessions()->create([
            'label' => $minutes >= 45 ? 'Deep Work' : 'Focus',
            'duration_minutes' => $minutes,
            'started_at' => now()->subMinutes($minutes),
            'completed_at' => now(),
            'was_completed' => true,
        ]);

        $this->dispatch('focus-session-logged');
    }

    public function render()
    {
        $user = auth()->user();
        $today = Carbon::today();

        $tasks = $user->tasks()->active()->with(['category', 'completions' => function ($query) use ($today) {
            $query->whereDate('date', $today);
        }])->get()->filter(fn (Task $task) => $task->isDueOn($today));

        $habits = $user->habits()->active()->with(['category', 'logs' => function ($query) use ($today) {
            $query->whereDate('date', $today);
        }])->get()->filter(fn (Habit $habit) => $habit->isScheduledOn($today));

        $checklist = $this->buildChecklist($tasks, $habits);

        $completedCount = collect($checklist)->where('completed', true)->count();
        $totalCount = count($checklist);
        $dailyProgress = $totalCount > 0 ? (int) round(($completedCount / $totalCount) * 100) : 0;

        $recovery = app(RecoveryModeDetector::class);
        $recoveryStatus = $recovery->status($user);
        $streak = $recovery->currentActiveStreak($user);

        if ($recoveryStatus === 'inactive') {
            $checklist = collect($checklist)
                ->sortByDesc('mandatory')
                ->take(RecoveryModeDetector::RESET_CHECKLIST_LIMIT)
                ->values()
                ->all();
        } else {
            usort($checklist, fn ($a, $b) => $b['mandatory'] <=> $a['mandatory']);
        }

        $score = app(DisciplineScoreCalculator::class)->computeAndStore($user, $today);

        $routineItems = $user->routineItems()->active()->orderBy('start_time')->get();
        $currentRoutineItem = $routineItems->first(function (RoutineItem $item) {
            $now = now()->format('H:i:s');

            return $item->end_time
                ? $now >= $item->start_time && $now <= $item->end_time
                : $now >= $item->start_time;
        });

        $vitals = $this->buildVitals($checklist);

        $circumference = round(2 * M_PI * 74, 2);
        $scoreOffset = round($circumference * (1 - ($score->overall_score / 100)), 2);

        return view('livewire.today.dashboard', [
            'greeting' => $this->buildGreeting(),
            'circumference' => $circumference,
            'scoreOffset' => $scoreOffset,
            'checklist' => $checklist,
            'completedCount' => $completedCount,
            'totalCount' => $totalCount,
            'dailyProgress' => $dailyProgress,
            'disciplineScore' => $score,
            'streak' => $streak,
            'recoveryStatus' => $recoveryStatus,
            'routineItems' => $routineItems,
            'currentRoutineItem' => $currentRoutineItem,
            'vitals' => $vitals,
        ]);
    }

    private function buildChecklist($tasks, $habits): array
    {
        $items = [];

        foreach ($tasks as $task) {
            $completed = $task->completions->isNotEmpty();

            $items[] = [
                'type' => 'task',
                'id' => $task->id,
                'name' => $task->name,
                'icon' => $task->icon,
                'category' => $task->category->name ?? 'General',
                'mandatory' => $task->is_mandatory,
                'completed' => $completed,
                'subtitle' => ($task->category->name ?? 'General').($task->estimated_duration_minutes ? ' — '.$task->estimated_duration_minutes.' min' : ''),
            ];
        }

        foreach ($habits as $habit) {
            $log = $habit->logs->first();
            $completed = (bool) ($log?->completed);

            $items[] = [
                'type' => 'habit',
                'id' => $habit->id,
                'name' => $habit->name,
                'icon' => $habit->icon,
                'category' => $habit->category->name ?? 'General',
                'mandatory' => $habit->is_mandatory,
                'completed' => $completed,
                'subtitle' => $this->habitSubtitle($habit, $log),
            ];
        }

        return $items;
    }

    private function habitSubtitle(Habit $habit, ?HabitLog $log): string
    {
        $category = $habit->category->name ?? 'General';

        return match ($habit->tracking_type) {
            'count', 'quantity', 'duration' => $habit->target_value
                ? $category.' — '.($log->value ?? 0).'/'.rtrim(rtrim($habit->target_value, '0'), '.').' '.$habit->target_unit
                : $category,
            'time' => $habit->target_time
                ? $category.' — before '.Carbon::parse($habit->target_time)->format('g:i A')
                : $category,
            default => $category,
        };
    }

    private function buildGreeting(): string
    {
        return match (true) {
            now()->hour < 12 => 'Good morning',
            now()->hour < 17 => 'Good afternoon',
            default => 'Good evening',
        };
    }

    private function buildVitals(array $checklist): array
    {
        $byCategory = collect($checklist)->groupBy('category');

        return $byCategory->map(function ($items, $category) {
            $total = $items->count();
            $completed = $items->where('completed', true)->count();

            return [
                'category' => $category,
                'icon' => $items->first()['icon'],
                'value' => "{$completed}/{$total}",
                'percentage' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
            ];
        })->sortByDesc('percentage')->take(4)->values()->all();
    }
}
