<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StreakRiskNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int $streakDays,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('⚠️ Tu racha de ' . $this->streakDays . ' días está en riesgo, ' . $notifiable->name)
            ->greeting('¡Oye, ' . $notifiable->name . '!')
            ->line('Llevas **' . $this->streakDays . ' días** con racha activa. ¡No la pierdas!')
            ->line('Aún tienes tiempo de completar al menos una tarea antes de medianoche.')
            ->action('Ir a mis tareas', url('/tasks'))
            ->line('Tú puedes. ¡Último empuje del día! 💪');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'streak_risk',
            'streak_days' => $this->streakDays,
            'message'     => '⚠️ Tu racha de ' . $this->streakDays . ' días está en riesgo. Completa una tarea antes de medianoche.',
        ];
    }
}
