<?php

namespace Tests\Feature\Projects;

use App\Livewire\Projects\Index;
use App\Livewire\Projects\Show;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_project(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('name', 'Portfolio Website')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('projects', [
            'user_id' => $user->id,
            'name' => 'Portfolio Website',
        ]);
    }

    public function test_completing_all_project_tasks_marks_project_complete(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();

        $component = Livewire::actingAs($user)->test(Show::class, ['project' => $project]);

        $component->set('newTaskName', 'Design homepage')->call('addTask');
        $component->set('newTaskName', 'Deploy to production')->call('addTask');

        $project->refresh();
        $taskIds = $project->tasks->pluck('id');

        $component->call('toggleTask', $taskIds->first());
        $project->refresh();
        $this->assertSame(50, $project->progress_percentage);

        $component->call('toggleTask', $taskIds->last());
        $project->refresh();
        $this->assertSame(100, $project->progress_percentage);
        $this->assertSame('completed', $project->status);
    }
}
