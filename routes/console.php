<?php

use App\Jobs\SendStreakRiskAlert;
use App\Jobs\SendTaskReminder;
use App\Models\Reminder;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cada hora: despachar recordatorios pendientes
Schedule::call(function () {
    Reminder::with('task.user')
        ->whereNull('sent_at')
        ->where('remind_at', '<=', now())
        ->each(fn ($reminder) => SendTaskReminder::dispatch($reminder));
})->hourly()->name('send-pending-reminders');

// Cada noche 20:00: alertar usuarios con racha activa sin completar nada hoy
Schedule::call(function () {
    User::where('streak_current', '>', 0)
        ->whereNotNull('streak_last_active')
        ->each(function (User $user) {
            $completedToday = $user->tasks()
                ->where('status', 'completada')
                ->whereDate('updated_at', today())
                ->exists();

            if (! $completedToday) {
                SendStreakRiskAlert::dispatch($user);
            }
        });
})->dailyAt('20:00')->name('streak-risk-alerts');

// Cada noche 23:59: recalcular procrastination de tareas vencidas + resetear rachas inactivas
Schedule::call(function () {
    Task::active()
        ->where('due_date', '<', now())
        ->each(function (Task $task) {
            $task->procrastination_score += config('services.kee_it.procrastination_penalty', 5);
            $task->saveQuietly();
        });

    User::where('streak_current', '>', 0)
        ->where('streak_last_active', '<', today()->subDay()->toDateString())
        ->each(fn (User $user) => $user->update(['streak_current' => 0]));
})->dailyAt('23:59')->name('nightly-recalculation');
