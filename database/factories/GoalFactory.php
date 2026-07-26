<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Goal>
 */
class GoalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => null,
            'name' => ucfirst($this->faker->unique()->sentence(4)),
            'description' => $this->faker->optional()->paragraph(),
            'deadline' => $this->faker->optional()->dateTimeBetween('now', '+1 year'),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'status' => 'not_started',
            'progress_percentage' => 0,
        ];
    }
}
