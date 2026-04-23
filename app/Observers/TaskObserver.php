<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\TaskLog;
use App\Services\ReminderSchedulerService;
use App\Services\TaskPriorityService;

class TaskObserver
{
    public function __construct(
        private readonly TaskPriorityService       $priorityService,
        private readonly ReminderSchedulerService  $reminderService,
    ) {}

    public function creating(Task $task): void
    {
        $this->applyPriority($task);
    }

    public function created(Task $task): void
    {
        $this->reminderService->scheduleForTask($task);
        TaskLog::record($task, 'Tarea creada');
    }

    public function updating(Task $task): void
    {
        if ($this->shouldRecalculate($task)) {
            $this->applyPriority($task);
        }
    }

    public function updated(Task $task): void
    {
        if ($task->wasChanged('due_date')) {
            $this->reminderService->rescheduleForTask($task);
            TaskLog::record($task, 'Fecha límite actualizada', $task->due_date?->toDateTimeString());
        }

        if ($task->wasChanged('status')) {
            TaskLog::record($task, 'Estado cambiado a: ' . $task->status);
        }

        if ($task->wasChanged('priority')) {
            TaskLog::record($task, 'Prioridad recalculada → ' . $task->priority);
        }
    }

    private function applyPriority(Task $task): void
    {
        $score = $this->priorityService->calculate($task);
        $label = $this->priorityService->getPriorityLabel($score);

        $task->priority_total = $score;
        $task->priority       = $label;
    }

    private function shouldRecalculate(Task $task): bool
    {
        return $task->isDirty([
            'due_date',
            'estimated_minutes',
            'procrastination_score',
            'priority',
            'status',
        ]);
    }
}
