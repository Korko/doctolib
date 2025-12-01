<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SlotAvailableNotification extends Notification
{
    use Queueable;

    protected array $slots;
    protected string $practitioner;
    protected string $motive;

    public function __construct(string $practitioner, string $motive, array $slots)
    {
        $this->practitioner = $practitioner;
        $this->motive = $motive;
        $this->slots = $slots;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Créneaux disponibles pour {$this->practitioner}")
            ->line("Des créneaux pour {$this->motive} sont disponibles :")
            ->line(implode(', ', $this->slots))
            ->line('Connectez-vous à Doctolib pour réserver rapidement.');
    }
}
