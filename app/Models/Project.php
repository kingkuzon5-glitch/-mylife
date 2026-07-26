<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'description',
        'status',
        'deadline',
        'priority',
        'notes',
        'progress_percentage',
    ];

    protected $attributes = [
        'status' => 'not_started',
        'priority' => 'medium',
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

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class)->orderBy('sort_order');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class)->orderBy('sort_order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['completed']);
    }

    public function recalculateProgress(): void
    {
        $total = $this->tasks()->count();

        if ($total === 0) {
            return;
        }

        $completed = $this->tasks()->where('is_completed', true)->count();

        $this->progress_percentage = (int) round(($completed / $total) * 100);

        if ($this->progress_percentage === 100 && $this->status !== 'completed') {
            $this->status = 'completed';
        }

        $this->save();
    }
}
