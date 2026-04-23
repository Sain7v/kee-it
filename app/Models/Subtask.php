<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subtask extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'task_id',
        'title',
        'is_completed',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'order'        => 'integer',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
