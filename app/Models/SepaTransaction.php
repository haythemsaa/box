<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SepaTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'invoice_id',
        'payment_id',
        'transaction_reference',
        'mandate_reference',
        'creditor_identifier',
        'amount',
        'currency',
        'debtor_name',
        'debtor_iban',
        'debtor_bic',
        'collection_date',
        'requested_date',
        'status',
        'end_to_end_id',
        'description',
        'bank_response',
        'failure_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'collection_date' => 'date',
        'requested_date' => 'date',
    ];

    /**
     * Get the customer for this transaction
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the invoice for this transaction
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the payment for this transaction
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Scope for pending transactions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for completed transactions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for failed transactions
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed', 'rejected']);
    }

    /**
     * Check if transaction is successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if transaction has failed
     */
    public function hasFailed(): bool
    {
        return in_array($this->status, ['failed', 'rejected']);
    }

    /**
     * Mark transaction as submitted
     */
    public function markAsSubmitted(?string $bankResponse = null): void
    {
        $this->update([
            'status' => 'submitted',
            'bank_response' => $bankResponse,
        ]);
    }

    /**
     * Mark transaction as completed
     */
    public function markAsCompleted(?string $bankResponse = null): void
    {
        $this->update([
            'status' => 'completed',
            'bank_response' => $bankResponse,
        ]);
    }

    /**
     * Mark transaction as failed
     */
    public function markAsFailed(string $reason, ?string $bankResponse = null): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
            'bank_response' => $bankResponse,
        ]);
    }

    /**
     * Mark transaction as rejected
     */
    public function markAsRejected(string $reason, ?string $bankResponse = null): void
    {
        $this->update([
            'status' => 'rejected',
            'failure_reason' => $reason,
            'bank_response' => $bankResponse,
        ]);
    }

    /**
     * Cancel the transaction
     */
    public function cancel(): void
    {
        if (!in_array($this->status, ['pending', 'submitted'])) {
            throw new \Exception('Cannot cancel a transaction that is not pending or submitted.');
        }

        $this->update(['status' => 'cancelled']);
    }

    /**
     * Generate a unique transaction reference
     */
    public static function generateReference(): string
    {
        do {
            $reference = 'SEPA-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        } while (static::where('transaction_reference', $reference)->exists());

        return $reference;
    }

    /**
     * Format IBAN for display (with spaces)
     */
    public function getFormattedIbanAttribute(): string
    {
        return chunk_split($this->debtor_iban, 4, ' ');
    }
}
