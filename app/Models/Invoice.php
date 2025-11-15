<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'tenant_id',
        'customer_id',
        'contract_id',
        'type',
        'status',
        'issue_date',
        'due_date',
        'paid_at',
        'period_start',
        'period_end',
        'line_items',
        'subtotal_ht',
        'vat_amount',
        'vat_rate',
        'discount_amount',
        'discount_type',
        'total_ttc',
        'amount_paid',
        'amount_due',
        'currency',
        'payment_method',
        'payment_instructions',
        'billing_address',
        'billing_city',
        'billing_postal_code',
        'billing_country',
        'legal_mentions',
        'pdf_path',
        'sent_to_customer',
        'sent_at',
        'reminder_count',
        'last_reminder_at',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'date',
        'sent_at' => 'datetime',
        'last_reminder_at' => 'datetime',
        'line_items' => 'array',
        'legal_mentions' => 'array',
        'subtotal_ht' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'sent_to_customer' => 'boolean',
        'reminder_count' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue' ||
               ($this->status === 'sent' && $this->due_date < now());
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = self::generateInvoiceNumber();
            }
        });
    }

    private static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $lastInvoice = self::whereYear('created_at', $year)->latest('id')->first();
        $nextNumber = $lastInvoice ? (intval(substr($lastInvoice->invoice_number, -6)) + 1) : 1;

        return sprintf('INV-%s-%06d', $year, $nextNumber);
    }
}
