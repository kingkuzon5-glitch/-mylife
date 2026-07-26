<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\DefaultCategoriesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.register');
    }

    public function test_new_users_can_register(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('register');

        $component->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_new_users_are_onboarded_with_starter_habits_tasks_and_routine(): void
    {
        $this->seed(DefaultCategoriesSeeder::class);

        Volt::test('pages.auth.register')
            ->set('name', 'Said')
            ->set('email', 'said@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register');

        $user = User::where('email', 'said@example.com')->firstOrFail();

        $this->assertGreaterThan(0, $user->habits()->count());
        $this->assertGreaterThan(0, $user->tasks()->count());
        $this->assertGreaterThan(0, $user->routineItems()->count());
    }
}
