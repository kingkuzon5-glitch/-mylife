<?php

namespace Tests\Feature\Services;

use App\Models\Habit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StreakCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_today_does_not_break_yesterdays_streak(): void
    {
        $user = User::factory()->create();
        $habit = Habit::factory()->for($user)->create([
            'schedule_type' => 'daily',
            'start_date' => now()->subDays(5)->toDateString(),
        ]);

        foreach ([2, 1] as $daysAgo) {
            $habit->logs()->create([
                'user_id' => $user->id,
                'date' => Carbon::today()->subDays($daysAgo)->toDateString(),
                'completed' => true,
                'logged_at' => now(),
            ]);
        }

        $habit->refresh();

        // Today isn't logged yet, but that shouldn't zero out the streak built through yesterday.
        $this->assertSame(2, $habit->current_streak);
    }

    public function test_a_missed_scheduled_day_resets_the_current_streak(): void
    {
        $user = User::factory()->create();
        $habit = Habit::factory()->for($user)->create([
            'schedule_type' => 'daily',
            'start_date' => now()->subDays(5)->toDateString(),
        ]);

        // Completed 4 days ago and 3 days ago, then a gap, nothing since.
        foreach ([4, 3] as $daysAgo) {
            $habit->logs()->create([
                'user_id' => $user->id,
                'date' => Carbon::today()->subDays($daysAgo)->toDateString(),
                'completed' => true,
                'logged_at' => now(),
            ]);
        }

        $habit->refresh();

        $this->assertSame(0, $habit->current_streak);
        $this->assertSame(2, $habit->best_streak);
    }

    public function test_specific_days_schedule_only_counts_scheduled_days(): void
    {
        $user = User::factory()->create();

        // Schedule only for today's weekday, starting a full week ago so there's real history to walk.
        $todayWeekday = Carbon::today()->dayOfWeek;

        $habit = Habit::factory()->for($user)->create([
            'schedule_type' => 'specific_days',
            'schedule_days' => [$todayWeekday],
            'start_date' => now()->subWeeks(3)->toDateString(),
        ]);

        foreach ([2, 1] as $weeksAgo) {
            $habit->logs()->create([
                'user_id' => $user->id,
                'date' => Carbon::today()->subWeeks($weeksAgo)->toDateString(),
                'completed' => true,
                'logged_at' => now(),
            ]);
        }

        $habit->refresh();

        $this->assertSame(2, $habit->current_streak);
    }
}
