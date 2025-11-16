<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Invoice $invoice,
        public string $errorMessage = ''
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
        $this->invoice->load(['customer', 'currency', 'contract.box.site']);

        $mail = (new MailMessage)
            ->subject('Échec de paiement - Action requise')
            ->greeting('Bonjour ' . $this->invoice->customer->first_name . ',')
            ->line('Nous vous informons que le paiement de votre facture a échoué.')
            ->line('**Numéro de facture**: ' . $this->invoice->invoice_number)
            ->line('**Montant**: ' . number_format($this->invoice->total_amount, 2) . ' ' . ($this->invoice->currency->symbol ?? '€'))
            ->line('**Date d\'échéance**: ' . $this->invoice->due_date->format('d/m/Y'));

        if ($this->errorMessage) {
            $mail->line('**Raison de l\'échec**: ' . $this->errorMessage);
        }

        $mail->line('Pour régulariser votre situation, veuillez:')
            ->line('- Vérifier que votre carte bancaire est valide et dispose de fonds suffisants')
            ->line('- Réessayer le paiement via votre espace client')
            ->line('- Ou nous contacter pour un autre moyen de paiement')
            ->action('Payer maintenant', url('/client/invoices/' . $this->invoice->id . '/checkout'))
            ->line('En cas de difficulté, n\'hésitez pas à nous contacter.')
            ->salutation('L\'équipe ' . ($this->invoice->contract->box->site->name ?? 'BoxManager'));

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
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'amount' => $this->invoice->total_amount,
            'currency' => $this->invoice->currency->code ?? 'EUR',
            'due_date' => $this->invoice->due_date->format('Y-m-d'),
            'error_message' => $this->errorMessage,
            'message' => 'Le paiement de la facture ' . $this->invoice->invoice_number . ' a échoué',
        ];
    }
}
