<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app', [
            'axiomNav' => [
                ['label' => 'Today', 'icon' => 'calendar_today', 'route' => route('dashboard'), 'active' => 'dashboard'],
                ['label' => 'Habits', 'icon' => 'repeat', 'route' => route('habits.index'), 'active' => 'habits.*'],
                ['label' => 'Tasks', 'icon' => 'assignment', 'route' => route('tasks.index'), 'active' => 'tasks.*'],
                ['label' => 'Goals', 'icon' => 'emoji_events', 'route' => route('goals.index'), 'active' => 'goals.*'],
                ['label' => 'Projects', 'icon' => 'account_tree', 'route' => route('projects.index'), 'active' => 'projects.*'],
                ['label' => 'Routine', 'icon' => 'schedule', 'route' => route('routine.index'), 'active' => 'routine.*'],
                ['label' => 'Time', 'icon' => 'hourglass_empty', 'route' => route('time.index'), 'active' => 'time.*'],
                ['label' => 'Finance', 'icon' => 'payments', 'route' => route('finance.index'), 'active' => 'finance.*'],
                ['label' => 'Journal', 'icon' => 'menu_book', 'route' => route('journal.index'), 'active' => 'journal.*'],
                ['label' => 'Analytics', 'icon' => 'monitoring', 'route' => route('analytics.index'), 'active' => 'analytics.*'],
                ['label' => 'Reviews', 'icon' => 'fact_check', 'route' => route('reviews.index'), 'active' => 'reviews.*'],
                ['label' => 'Settings', 'icon' => 'settings', 'route' => route('settings.categories'), 'active' => 'settings.*'],
            ],
        ]);
    }
}
