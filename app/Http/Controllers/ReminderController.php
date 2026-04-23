<?php

namespace App\Http\Controllers;

use App\Jobs\SendTaskReminder;
use App\Models\Reminder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReminderController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $pending = Reminder::whereHas('task', fn ($q) => $q->where('user_id', $user->id))
            ->whereNull('sent_at')
            ->with('task')
            ->orderBy('remind_at')
            ->get();

        $sent = Reminder::whereHas('task', fn ($q) => $q->where('user_id', $user->id))
            ->whereNotNull('sent_at')
            ->with('task')
            ->orderByDesc('sent_at')
            ->limit(20)
            ->get();

        $tasks = $user->tasks()->active()->orderBy('due_date')->get(['id', 'title']);

        $unreadCount = $user->unreadNotifications()->count();

        return view('reminders.index', compact('pending', 'sent', 'tasks', 'unreadCount'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'task_id'   => ['required', 'exists:tasks,id'],
            'remind_at' => ['required', 'date', 'after_or_equal:now'],
        ]);

        $task = $request->user()->tasks()->findOrFail($validated['task_id']);

        $reminder = $task->reminders()->create([
            'task_id'   => $validated['task_id'],
            'remind_at' => $validated['remind_at'],
            'type'      => 'push',
        ]);

        // Disparar inmediatamente si la hora ya llegó
        if ($reminder->remind_at->lte(now()->addMinutes(1))) {
            SendTaskReminder::dispatch($reminder);
        }

        return redirect()->route('reminders.index')
            ->with('success', 'Recordatorio creado.');
    }

    public function destroy(Reminder $reminder): RedirectResponse
    {
        abort_if($reminder->task->user_id !== auth()->id(), 403);

        $reminder->delete();

        return redirect()->route('reminders.index')
            ->with('success', 'Recordatorio eliminado.');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
