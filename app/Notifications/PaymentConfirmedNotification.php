<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Payment $payment
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
        $this->payment->load(['customer', 'invoice', 'contract.box']);

        return (new MailMessage)
                    ->subject('Paiement confirmé')
                    ->greeting('Bonjour ' . $this->payment->customer->first_name . ',')
                    ->line('Nous avons bien reçu votre paiement.')
                    ->line('**Montant**: ' . number_format($this->payment->amount, 2) . ' €')
                    ->line('**Date**: ' . $this->payment->payment_date->format('d/m/Y'))
                    ->line('**Méthode**: ' . ucfirst($this->payment->payment_method))
                    ->line('**ID Transaction**: ' . $this->payment->transaction_id)
                    ->when($this->payment->invoice, function ($message) {
                        return $message->line('**Facture**: ' . $this->payment->invoice->invoice_number);
                    })
                    ->action('Voir mes paiements', url('/client/payments'))
                    ->line('Merci de votre paiement!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount,
            'transaction_id' => $this->payment->transaction_id,
            'message' => 'Paiement de ' . number_format($this->payment->amount, 2) . ' € confirmé',
        ];
    }
}
