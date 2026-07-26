<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisciplineScore extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'overall_score',
        'breakdown',
        'computed_at',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'breakdown' => 'array',
            'computed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
