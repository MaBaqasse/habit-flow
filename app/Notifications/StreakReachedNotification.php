<?php

namespace App\Notifications;

use App\Models\Habit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StreakReachedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Habit $habit, public int $streakCount) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🔥 Record battu pour votre habitude !')
            ->greeting('Félicitations ' . $notifiable->name . ' !')
            ->line('Vous avez maintenu votre habitude **' . $this->habit->name . '** pendant ' . $this->streakCount . ' jours consécutifs.')
            ->line('C\'est un accomplissement incroyable, continuez sur cette lancée !')
            ->action('Voir mon Dashboard', url('/dashboard'))
            ->line('Le secret du succès, c\'est la régularité.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
