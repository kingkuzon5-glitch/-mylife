<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'user_id' => User::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'icon' => 'category',
            'color' => $this->faker->randomElement(['command-blue', 'discipline-green', 'focus-orange']),
            'sort_order' => 0,
            'is_system' => false,
        ];
    }
}
