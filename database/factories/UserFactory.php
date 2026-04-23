<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'               => fake()->name(),
            'email'              => fake()->unique()->safeEmail(),
            'email_verified_at'  => now(),
            'password'           => static::$password ??= Hash::make('password'),
            'remember_token'     => Str::random(10),
            'streak_current'     => 0,
            'streak_best'        => 0,
            'streak_last_active' => null,
            'preferences'        => [
                'priority_method' => 'auto',
                'reminder_hours'  => 24,
            ],
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function withStreak(int $days = 5): static
    {
        return $this->state(fn (array $attributes) => [
            'streak_current'     => $days,
            'streak_best'        => max($attributes['streak_best'] ?? 0, $days),
            'streak_last_active' => today()->toDateString(),
        ]);
    }
}
