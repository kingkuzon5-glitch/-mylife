<?php

namespace App\Observers;

use App\Models\TaskCompletion;
use App\Services\StreakCalculator;

class TaskCompletionObserver
{
    public function __construct(private StreakCalculator $streaks) {}

    public function saved(TaskCompletion $taskCompletion): void
    {
        $this->streaks->recalculateTask($taskCompletion->task);
    }

    public function deleted(TaskCompletion $taskCompletion): void
    {
        $this->streaks->recalculateTask($taskCompletion->task);
    }
}
