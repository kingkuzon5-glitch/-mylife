<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'description',
        'icon',
        'priority',
        'scheduled_time',
        'estimated_duration_minutes',
        'reminder_minutes_before',
        'repeat_type',
        'repeat_days_of_week',
        'start_date',
        'end_date',
        'is_mandatory',
        'is_active',
        'sort_order',
    ];

    protected $attributes = [
        'priority' => 'medium',
        'repeat_type' => 'daily',
        'is_mandatory' => true,
        'is_active' => true,
        'sort_order' => 0,
        'current_streak' => 0,
        'best_streak' => 0,
    ];

    protected function casts(): array
    {
        return [
            'repeat_days_of_week' => 'array',
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

    public function completions(): HasMany
    {
        return $this->hasMany(TaskCompletion::class);
    }

    public function goals(): BelongsToMany
    {
        return $this->belongsToMany(Goal::class, 'goal_task');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function isDueOn(\DateTimeInterface $date): bool
    {
        $date = Carbon::parse($date)->startOfDay();

        if ($date->lt($this->start_date->copy()->startOfDay())) {
            return false;
        }

        if ($this->end_date && $date->gt($this->end_date->copy()->endOfDay())) {
            return false;
        }

        return match ($this->repeat_type) {
            'none' => $date->isSameDay($this->start_date),
            'daily' => true,
            'weekly' => $date->dayOfWeek === $this->start_date->dayOfWeek,
            'specific_days' => in_array($date->dayOfWeek, $this->repeat_days_of_week ?? [], true),
            'monthly' => $date->day === $this->start_date->day,
            default => true,
        };
    }
}
