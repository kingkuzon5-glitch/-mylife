<?php

namespace Tests\Feature\Services;

use App\Models\Category;
use App\Models\Habit;
use App\Models\Task;
use App\Models\User;
use App\Services\DisciplineScoreCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DisciplineScoreCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_score_is_100_when_nothing_is_due(): void
    {
        $user = User::factory()->create();

        $result = app(DisciplineScoreCalculator::class)->forUser($user);

        $this->assertSame(100, $result['overall']);
        $this->assertSame([], $result['breakdown']);
    }

    public function test_mandatory_items_are_weighted_more_than_optional(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create(['name' => 'Deen']);
        $today = Carbon::today();

        $mandatoryTask = Task::factory()->for($user)->for($category)->create([
            'is_mandatory' => true,
            'repeat_type' => 'daily',
            'start_date' => $today->toDateString(),
        ]);

        $optionalTask = Task::factory()->for($user)->for($category)->create([
            'is_mandatory' => false,
            'repeat_type' => 'daily',
            'start_date' => $today->toDateString(),
        ]);

        // Only the optional task is completed: 1 (optional weight) out of 3 (2 mandatory + 1 optional) possible.
        $optionalTask->completions()->create([
            'user_id' => $user->id,
            'date' => $today->toDateString(),
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $result = app(DisciplineScoreCalculator::class)->forUser($user, $today);

        $this->assertSame(33, $result['overall']);
        $this->assertSame(33, $result['breakdown']['Deen']);
    }

    public function test_score_reaches_100_when_everything_due_is_completed(): void
    {
        $user = User::factory()->create();
        $today = Carbon::today();

        $habit = Habit::factory()->for($user)->create([
            'schedule_type' => 'daily',
            'start_date' => $today->toDateString(),
        ]);

        $habit->logs()->create([
            'user_id' => $user->id,
            'date' => $today->toDateString(),
            'completed' => true,
            'logged_at' => now(),
        ]);

        $result = app(DisciplineScoreCalculator::class)->forUser($user, $today);

        $this->assertSame(100, $result['overall']);
    }
}
