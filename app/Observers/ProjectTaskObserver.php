<?php

namespace App\Observers;

use App\Models\ProjectTask;

class ProjectTaskObserver
{
    public function saved(ProjectTask $projectTask): void
    {
        $projectTask->project->recalculateProgress();
    }

    public function deleted(ProjectTask $projectTask): void
    {
        $projectTask->project->recalculateProgress();
    }
}
