<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'task_id',
        'action',
        'note',
        'logged_at',
    ];

    protected function casts(): array
    {
        return [
            'logged_at' => 'datetime',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public static function record(Task $task, string $action, ?string $note = null): self
    {
        return static::create([
            'task_id'   => $task->id,
            'action'    => $action,
            'note'      => $note,
            'logged_at' => now(),
        ]);
    }
}
