<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $demo = User::factory()->withStreak(5)->create([
            'name'     => 'Demo Student',
            'email'    => 'demo@keepit.test',
            'password' => Hash::make('password'),
            'preferences' => [
                'priority_method' => 'auto',
                'reminder_hours'  => 24,
                'work_start'      => '08:00',
                'work_end'        => '22:00',
                'work_days'       => ['lunes', 'martes', 'miércoles', 'jueves', 'viernes'],
            ],
        ]);

        // 3 critical tasks due very soon
        Task::factory(3)->critical()->for($demo)->create();

        // 4 overdue tasks
        Task::factory(4)->overdue()->for($demo)->create();

        // 2 tasks due today
        Task::factory(2)->dueToday()->for($demo)->create();

        // 3 in-progress tasks
        Task::factory(3)->inProgress()->for($demo)->create();

        // 5 completed tasks spread over last 5 days (one per day for streak)
        foreach (range(0, 4) as $daysAgo) {
            Task::factory()->completed()->for($demo)->create([
                'updated_at' => Carbon::today()->subDays($daysAgo)->setTime(14, 0),
            ]);
        }

        // 3 regular pending tasks due in the next 2 weeks
        Task::factory(3)->for($demo)->create();
    }
}
