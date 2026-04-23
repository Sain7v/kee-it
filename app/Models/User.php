<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'preferences',
        'streak_current',
        'streak_best',
        'streak_last_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'preferences'        => 'array',
            'streak_last_active' => 'date',
            'streak_current'     => 'integer',
            'streak_best'        => 'integer',
        ];
    }

    // ── Relationships ──────────────────────────────────────────

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function streakLogs(): HasMany
    {
        return $this->hasMany(StreakLog::class)->orderByDesc('date');
    }

    // ── Helpers ────────────────────────────────────────────────

    public function preference(string $key, mixed $default = null): mixed
    {
        return data_get($this->preferences, $key, $default);
    }

    public function initials(): string
    {
        return strtoupper(
            collect(explode(' ', $this->name))
                ->take(2)
                ->map(fn ($word) => $word[0] ?? '')
                ->implode('')
        );
    }

    public function streakMotivation(): string
    {
        return match (true) {
            $this->streak_current === 0    => '¡Empieza hoy tu racha!',
            $this->streak_current <= 2     => '¡Buen comienzo, sigue así!',
            $this->streak_current <= 6     => '¡Vas muy bien, no te detengas!',
            $this->streak_current <= 13    => '¡Una semana completa, eres increíble!',
            default                        => '¡Racha legendaria, eres imparable!',
        };
    }
}
