<?php

namespace App\Notifications;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Contract $contract
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
        $this->contract->load(['box.site', 'customer']);

        return (new MailMessage)
                    ->subject('Votre contrat de location BoxManager')
                    ->greeting('Bonjour ' . $this->contract->customer->first_name . ',')
                    ->line('Votre contrat de location a été créé avec succès.')
                    ->line('**Numéro de contrat**: ' . $this->contract->contract_number)
                    ->line('**Box**: ' . $this->contract->box->number . ' - ' . $this->contract->box->site->name)
                    ->line('**Date de début**: ' . $this->contract->start_date->format('d/m/Y'))
                    ->line('**Montant mensuel**: ' . number_format($this->contract->monthly_amount, 2) . ' €')
                    ->line('**Code d\'accès**: ' . $this->contract->access_code)
                    ->action('Voir mon contrat', url('/client/contracts/' . $this->contract->id))
                    ->line('Merci de votre confiance!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'contract_id' => $this->contract->id,
            'contract_number' => $this->contract->contract_number,
            'message' => 'Nouveau contrat créé: ' . $this->contract->contract_number,
        ];
    }
}
