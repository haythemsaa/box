<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Reservation $reservation
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

        return (new MailMessage)
            ->subject('Réservation confirmée - BoxManager')
            ->greeting('Bonjour ' . $this->reservation->first_name . ',')
            ->line('Nous avons bien reçu votre demande de réservation.')
            ->line('**Numéro de réservation**: ' . $this->reservation->reservation_number)
            ->line('**Box**: ' . $this->reservation->box->box_number)
            ->line('**Site**: ' . $this->reservation->box->site->name)
            ->line('**Date de début souhaitée**: ' . $this->reservation->desired_start_date->format('d/m/Y'))
            ->line('Votre réservation est valable pendant 48 heures.')
            ->line('Notre équipe va la vérifier et vous contacter rapidement pour finaliser votre location.')
            ->action('Voir ma réservation', url('/reservations/' . $this->reservation->id . '/confirmation'))
            ->line('Merci de votre confiance!')
            ->salutation('L\'équipe ' . $this->reservation->box->site->name);
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
            'desired_start_date' => $this->reservation->desired_start_date->format('Y-m-d'),
            'message' => 'Votre réservation ' . $this->reservation->reservation_number . ' a été créée',
        ];
    }
}
