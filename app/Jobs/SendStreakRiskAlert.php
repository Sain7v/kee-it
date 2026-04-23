<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\StreakRiskNotification;
use App\Services\StreakService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendStreakRiskAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 30;

    public function __construct(
        public readonly User $user,
    ) {}

    public function handle(StreakService $streakService): void
    {
        $user = $this->user->fresh();

        if (! $user || $user->streak_current === 0) {
            return;
        }

        if (! $streakService->checkStreakAtRisk($user)) {
            return;
        }

        $user->notify(new StreakRiskNotification($user->streak_current));
    }
}
