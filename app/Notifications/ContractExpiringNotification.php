<?php

namespace App\Notifications;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Contract $contract,
        public int $daysUntilExpiry
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
                    ->subject("Votre contrat expire dans {$this->daysUntilExpiry} jour(s)")
                    ->greeting('Bonjour ' . $this->contract->customer->first_name . ',')
                    ->line("Votre contrat de location arrive à expiration dans {$this->daysUntilExpiry} jour(s).")
                    ->line('**Numéro de contrat**: ' . $this->contract->contract_number)
                    ->line('**Box**: ' . $this->contract->box->number . ' - ' . $this->contract->box->site->name)
                    ->line('**Date d\'expiration**: ' . $this->contract->end_date->format('d/m/Y'))
                    ->line('Si vous souhaitez renouveler votre contrat ou libérer votre box, merci de nous contacter au plus vite.')
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
            'days_until_expiry' => $this->daysUntilExpiry,
            'message' => "Contrat {$this->contract->contract_number} expire dans {$this->daysUntilExpiry} jour(s)",
        ];
    }
}
