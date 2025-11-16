<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Invoice $invoice,
        public int $daysUntilDue
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

        $subject = $this->daysUntilDue > 0
            ? "Rappel: Facture à régler dans {$this->daysUntilDue} jour(s)"
            : ($this->daysUntilDue == 0
                ? "Rappel: Facture à régler aujourd'hui"
                : "Facture en retard de " . abs($this->daysUntilDue) . " jour(s)");

        $message = (new MailMessage)
                    ->subject($subject)
                    ->greeting('Bonjour ' . $this->invoice->customer->first_name . ',');

        if ($this->daysUntilDue > 0) {
            $message->line("Votre facture arrive à échéance dans {$this->daysUntilDue} jour(s).");
        } elseif ($this->daysUntilDue == 0) {
            $message->line("Votre facture arrive à échéance aujourd'hui.");
        } else {
            $message->error()
                    ->line("Votre facture est en retard de " . abs($this->daysUntilDue) . " jour(s).")
                    ->line("Merci de régulariser votre situation au plus vite.");
        }

        return $message
                    ->line('**Numéro de facture**: ' . $this->invoice->invoice_number)
                    ->line('**Montant**: ' . number_format($this->invoice->total_amount, 2) . ' €')
                    ->line('**Date d\'échéance**: ' . $this->invoice->due_date->format('d/m/Y'))
                    ->action('Voir ma facture', url('/client/invoices'))
                    ->line('Merci de votre attention.');
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
            'days_until_due' => $this->daysUntilDue,
            'amount' => $this->invoice->total_amount,
            'message' => $this->daysUntilDue > 0
                ? "Facture {$this->invoice->invoice_number} à régler dans {$this->daysUntilDue} jour(s)"
                : "Facture {$this->invoice->invoice_number} en retard",
        ];
    }
}
