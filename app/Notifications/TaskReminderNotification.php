<?php

namespace App\Notifications;

use App\Models\Reminder;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Task     $task,
        public readonly Reminder $reminder,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'     => 'task_reminder',
            'task_id'  => $this->task->id,
            'title'    => $this->task->title,
            'due_date' => $this->task->due_date->toDateTimeString(),
            'message'  => 'Recordatorio: ' . $this->task->title,
        ];
    }
}
