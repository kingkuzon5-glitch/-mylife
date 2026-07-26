<?php

namespace App\Livewire\Habits;

use App\Livewire\Concerns\TogglesHabitCompletion;
use App\Models\Habit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

class Show extends Component
{
    use TogglesHabitCompletion;

    public Habit $habit;

    public function mount(Habit $habit): void
    {
        $this->authorize('view', $habit);
        $this->habit = $habit;
    }

    public function render()
    {
        $this->habit->refresh();

        $completedDates = $this->habit->logs()
            ->where('completed', true)
            ->pluck('date')
            ->map(fn ($date) => $date->toDateString())
            ->flip();

        $today = Carbon::today();

        return view('livewire.habits.show', [
            'heatmap' => $this->buildHeatmap($completedDates, $today),
            'totalCompletions' => $completedDates->count(),
            'week' => $this->completionRate($today->copy()->startOfWeek(), $today, $completedDates),
            'month' => $this->completionRate($today->copy()->startOfMonth(), $today, $completedDates),
            'year' => $this->completionRate($today->copy()->startOfYear(), $today, $completedDates),
            'todayLog' => $this->habit->logs()->whereDate('date', $today)->first(),
        ]);
    }

    private function buildHeatmap(Collection $completedDates, Carbon $today): array
    {
        $start = $today->copy()->subWeeks(16)->startOfWeek(Carbon::SUNDAY);
        $weeks = [];

        for ($date = $start->copy(); $date->lte($today); $date->addWeek()) {
            $week = [];

            for ($i = 0; $i < 7; $i++) {
                $day = $date->copy()->addDays($i);
                $inRange = $day->gte($this->habit->start_date) && $day->lte($today);
                $scheduled = $inRange && $this->habit->isScheduledOn($day);

                $week[] = [
                    'date' => $day->toDateString(),
                    'label' => $day->format('M j'),
                    'scheduled' => $scheduled,
                    'completed' => $completedDates->has($day->toDateString()),
                    'future' => $day->gt($today),
                ];
            }

            $weeks[] = $week;
        }

        return $weeks;
    }

    /**
     * @return array{completed: int, scheduled: int, percent: int}
     */
    private function completionRate(Carbon $from, Carbon $to, Collection $completedDates): array
    {
        $scheduled = 0;
        $completed = 0;

        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            if ($date->lt($this->habit->start_date)) {
                continue;
            }

            if ($this->habit->isScheduledOn($date)) {
                $scheduled++;

                if ($completedDates->has($date->toDateString())) {
                    $completed++;
                }
            }
        }

        return [
            'completed' => $completed,
            'scheduled' => $scheduled,
            'percent' => $scheduled > 0 ? (int) round(($completed / $scheduled) * 100) : 0,
        ];
    }
}
