<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => null,
            'name' => ucfirst($this->faker->unique()->catchPhrase()),
            'description' => $this->faker->optional()->paragraph(),
            'status' => 'not_started',
            'deadline' => $this->faker->optional()->dateTimeBetween('now', '+1 year'),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'progress_percentage' => 0,
        ];
    }
}
