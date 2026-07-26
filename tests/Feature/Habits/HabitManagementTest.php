<?php

namespace Tests\Feature\Habits;

use App\Livewire\Habits\Index;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HabitManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_habit(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('name', 'Read Quran')
            ->set('tracking_type', 'count')
            ->set('target_value', 10)
            ->set('target_unit', 'pages')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('habits', [
            'user_id' => $user->id,
            'name' => 'Read Quran',
            'tracking_type' => 'count',
        ]);
    }

    public function test_user_cannot_view_another_users_habit_detail_page(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $habit = Habit::factory()->for($owner)->create();

        $this->actingAs($other)
            ->get(route('habits.show', $habit))
            ->assertForbidden();
    }

    public function test_toggling_a_habit_creates_and_removes_todays_log(): void
    {
        $user = User::factory()->create();
        $habit = Habit::factory()->for($user)->create(['tracking_type' => 'boolean']);

        $component = Livewire::actingAs($user)->test(Index::class);

        $component->call('toggleHabit', $habit->id);
        $this->assertTrue(
            HabitLog::where('habit_id', $habit->id)->whereDate('date', now())->where('completed', true)->exists()
        );

        $component->call('toggleHabit', $habit->id);
        $this->assertDatabaseMissing('habit_logs', [
            'habit_id' => $habit->id,
        ]);
    }

    public function test_completing_a_habit_updates_its_streak(): void
    {
        $user = User::factory()->create();
        $habit = Habit::factory()->for($user)->create([
            'schedule_type' => 'daily',
            'start_date' => now()->subDays(3)->toDateString(),
        ]);

        foreach ([2, 1, 0] as $daysAgo) {
            $habit->logs()->create([
                'user_id' => $user->id,
                'date' => now()->subDays($daysAgo)->toDateString(),
                'completed' => true,
                'logged_at' => now(),
            ]);
        }

        $habit->refresh();

        $this->assertSame(3, $habit->current_streak);
        $this->assertSame(3, $habit->best_streak);
    }
}
