<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $dueDate = fake()->dateTimeBetween('now', '+30 days');
        $estimatedMinutes = fake()->randomElement([15, 30, 45, 60, 90, 120, 180]);

        return [
            'user_id'            => User::factory(),
            'title'              => fake()->sentence(4),
            'description'        => fake()->optional(0.6)->paragraph(),
            'category'           => fake()->randomElement(['tarea', 'examen', 'proyecto', 'lectura', 'otro']),
            'priority'           => fake()->randomElement(['baja', 'media', 'alta', 'critica']),
            'status'             => 'pendiente',
            'due_date'           => $dueDate,
            'estimated_minutes'  => $estimatedMinutes,
            'actual_minutes'     => null,
            'procrastination_score' => 0,
            'priority_total'     => fake()->numberBetween(0, 100),
            'is_recurring'       => false,
            'recurrence_rule'    => null,
            'ai_suggestion'      => null,
            'ai_prioritized_at'  => null,
        ];
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date'              => fake()->dateTimeBetween('-14 days', '-1 day'),
            'procrastination_score' => fake()->numberBetween(5, 40),
            'status'                => 'pendiente',
            'priority'              => fake()->randomElement(['alta', 'critica']),
            'priority_total'        => fake()->numberBetween(60, 100),
        ]);
    }

    public function critical(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date'              => fake()->dateTimeBetween('now', '+2 days'),
            'priority'              => 'critica',
            'procrastination_score' => fake()->numberBetween(0, 20),
            'priority_total'        => fake()->numberBetween(80, 100),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'        => 'completada',
            'priority_total' => 0,
            'updated_at'    => fake()->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'en_progreso',
        ]);
    }

    public function dueToday(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => today(),
            'priority' => fake()->randomElement(['alta', 'critica']),
        ]);
    }
}
