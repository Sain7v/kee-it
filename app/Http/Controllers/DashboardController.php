<?php

namespace App\Http\Controllers;

use App\Services\StreakService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly StreakService $streakService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $tasksUrgent = $user->tasks()
            ->with('subtasks')
            ->active()
            ->byPriority()
            ->orderBy('due_date')
            ->limit(config('services.kee_it.max_dashboard_urgent', 5))
            ->get();

        $tasksPending  = $user->tasks()->active()->count();
        $tasksCritical = $user->tasks()->active()->where('priority', 'critica')->count();

        $tasksCompletedToday = $user->tasks()
            ->where('status', 'completada')
            ->whereDate('updated_at', today())
            ->count();

        $weekStart = now()->startOfWeek();
        $weekEnd   = now()->endOfWeek();

        $weekTotal     = $user->tasks()->whereBetween('due_date', [$weekStart, $weekEnd])->count();
        $weekCompleted = $user->tasks()
            ->where('status', 'completada')
            ->whereBetween('due_date', [$weekStart, $weekEnd])
            ->count();

        $weekProgress = [
            'completed' => $weekCompleted,
            'total'     => max($weekTotal, 1),
            'percent'   => $weekTotal > 0 ? round(($weekCompleted / $weekTotal) * 100) : 0,
            'label'     => now()->locale('es')->isoFormat('[Semana del] D [de] MMMM'),
        ];

        $streak = [
            'current'  => $user->streak_current,
            'best'     => $user->streak_best,
            'at_risk'  => $this->streakService->checkStreakAtRisk($user),
        ];

        $streakCalendar = $this->streakService->getStreakCalendar($user, 7);

        return view('dashboard', compact(
            'tasksUrgent',
            'tasksPending',
            'tasksCritical',
            'tasksCompletedToday',
            'weekProgress',
            'streak',
            'streakCalendar',
        ));
    }
}
