<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_number',
        'tenant_id',
        'customer_id',
        'box_id',
        'site_id',
        'status',
        'start_date',
        'end_date',
        'duration_type',
        'initial_duration_months',
        'auto_renewal',
        'billing_frequency',
        'monthly_price',
        'deposit_amount',
        'setup_fee',
        'insurance_included',
        'insurance_amount',
        'declared_value',
        'stored_items',
        'payment_method',
        'access_code',
        'access_schedule',
        'special_conditions',
        'signed_at',
        'signature_method',
        'signed_documents',
        'terminated_at',
        'termination_reason',
        'notice_period_days',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'signed_at' => 'date',
        'terminated_at' => 'date',
        'auto_renewal' => 'boolean',
        'insurance_included' => 'boolean',
        'monthly_price' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'setup_fee' => 'decimal:2',
        'insurance_amount' => 'decimal:2',
        'declared_value' => 'decimal:2',
        'stored_items' => 'array',
        'access_schedule' => 'array',
        'signed_documents' => 'array',
        'initial_duration_months' => 'integer',
        'notice_period_days' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function contractInsurances(): HasMany
    {
        return $this->hasMany(ContractInsurance::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isTerminated(): bool
    {
        return in_array($this->status, ['terminated', 'cancelled']);
    }

    /**
     * Generate unique contract number.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            if (empty($contract->contract_number)) {
                $contract->contract_number = self::generateContractNumber();
            }

            if (empty($contract->access_code)) {
                $contract->access_code = self::generateAccessCode();
            }
        });
    }

    private static function generateContractNumber(): string
    {
        $year = date('Y');
        $lastContract = self::whereYear('created_at', $year)->latest('id')->first();
        $nextNumber = $lastContract ? (intval(substr($lastContract->contract_number, -6)) + 1) : 1;

        return sprintf('CTR-%s-%06d', $year, $nextNumber);
    }

    private static function generateAccessCode(): string
    {
        return strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
    }
}
