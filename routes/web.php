<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', \App\Livewire\Today\Dashboard::class)->name('dashboard');

    Route::get('/habits', \App\Livewire\Habits\Index::class)->name('habits.index');
    Route::get('/habits/{habit}', \App\Livewire\Habits\Show::class)->name('habits.show');

    Route::get('/tasks', \App\Livewire\Tasks\Index::class)->name('tasks.index');

    Route::get('/goals', \App\Livewire\Goals\Index::class)->name('goals.index');
    Route::get('/goals/{goal}', \App\Livewire\Goals\Show::class)->name('goals.show');

    Route::get('/projects', \App\Livewire\Projects\Index::class)->name('projects.index');
    Route::get('/projects/{project}', \App\Livewire\Projects\Show::class)->name('projects.show');

    Route::get('/routine', \App\Livewire\Routine\Index::class)->name('routine.index');

    Route::redirect('/settings', '/profile');
    Route::view('/profile', 'profile')->name('profile');
    Route::get('/settings/categories', \App\Livewire\Settings\Categories::class)->name('settings.categories');
    Route::get('/settings/appearance', \App\Livewire\Settings\Appearance::class)->name('settings.appearance');

    Route::get('/time', function () {
        return view('coming-soon', [
            'title' => 'Time Tracking',
            'icon' => 'hourglass_empty',
            'description' => 'Track hours across work, learning, and everything in between — coming in a future round.',
        ]);
    })->name('time.index');

    Route::get('/finance', function () {
        return view('coming-soon', [
            'title' => 'Finance',
            'icon' => 'payments',
            'description' => 'Income, expenses, savings, and financial goals — coming in a future round.',
        ]);
    })->name('finance.index');

    Route::get('/journal', function () {
        return view('coming-soon', [
            'title' => 'Journal',
            'icon' => 'menu_book',
            'description' => 'Daily reflection and mood tracking — coming in a future round.',
        ]);
    })->name('journal.index');

    Route::get('/analytics', function () {
        return view('coming-soon', [
            'title' => 'Analytics',
            'icon' => 'monitoring',
            'description' => 'Deep reports across every life area — coming in a future round.',
        ]);
    })->name('analytics.index');

    Route::get('/reviews', function () {
        return view('coming-soon', [
            'title' => 'Reviews',
            'icon' => 'fact_check',
            'description' => 'Weekly and monthly reviews — coming in a future round.',
        ]);
    })->name('reviews.index');

    Route::get('/support', function () {
        return view('coming-soon', [
            'title' => 'Support',
            'icon' => 'help',
            'description' => 'A help center is on the way. For now, this is your own system — you already know where everything lives.',
        ]);
    })->name('support');

    Route::get('/archive', function () {
        return view('coming-soon', [
            'title' => 'Archive',
            'icon' => 'archive',
            'description' => 'Completed goals and projects will collect here — coming in a future round.',
        ]);
    })->name('archive');
});

require __DIR__.'/auth.php';
