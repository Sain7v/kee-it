<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'due_date',
        'estimated_minutes',
        'actual_minutes',
        'priority_total',
        'is_recurring',
        'recurrence_rule',
        'procrastination_score',
        'ai_suggestion',
        'ai_prioritized_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date'          => 'datetime',
            'ai_prioritized_at' => 'datetime',
            'is_recurring'      => 'boolean',
            'estimated_minutes' => 'integer',
            'actual_minutes'    => 'integer',
            'priority_total'    => 'integer',
            'procrastination_score' => 'integer',
        ];
    }

    // ── Relationships ──────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class)->orderBy('order');
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class)->orderBy('remind_at');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TaskLog::class)->orderByDesc('logged_at');
    }

    // ── Scopes ─────────────────────────────────────────────────

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pendiente');
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', 'en_progreso');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completada');
    }

    public function scopeByPriority(Builder $query): Builder
    {
        return $query->orderByRaw("CASE priority WHEN 'critica' THEN 1 WHEN 'alta' THEN 2 WHEN 'media' THEN 3 WHEN 'baja' THEN 4 ELSE 5 END");
    }

    public function scopeDueToday(Builder $query): Builder
    {
        return $query->whereDate('due_date', today());
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', now())
                     ->whereNotIn('status', ['completada', 'cancelada']);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['completada', 'cancelada']);
    }

    // ── Helpers ────────────────────────────────────────────────

    public function isAiCacheStale(): bool
    {
        if (! $this->ai_prioritized_at) {
            return true;
        }

        return $this->ai_prioritized_at->diffInHours(now()) >= config('services.anthropic.cache_hours', 6);
    }

    public function priorityColor(): string
    {
        return match ($this->priority) {
            'critica' => 'red',
            'alta'    => 'orange',
            'media'   => 'amber',
            default   => 'green',
        };
    }
}
