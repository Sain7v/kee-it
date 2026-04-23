<?php

namespace App\Jobs;

use App\Models\Reminder;
use App\Notifications\TaskReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTaskReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 30;

    public function __construct(
        public readonly Reminder $reminder,
    ) {}

    public function handle(): void
    {
        $reminder = $this->reminder->fresh(['task.user']);

        if (! $reminder || $reminder->sent_at !== null) {
            return;
        }

        $task = $reminder->task;
        $user = $task?->user;

        if (! $task || ! $user || $task->status === 'completada') {
            return;
        }

        $user->notify(new TaskReminderNotification($task, $reminder));

        $reminder->update(['sent_at' => now()]);
    }
}
