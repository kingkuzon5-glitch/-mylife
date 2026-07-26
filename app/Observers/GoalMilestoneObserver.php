<?php

namespace App\Observers;

use App\Models\GoalMilestone;

class GoalMilestoneObserver
{
    public function saved(GoalMilestone $goalMilestone): void
    {
        $goalMilestone->goal->recalculateProgress();
    }

    public function deleted(GoalMilestone $goalMilestone): void
    {
        $goalMilestone->goal->recalculateProgress();
    }
}
