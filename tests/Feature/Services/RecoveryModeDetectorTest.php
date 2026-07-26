<?php

namespace Tests\Feature\Services;

use App\Models\Habit;
use App\Models\User;
use App\Services\RecoveryModeDetector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RecoveryModeDetectorTest extends TestCase
{
    use RefreshDatabase;

    public function test_brand_new_user_with_no_history_is_not_treated_as_inactive(): void
    {
        $user = User::factory()->create();

        $status = app(RecoveryModeDetector::class)->status($user);

        $this->assertSame('none', $status);
    }

    public function test_user_silent_for_three_or_more_days_is_inactive(): void
    {
        $user = User::factory()->create();
        $habit = Habit::factory()->for($user)->create(['start_date' => now()->subDays(10)->toDateString()]);

        $habit->logs()->create([
            'user_id' => $user->id,
            'date' => now()->subDays(4)->toDateString(),
            'completed' => true,
            'logged_at' => now(),
        ]);

        $status = app(RecoveryModeDetector::class)->status($user);

        $this->assertSame('inactive', $status);
    }

    public function test_user_active_today_with_a_recent_gap_is_easing_back(): void
    {
        $user = User::factory()->create();
        $habit = Habit::factory()->for($user)->create(['start_date' => now()->subDays(10)->toDateString()]);

        $habit->logs()->create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'completed' => true,
            'logged_at' => now(),
        ]);

        $status = app(RecoveryModeDetector::class)->status($user);

        $this->assertSame('easing_back', $status);
    }

    public function test_three_consecutive_active_days_clears_recovery_status(): void
    {
        $user = User::factory()->create();
        $habit = Habit::factory()->for($user)->create(['start_date' => now()->subDays(10)->toDateString()]);

        foreach ([2, 1, 0] as $daysAgo) {
            $habit->logs()->create([
                'user_id' => $user->id,
                'date' => Carbon::today()->subDays($daysAgo)->toDateString(),
                'completed' => true,
                'logged_at' => now(),
            ]);
        }

        $status = app(RecoveryModeDetector::class)->status($user);

        $this->assertSame('none', $status);
    }
}
