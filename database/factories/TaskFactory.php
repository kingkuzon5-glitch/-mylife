<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => null,
            'name' => ucfirst($this->faker->unique()->words(3, true)),
            'description' => $this->faker->optional()->sentence(),
            'icon' => 'task_alt',
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'repeat_type' => 'daily',
            'is_mandatory' => $this->faker->boolean(70),
            'is_active' => true,
            'start_date' => now()->toDateString(),
        ];
    }
}
