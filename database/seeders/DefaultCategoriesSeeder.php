<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class DefaultCategoriesSeeder extends Seeder
{
    /**
     * System-wide default life-area categories, shared by every user (user_id is null).
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Deen', 'slug' => 'deen', 'icon' => 'mosque', 'color' => 'discipline-green'],
            ['name' => 'Health & Fitness', 'slug' => 'health-fitness', 'icon' => 'fitness_center', 'color' => 'focus-orange'],
            ['name' => 'Personal Development', 'slug' => 'personal-development', 'icon' => 'self_improvement', 'color' => 'command-blue'],
            ['name' => 'Career', 'slug' => 'career', 'icon' => 'work', 'color' => 'command-blue'],
            ['name' => 'Business & Money', 'slug' => 'business-money', 'icon' => 'payments', 'color' => 'discipline-green'],
            ['name' => 'Relationships', 'slug' => 'relationships', 'icon' => 'diversity_3', 'color' => 'focus-orange'],
            ['name' => 'Productivity', 'slug' => 'productivity', 'icon' => 'bolt', 'color' => 'command-blue'],
            ['name' => 'Personal Habits', 'slug' => 'personal-habits', 'icon' => 'repeat', 'color' => 'discipline-green'],
        ];

        foreach ($categories as $index => $category) {
            Category::updateOrCreate(
                ['user_id' => null, 'slug' => $category['slug']],
                [...$category, 'sort_order' => $index, 'is_system' => true],
            );
        }
    }
}
