<?php

namespace App\Models;

use App\Observers\HabitLogObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(HabitLogObserver::class)]
class HabitLog extends Model
{
    protected $fillable = [
        'habit_id',
        'user_id',
        'date',
        'value',
        'completed',
        'logged_at',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'value' => 'decimal:2',
            'completed' => 'boolean',
            'logged_at' => 'datetime',
        ];
    }

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
