<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Carbon;

trait TogglesTaskCompletion
{
    public function toggleTask(int $taskId): void
    {
        $task = auth()->user()->tasks()->findOrFail($taskId);
        $this->authorize('view', $task);

        $today = Carbon::today()->toDateString();
        $completion = $task->completions()->whereDate('date', $today)->first();

        if ($completion) {
            $completion->delete();
        } else {
            $task->completions()->create([
                'user_id' => auth()->id(),
                'date' => $today,
                'completed_at' => now(),
                'status' => 'completed',
            ]);
        }
    }
}
