<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRefundedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Payment $payment,
        public float $refundAmount
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
        $this->payment->load(['invoice', 'customer', 'currency']);

        return (new MailMessage)
            ->subject('Remboursement effectué')
            ->greeting('Bonjour ' . $this->payment->customer->first_name . ',')
            ->line('Nous vous informons qu\'un remboursement a été effectué sur votre compte.')
            ->line('**Montant remboursé**: ' . number_format($this->refundAmount, 2) . ' ' . ($this->payment->currency->symbol ?? '€'))
            ->line('**Paiement original**: ' . number_format($this->payment->amount, 2) . ' ' . ($this->payment->currency->symbol ?? '€'))
            ->line('**Date du paiement**: ' . $this->payment->payment_date->format('d/m/Y'))
            ->line('**Facture**: ' . ($this->payment->invoice->invoice_number ?? 'N/A'))
            ->line('Le remboursement sera visible sur votre relevé bancaire sous 5 à 10 jours ouvrés.')
            ->line('Pour toute question concernant ce remboursement, n\'hésitez pas à nous contacter.')
            ->salutation('L\'équipe BoxManager');
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
            'invoice_number' => $this->payment->invoice->invoice_number ?? null,
            'refund_amount' => $this->refundAmount,
            'original_amount' => $this->payment->amount,
            'currency' => $this->payment->currency->code ?? 'EUR',
            'payment_date' => $this->payment->payment_date->format('Y-m-d'),
            'message' => 'Remboursement de ' . number_format($this->refundAmount, 2) . ' ' . ($this->payment->currency->code ?? 'EUR'),
        ];
    }
}
