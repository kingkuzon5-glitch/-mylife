<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Goal extends Model
{
    /** @use HasFactory<\Database\Factories\GoalFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'description',
        'deadline',
        'priority',
        'status',
        'progress_percentage',
    ];

    protected $attributes = [
        'priority' => 'medium',
        'status' => 'not_started',
        'progress_percentage' => 0,
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'progress_percentage' => 'integer',
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

    public function milestones(): HasMany
    {
        return $this->hasMany(GoalMilestone::class)->orderBy('sort_order');
    }

    public function habits(): BelongsToMany
    {
        return $this->belongsToMany(Habit::class, 'goal_habit');
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'goal_task');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['completed', 'abandoned']);
    }

    public function recalculateProgress(): void
    {
        $total = $this->milestones()->count();

        if ($total === 0) {
            return;
        }

        $completed = $this->milestones()->where('is_completed', true)->count();

        $this->progress_percentage = (int) round(($completed / $total) * 100);

        if ($this->progress_percentage === 100 && $this->status !== 'completed') {
            $this->status = 'completed';
        }

        $this->save();
    }
}
