<?php

namespace App\Models;

use App\Observers\ProjectTaskObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(ProjectTaskObserver::class)]
class ProjectTask extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'description',
        'is_completed',
        'completed_at',
        'due_date',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
            'due_date' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
