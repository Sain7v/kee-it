<?php

namespace App\Services;

use App\Models\StreakLog;
use App\Models\User;
use App\Notifications\AchievementUnlockedNotification;
use Carbon\Carbon;

class StreakService
{
    private const ACHIEVEMENTS = [
        'primera_llama'  => 3,
        'semana_de_fuego' => 7,
        'imparable'      => 14,
        'leyenda'        => 30,
    ];

    private const ACHIEVEMENT_LABELS = [
        'primera_llama'   => 'Primera llama 🔥',
        'semana_de_fuego' => 'Semana de fuego 🔥🔥',
        'imparable'       => 'Imparable ⚡',
        'leyenda'         => 'Leyenda 👑',
    ];

    public function updateStreak(User $user): void
    {
        $today     = today();
        $lastActive = $user->streak_last_active;

        if ($lastActive && Carbon::parse($lastActive)->isSameDay($today)) {
            $this->upsertStreakLog($user, $today);
            return;
        }

        if ($lastActive && Carbon::parse($lastActive)->isSameDay($today->copy()->subDay())) {
            $user->streak_current += 1;
        } else {
            $user->streak_current = 1;
        }

        if ($user->streak_current > $user->streak_best) {
            $user->streak_best = $user->streak_current;
        }

        $user->streak_last_active = $today;
        $user->save();

        $this->upsertStreakLog($user, $today);
        $this->checkAndUnlockAchievements($user);
    }

    public function checkStreakAtRisk(User $user): bool
    {
        if ($user->streak_current === 0 || ! $user->streak_last_active) {
            return false;
        }

        $lastActive = Carbon::parse($user->streak_last_active);

        if (! $lastActive->isSameDay(today()->subDay())) {
            return false;
        }

        return $user->tasks()
            ->where('status', 'completada')
            ->whereDate('updated_at', today())
            ->doesntExist();
    }

    public function getStreakCalendar(User $user, int $days = 30): array
    {
        $logs = StreakLog::where('user_id', $user->id)
            ->where('date', '>=', today()->subDays($days - 1))
            ->orderBy('date')
            ->get()
            ->keyBy(fn ($log) => $log->date->toDateString());

        $calendar = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date   = today()->subDays($i)->toDateString();
            $log    = $logs->get($date);

            $calendar[] = [
                'date'            => $date,
                'is_productive'   => $log?->is_productive ?? false,
                'tasks_completed' => $log?->tasks_completed ?? 0,
            ];
        }

        return $calendar;
    }

    public function checkAndUnlockAchievements(User $user): void
    {
        $preferences  = $user->preferences ?? [];
        $achievements = $preferences['achievements'] ?? [];
        $unlocked     = false;

        foreach (self::ACHIEVEMENTS as $key => $requiredDays) {
            if (isset($achievements[$key])) {
                continue;
            }

            if ($user->streak_current >= $requiredDays) {
                $achievements[$key] = now()->toDateString();
                $unlocked           = true;

                $user->notify(new AchievementUnlockedNotification(
                    self::ACHIEVEMENT_LABELS[$key]
                ));
            }
        }

        if ($unlocked) {
            $preferences['achievements'] = $achievements;
            $user->preferences           = $preferences;
            $user->save();
        }
    }

    private function upsertStreakLog(User $user, Carbon $date): void
    {
        $completedToday = $user->tasks()
            ->where('status', 'completada')
            ->whereDate('updated_at', $date)
            ->count();

        StreakLog::updateOrCreate(
            ['user_id' => $user->id, 'date' => $date->toDateString()],
            [
                'tasks_completed' => $completedToday,
                'is_productive'   => $completedToday > 0,
            ]
        );
    }
}
