<?php

namespace App\Services;

use App\Models\Reminder;
use App\Models\Task;

class ReminderSchedulerService
{
    public function scheduleForTask(Task $task): void
    {
        if (! $task->due_date) {
            return;
        }

        $remindAt = $task->due_date->copy()->subHours(
            config('services.kee_it.reminder_hours_before', 24)
        );

        if ($remindAt->isPast()) {
            return;
        }

        $task->reminders()->updateOrCreate(
            ['sent_at' => null],
            [
                'remind_at' => $remindAt,
                'type'      => 'email',
            ]
        );
    }

    public function rescheduleForTask(Task $task): void
    {
        $task->reminders()->whereNull('sent_at')->delete();
        $this->scheduleForTask($task);
    }
}
