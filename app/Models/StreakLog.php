<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class StreakLog extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'tasks_completed',
        'is_productive',
    ];

    protected function casts(): array
    {
        return [
            'date'            => 'date',
            'is_productive'   => 'boolean',
            'tasks_completed' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeProductive(Builder $query): Builder
    {
        return $query->where('is_productive', true);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
