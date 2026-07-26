<?php

namespace Tests\Feature\Tasks;

use App\Livewire\Tasks\Index;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_task(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('name', 'Study Laravel')
            ->set('priority', 'high')
            ->set('estimated_duration_minutes', 120)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'name' => 'Study Laravel',
            'priority' => 'high',
        ]);
    }

    public function test_toggling_a_task_creates_and_removes_todays_completion(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        $component = Livewire::actingAs($user)->test(Index::class);

        $component->call('toggleTask', $task->id);
        $this->assertDatabaseHas('task_completions', [
            'task_id' => $task->id,
            'status' => 'completed',
        ]);

        $component->call('toggleTask', $task->id);
        $this->assertDatabaseMissing('task_completions', [
            'task_id' => $task->id,
        ]);
    }

    public function test_task_name_is_required(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }
}
