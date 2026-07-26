<?php

namespace App\Listeners;

use App\Models\Category;
use App\Models\Habit;
use App\Models\RoutineItem;
use App\Models\Task;
use Illuminate\Auth\Events\Registered;

class SeedStarterContent
{
    public function handle(Registered $event): void
    {
        $user = $event->user;

        $categoryId = fn (string $slug) => Category::where('slug', $slug)->value('id');

        $today = now()->toDateString();

        Habit::insert([
            $this->habit($user->id, $categoryId('deen'), 'Pray all obligatory prayers', 'mosque', 'count', 5, 'prayers', true, $today),
            $this->habit($user->id, $categoryId('deen'), 'Read Quran', 'menu_book', 'count', 10, 'pages', false, $today),
            $this->habit($user->id, $categoryId('health-fitness'), 'Exercise', 'fitness_center', 'duration', 30, 'minutes', true, $today),
            $this->habit($user->id, $categoryId('health-fitness'), 'Drink water', 'water_drop', 'quantity', 2.5, 'liters', false, $today),
        ]);

        Task::insert([
            $this->task($user->id, $categoryId('career'), 'Study Laravel', 'code', 'high', 120, $today),
            $this->task($user->id, $categoryId('personal-development'), 'Read a book', 'auto_stories', 'low', 30, $today),
        ]);

        RoutineItem::insert([
            $this->routine($user->id, 'Wake Up', 'wb_twilight', '05:00', '05:15', 0),
            $this->routine($user->id, 'Fajr', 'mosque', '05:15', '05:45', 1),
            $this->routine($user->id, 'Deep Work', 'code', '08:00', '13:00', 2),
            $this->routine($user->id, 'Exercise', 'fitness_center', '18:00', '19:00', 3),
            $this->routine($user->id, 'Wind Down', 'bedtime', '22:30', '23:00', 4),
        ]);
    }

    private function habit(int $userId, ?int $categoryId, string $name, string $icon, string $trackingType, float $target, string $unit, bool $mandatory, string $today): array
    {
        return [
            'user_id' => $userId,
            'category_id' => $categoryId,
            'name' => $name,
            'icon' => $icon,
            'tracking_type' => $trackingType,
            'target_value' => $target,
            'target_unit' => $unit,
            'schedule_type' => 'daily',
            'priority' => $mandatory ? 'high' : 'medium',
            'is_mandatory' => $mandatory,
            'is_active' => true,
            'start_date' => $today,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function task(int $userId, ?int $categoryId, string $name, string $icon, string $priority, int $durationMinutes, string $today): array
    {
        return [
            'user_id' => $userId,
            'category_id' => $categoryId,
            'name' => $name,
            'icon' => $icon,
            'priority' => $priority,
            'estimated_duration_minutes' => $durationMinutes,
            'repeat_type' => 'daily',
            'is_mandatory' => $priority === 'high',
            'is_active' => true,
            'start_date' => $today,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function routine(int $userId, string $title, string $icon, string $start, string $end, int $sortOrder): array
    {
        return [
            'user_id' => $userId,
            'title' => $title,
            'icon' => $icon,
            'start_time' => $start,
            'end_time' => $end,
            'sort_order' => $sortOrder,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
