<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceAvailableNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Invoice $invoice
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
        $this->invoice->load(['customer', 'contract.box']);

        return (new MailMessage)
                    ->subject('Nouvelle facture disponible')
                    ->greeting('Bonjour ' . $this->invoice->customer->first_name . ',')
                    ->line('Une nouvelle facture est disponible sur votre espace client.')
                    ->line('**Numéro de facture**: ' . $this->invoice->invoice_number)
                    ->line('**Montant**: ' . number_format($this->invoice->total_amount, 2) . ' €')
                    ->line('**Date d\'émission**: ' . $this->invoice->issue_date->format('d/m/Y'))
                    ->line('**Date d\'échéance**: ' . $this->invoice->due_date->format('d/m/Y'))
                    ->action('Voir ma facture', url('/client/invoices'))
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
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'amount' => $this->invoice->total_amount,
            'message' => 'Nouvelle facture ' . $this->invoice->invoice_number,
        ];
    }
}
