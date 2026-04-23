<?php

namespace App\Services;

use App\Models\Task;

class TaskPriorityService
{
    public function calculate(Task $task): int
    {
        if ($task->status === 'completada') {
            return 0;
        }

        $urgency         = $this->urgencyScore($task);
        $weight          = $this->weightScore($task);
        $procrastination = $task->procrastination_score ?? 0;

        return (int) round($urgency + $weight + $procrastination);
    }

    public function getPriorityLabel(int $score): string
    {
        return match (true) {
            $score <= 30 => 'baja',
            $score <= 60 => 'media',
            $score <= 80 => 'alta',
            default      => 'critica',
        };
    }

    private function urgencyScore(Task $task): float
    {
        if (! $task->due_date) {
            return 0;
        }

        $daysLeft = now()->startOfDay()->diffInDays(
            $task->due_date->startOfDay(),
            absolute: false
        );

        return max(0, 100 - ($daysLeft * 10));
    }

    private function weightScore(Task $task): float
    {
        if (! $task->estimated_minutes) {
            return 0;
        }

        return min(30, $task->estimated_minutes / 30);
    }
}
