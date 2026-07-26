<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\DisciplineScoreCalculator;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class FinalizeDisciplineScores extends Command
{
    protected $signature = 'app:finalize-discipline-scores';

    protected $description = "Snapshot yesterday's discipline score for every user";

    public function handle(DisciplineScoreCalculator $calculator): int
    {
        $yesterday = Carbon::yesterday();

        User::query()->each(function (User $user) use ($calculator, $yesterday) {
            $calculator->computeAndStore($user, $yesterday);
        });

        $this->info('Discipline scores finalized for '.$yesterday->toDateString());

        return self::SUCCESS;
    }
}
