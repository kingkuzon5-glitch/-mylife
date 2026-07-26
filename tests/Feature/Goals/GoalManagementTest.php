<?php

namespace Tests\Feature\Goals;

use App\Livewire\Goals\Index;
use App\Livewire\Goals\Show;
use App\Models\Goal;
use App\Models\Habit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GoalManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_goal(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('name', 'Become a Full-Stack Developer')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('goals', [
            'user_id' => $user->id,
            'name' => 'Become a Full-Stack Developer',
        ]);
    }

    public function test_completing_all_milestones_marks_goal_complete_and_sets_progress_to_100(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create();

        $milestoneOne = $goal->milestones()->create(['name' => 'Learn PHP', 'sort_order' => 0]);
        $milestoneTwo = $goal->milestones()->create(['name' => 'Learn Laravel', 'sort_order' => 1]);

        $component = Livewire::actingAs($user)->test(Show::class, ['goal' => $goal]);

        $component->call('toggleMilestone', $milestoneOne->id);
        $goal->refresh();
        $this->assertSame(50, $goal->progress_percentage);
        $this->assertSame('not_started', $goal->status);

        $component->call('toggleMilestone', $milestoneTwo->id);
        $goal->refresh();
        $this->assertSame(100, $goal->progress_percentage);
        $this->assertSame('completed', $goal->status);
    }

    public function test_user_can_link_a_habit_to_a_goal(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create();
        $habit = Habit::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(Show::class, ['goal' => $goal])
            ->set('selectedHabitIds', [(string) $habit->id]);

        $this->assertTrue($goal->habits()->whereKey($habit->id)->exists());
    }
}
