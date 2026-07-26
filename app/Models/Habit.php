<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Habit extends Model
{
    /** @use HasFactory<\Database\Factories\HabitFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'description',
        'icon',
        'tracking_type',
        'target_value',
        'target_unit',
        'target_time',
        'schedule_type',
        'schedule_days',
        'schedule_times_per_week',
        'priority',
        'reminder_time',
        'is_mandatory',
        'start_date',
        'end_date',
        'is_active',
        'sort_order',
    ];

    protected $attributes = [
        'tracking_type' => 'boolean',
        'schedule_type' => 'daily',
        'priority' => 'medium',
        'is_mandatory' => true,
        'is_active' => true,
        'sort_order' => 0,
        'current_streak' => 0,
        'best_streak' => 0,
    ];

    protected function casts(): array
    {
        return [
            'target_value' => 'decimal:2',
            'schedule_days' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_mandatory' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    public function goals(): BelongsToMany
    {
        return $this->belongsToMany(Goal::class, 'goal_habit');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function isScheduledOn(\DateTimeInterface $date): bool
    {
        $date = Carbon::parse($date)->startOfDay();

        if ($date->lt($this->start_date->copy()->startOfDay())) {
            return false;
        }

        if ($this->end_date && $date->gt($this->end_date->copy()->endOfDay())) {
            return false;
        }

        return match ($this->schedule_type) {
            'daily' => true,
            'specific_days' => in_array($date->dayOfWeek, $this->schedule_days ?? [], true),
            'x_per_week' => true,
            'monthly' => $date->day === $this->start_date->day,
            default => true,
        };
    }
}
