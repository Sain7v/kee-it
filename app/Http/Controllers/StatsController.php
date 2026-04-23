<?php

namespace App\Http\Controllers;

use App\Services\ClaudeAIService;
use App\Services\StreakService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StatsController extends Controller
{
    public function __construct(
        private readonly StreakService   $streakService,
        private readonly ClaudeAIService $aiService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $weeklyCompleted = DB::table('tasks')
            ->select(DB::raw('YEARWEEK(updated_at, 1) as yw, COUNT(*) as total'))
            ->where('user_id', $user->id)
            ->where('status', 'completada')
            ->where('updated_at', '>=', now()->subWeeks(4))
            ->groupBy('yw')
            ->orderBy('yw')
            ->get();

        $categoryDistribution = $user->tasks()
            ->select('category', DB::raw('COUNT(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $weeklyProcrastination = DB::table('tasks')
            ->select(DB::raw('YEARWEEK(updated_at, 1) as yw, AVG(procrastination_score) as avg_score'))
            ->where('user_id', $user->id)
            ->where('updated_at', '>=', now()->subWeeks(4))
            ->groupBy('yw')
            ->orderBy('yw')
            ->get();

        $totalTasks     = $user->tasks()->count();
        $completedOnTime = $user->tasks()
            ->where('status', 'completada')
            ->whereColumn('updated_at', '<=', 'due_date')
            ->count();

        $onTimeRate = $totalTasks > 0
            ? round(($completedOnTime / max($totalTasks, 1)) * 100)
            : 0;

        $streak = [
            'current' => $user->streak_current,
            'best'    => $user->streak_best,
        ];

        $heatmap = $this->streakService->getStreakCalendar($user, 30);

        $achievements = $user->preferences['achievements'] ?? [];

        $weeklyStats = [
            'completed_per_week'       => $weeklyCompleted->pluck('total')->toArray(),
            'avg_procrastination'      => $weeklyProcrastination->pluck('avg_score')->toArray(),
            'on_time_rate'             => $onTimeRate,
            'most_procrastinated_category' => $this->mostProcrastinatedCategory($user),
        ];

        $aiInsight = $this->aiService->analyzeProductivityPattern($user, $weeklyStats);

        return view('stats', compact(
            'weeklyCompleted',
            'categoryDistribution',
            'weeklyProcrastination',
            'onTimeRate',
            'streak',
            'heatmap',
            'achievements',
            'aiInsight',
        ));
    }

    private function mostProcrastinatedCategory($user): string
    {
        $result = $user->tasks()
            ->select('category', DB::raw('AVG(procrastination_score) as avg'))
            ->groupBy('category')
            ->orderByDesc('avg')
            ->first();

        return $result?->category ?? 'ninguna';
    }
}
