<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Reminder extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'task_id',
        'remind_at',
        'type',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'remind_at' => 'datetime',
            'sent_at'   => 'datetime',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('sent_at')
                     ->where('remind_at', '<=', now());
    }

    public function scopeUnsent(Builder $query): Builder
    {
        return $query->whereNull('sent_at');
    }

    public function isPending(): bool
    {
        return is_null($this->sent_at);
    }
}
