<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Reservation $reservation,
        public string $reason = ''
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $this->reservation->load(['box.site']);

        $mail = (new MailMessage)
            ->subject('Réservation annulée')
            ->greeting('Bonjour ' . $this->reservation->first_name . ',')
            ->line('Votre réservation a été annulée.')
            ->line('**Numéro de réservation**: ' . $this->reservation->reservation_number)
            ->line('**Box**: ' . $this->reservation->box->box_number)
            ->line('**Site**: ' . $this->reservation->box->site->name);

        if ($this->reason) {
            $mail->line('**Raison**: ' . $this->reason);
        }

        $mail->line('Si vous souhaitez effectuer une nouvelle réservation, n\'hésitez pas à consulter nos boxes disponibles.')
            ->action('Voir les boxes disponibles', url('/reservations'))
            ->line('Pour toute question, n\'hésitez pas à nous contacter.')
            ->salutation('L\'équipe ' . $this->reservation->box->site->name);

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'reservation_number' => $this->reservation->reservation_number,
            'box_number' => $this->reservation->box->box_number,
            'site_name' => $this->reservation->box->site->name,
            'reason' => $this->reason,
            'message' => 'Votre réservation ' . $this->reservation->reservation_number . ' a été annulée',
        ];
    }
}
