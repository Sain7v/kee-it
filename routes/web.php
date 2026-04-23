<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Tasks
    Route::get('/tasks',                    [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create',             [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks',                   [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}',             [TaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit',        [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}',             [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}',          [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::patch('/tasks/{task}/complete',  [TaskController::class, 'complete'])->name('tasks.complete');
    Route::patch('/tasks/{task}/postpone',  [TaskController::class, 'postpone'])->name('tasks.postpone');
    Route::patch('/tasks/{task}/toggle',    [TaskController::class, 'toggleComplete'])->name('tasks.toggle');

    // Subtasks
    Route::post('/tasks/{task}/subtasks',                   [SubtaskController::class, 'store'])->name('subtasks.store');
    Route::patch('/tasks/{task}/subtasks/{subtask}/toggle', [SubtaskController::class, 'toggle'])->name('subtasks.toggle');
    Route::delete('/tasks/{task}/subtasks/{subtask}',       [SubtaskController::class, 'destroy'])->name('subtasks.destroy');

    // Calendar feed
    Route::get('/calendar',                 fn () => view('calendar'))->name('calendar');
    Route::get('/api/tasks/calendar',       [TaskController::class, 'calendarFeed'])->name('tasks.calendar-feed');

    // AI
    Route::post('/tasks/ai-prioritize',     [TaskController::class, 'aiPrioritize'])->name('tasks.ai-prioritize');
    Route::post('/tasks/{task}/ai-suggestion', [TaskController::class, 'aiSuggestion'])->name('tasks.ai-suggestion');

    // Timer
    Route::post('/tasks/{task}/start-timer', [TaskController::class, 'startTimer'])->name('tasks.start-timer');
    Route::post('/tasks/{task}/stop-timer',  [TaskController::class, 'stopTimer'])->name('tasks.stop-timer');

    // Reminders
    Route::get('/reminders',               [ReminderController::class, 'index'])->name('reminders.index');
    Route::post('/reminders',              [ReminderController::class, 'store'])->name('reminders.store');
    Route::delete('/reminders/{reminder}', [ReminderController::class, 'destroy'])->name('reminders.destroy');
    Route::post('/reminders/mark-read',    [ReminderController::class, 'markAllRead'])->name('reminders.mark-read');

    // Stats
    Route::get('/stats', [StatsController::class, 'index'])->name('stats');

    // Profile
    Route::get('/profile',                   [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',                 [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password',        [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::patch('/profile/preferences',     [ProfileController::class, 'updatePreferences'])->name('profile.preferences');
    Route::delete('/profile',               [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
