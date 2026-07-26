<?php

namespace App\Support;

class IconLibrary
{
    /**
     * A curated set of Material Symbols names offered in icon pickers across the app.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            'mosque' => 'Prayer',
            'menu_book' => 'Reading',
            'auto_stories' => 'Book',
            'self_improvement' => 'Mindfulness',
            'fitness_center' => 'Exercise',
            'directions_run' => 'Running',
            'water_drop' => 'Water',
            'bedtime' => 'Sleep',
            'restaurant' => 'Meal',
            'code' => 'Coding',
            'work' => 'Work',
            'school' => 'Learning',
            'payments' => 'Money',
            'savings' => 'Savings',
            'diversity_3' => 'Relationships',
            'favorite' => 'Kindness',
            'bolt' => 'Energy',
            'task_alt' => 'Task',
            'repeat' => 'Habit',
            'emoji_events' => 'Goal',
            'account_tree' => 'Project',
            'schedule' => 'Routine',
            'edit_note' => 'Journal',
            'wb_twilight' => 'Wake up',
            'category' => 'General',
        ];
    }
}
