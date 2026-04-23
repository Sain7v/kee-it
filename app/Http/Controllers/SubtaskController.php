<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Http\Request;

class SubtaskController extends Controller
{
    public function store(Request $request, Task $task)
    {
        abort_if($task->user_id !== auth()->id(), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $subtask = $task->subtasks()->create([
            'title'        => $data['title'],
            'is_completed' => false,
            'order'        => $task->subtasks()->count(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id'           => $subtask->id,
                'title'        => $subtask->title,
                'is_completed' => false,
            ]);
        }

        return back()->with('success', 'Subtarea añadida.');
    }

    public function toggle(Task $task, Subtask $subtask)
    {
        abort_if($task->user_id !== auth()->id(), 403);
        abort_if($subtask->task_id !== $task->id, 404);

        $subtask->update(['is_completed' => ! $subtask->is_completed]);

        if (request()->expectsJson()) {
            return response()->json(['is_completed' => $subtask->is_completed]);
        }

        return back();
    }

    public function destroy(Task $task, Subtask $subtask)
    {
        abort_if($task->user_id !== auth()->id(), 403);
        abort_if($subtask->task_id !== $task->id, 404);

        $subtask->delete();

        if (request()->expectsJson()) {
            return response()->json(['deleted' => true]);
        }

        return back()->with('success', 'Subtarea eliminada.');
    }
}
