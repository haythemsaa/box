<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_reference',
        'tenant_id',
        'customer_id',
        'invoice_id',
        'contract_id',
        'type',
        'status',
        'method',
        'amount',
        'currency',
        'fee_amount',
        'net_amount',
        'payment_gateway',
        'gateway_transaction_id',
        'gateway_payment_intent_id',
        'gateway_response',
        'card_brand',
        'card_last4',
        'sepa_mandate_id',
        'bank_account_iban',
        'check_number',
        'check_date',
        'processed_at',
        'failed_at',
        'failure_reason',
        'failure_code',
        'refunded_at',
        'refunded_amount',
        'refund_reason',
        'description',
        'receipt_url',
        'receipt_number',
        'metadata',
        'notes',
    ];

    protected $casts = [
        'check_date' => 'date',
        'processed_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
        'amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'gateway_response' => 'array',
        'metadata' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_reference)) {
                $payment->payment_reference = self::generatePaymentReference();
            }

            // Calculate net amount
            if ($payment->amount && $payment->fee_amount) {
                $payment->net_amount = $payment->amount - $payment->fee_amount;
            } else {
                $payment->net_amount = $payment->amount;
            }
        });
    }

    private static function generatePaymentReference(): string
    {
        return 'PAY-' . strtoupper(uniqid());
    }
}
