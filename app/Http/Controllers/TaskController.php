<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskLog;
use App\Services\ClaudeAIService;
use App\Services\StreakService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct(
        private readonly StreakService   $streakService,
        private readonly ClaudeAIService $aiService,
    ) {}

    public function index(Request $request): View
    {
        $query = $request->user()
            ->tasks()
            ->with('subtasks')
            ->whereNot('status', 'completada');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $sort = $request->get('sort', 'priority');

        match ($sort) {
            'due_date'   => $query->orderBy('due_date'),
            'created_at' => $query->orderByDesc('created_at'),
            default      => $query->orderByDesc('priority_total')->orderBy('due_date'),
        };

        $tasks = $query->paginate(10)->withQueryString();

        $tasksPending = $request->user()->tasks()->active()->count();
        $tasksToday   = $request->user()->tasks()
            ->where('status', 'completada')
            ->whereDate('updated_at', today())
            ->count();

        return view('tasks.index', compact('tasks', 'tasksPending', 'tasksToday'));
    }

    public function create(): View
    {
        return view('tasks.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'             => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
            'category'          => ['required', 'in:tarea,examen,proyecto,lectura,otro'],
            'due_date'          => ['required', 'date'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1'],
        ]);

        $validated['status']             = $validated['status'] ?? 'pendiente';
        $validated['estimated_minutes']  = $validated['estimated_minutes'] ?? 0;

        auth()->user()->tasks()->create($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'Tarea creada correctamente.');
    }

    public function show(Task $task): View
    {
        $this->authorizeTask($task);

        $task->load(['subtasks', 'logs', 'reminders']);

        $breakdownService = app(\App\Services\TaskPriorityService::class);
        $urgency          = max(0, 100 - (now()->diffInDays($task->due_date, false) * -10));

        $priorityBreakdown = [
            'urgency'        => (int) max(0, 100 - (now()->startOfDay()->diffInDays($task->due_date?->startOfDay(), false) * -10)),
            'weight'         => (int) min(30, ($task->estimated_minutes ?? 0) / 30),
            'procrastination' => $task->procrastination_score,
            'total'          => $task->priority_total,
        ];

        $aiSuggestion = $task->ai_suggestion;

        if ($task->isAiCacheStale() && config('services.anthropic.key')) {
            $aiSuggestion = $this->aiService->getSuggestionForTask($task);
            $task->update([
                'ai_suggestion'     => $aiSuggestion,
                'ai_prioritized_at' => now(),
            ]);
        }

        return view('tasks.show', compact('task', 'priorityBreakdown', 'aiSuggestion'));
    }

    public function edit(Task $task): View
    {
        $this->authorizeTask($task);
        $task->load('subtasks');

        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $this->authorizeTask($task);

        $validated = $request->validate([
            'title'             => ['sometimes', 'required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
            'category'          => ['sometimes', 'required', 'in:tarea,examen,proyecto,lectura,otro'],
            'status'            => ['sometimes', 'required', 'in:pendiente,en_progreso,completada,cancelada'],
            'due_date'          => ['required', 'date'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1'],
            'is_recurring'      => ['boolean'],
            'recurrence_rule'   => ['nullable', 'string', 'max:255'],
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'Tarea actualizada correctamente.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->authorizeTask($task);

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Tarea eliminada correctamente.');
    }

    public function complete(Task $task): RedirectResponse
    {
        $this->authorizeTask($task);

        $task->update(['status' => 'completada']);

        $this->streakService->updateStreak(auth()->user());

        return redirect()->route('tasks.index')
            ->with('success', '¡Tarea completada! 🎉');
    }

    public function postpone(Task $task): RedirectResponse
    {
        $this->authorizeTask($task);

        $task->update([
            'due_date'            => $task->due_date->addDay(),
            'procrastination_score' => $task->procrastination_score + config('services.kee_it.procrastination_penalty', 5),
        ]);

        TaskLog::record($task, 'Tarea pospuesta +1 día');

        return redirect()->route('tasks.index')
            ->with('success', 'Tarea pospuesta un día.');
    }

    public function toggleComplete(Task $task): RedirectResponse
    {
        $this->authorizeTask($task);

        $newStatus = in_array($task->status, ['pendiente', 'en_progreso'])
            ? 'completada'
            : 'pendiente';

        $task->update(['status' => $newStatus]);

        if ($newStatus === 'completada') {
            $this->streakService->updateStreak(auth()->user());
        }

        return redirect()->route('tasks.index');
    }

    public function aiPrioritize(Request $request): JsonResponse
    {
        $tasks = $request->user()
            ->tasks()
            ->active()
            ->orderByDesc('priority_total')
            ->get();

        if ($tasks->isEmpty()) {
            return response()->json(['tasks' => []]);
        }

        $result = $this->aiService->prioritizeTasks($tasks, $request->user());

        return response()->json(['tasks' => $result]);
    }

    public function aiSuggestion(Task $task): JsonResponse
    {
        $this->authorizeTask($task);

        $suggestion = $this->aiService->getSuggestionForTask($task);

        $task->update([
            'ai_suggestion'     => $suggestion,
            'ai_prioritized_at' => now(),
        ]);

        return response()->json(['suggestion' => $suggestion]);
    }

    public function startTimer(Request $request, Task $task): JsonResponse
    {
        $this->authorizeTask($task);

        TaskLog::record($task, 'Cronómetro iniciado');

        return response()->json(['started_at' => now()->toIso8601String()]);
    }

    public function stopTimer(Request $request, Task $task): JsonResponse
    {
        $this->authorizeTask($task);

        $validated = $request->validate([
            'minutes' => ['required', 'integer', 'min:0'],
        ]);

        $task->update([
            'actual_minutes' => ($task->actual_minutes ?? 0) + $validated['minutes'],
        ]);

        TaskLog::record($task, 'Tiempo registrado: ' . $validated['minutes'] . ' min');

        return response()->json([
            'actual_minutes' => $task->actual_minutes,
        ]);
    }

    public function calendarFeed(Request $request): JsonResponse
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end'   => ['required', 'date'],
        ]);

        $tasks = $request->user()
            ->tasks()
            ->whereBetween('due_date', [$request->input('start'), $request->input('end')])
            ->get(['id', 'title', 'due_date', 'priority', 'status']);

        $events = $tasks->map(fn (Task $t) => [
            'id'        => $t->id,
            'title'     => $t->title,
            'start'     => $t->due_date->toDateString(),
            'url'       => route('tasks.show', $t),
            'className' => 'priority-' . $t->priority,
        ]);

        return response()->json($events);
    }

    private function authorizeTask(Task $task): void
    {
        abort_if($task->user_id !== auth()->id(), 403);
    }
}
