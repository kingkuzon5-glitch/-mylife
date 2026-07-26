<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Habit>
 */
class HabitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => null,
            'name' => ucfirst($this->faker->unique()->words(2, true)),
            'description' => $this->faker->optional()->sentence(),
            'icon' => 'repeat',
            'tracking_type' => 'boolean',
            'schedule_type' => 'daily',
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'is_mandatory' => $this->faker->boolean(70),
            'is_active' => true,
            'start_date' => now()->toDateString(),
        ];
    }
}
