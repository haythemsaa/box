<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractInsurance extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'insurance_product_id',
        'monthly_premium',
        'commission_amount',
        'coverage_amount',
        'start_date',
        'end_date',
        'status',
        'cancelled_at',
    ];

    protected $casts = [
        'monthly_premium' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'coverage_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the contract this insurance belongs to.
     */
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    /**
     * Get the insurance product.
     */
    public function insuranceProduct(): BelongsTo
    {
        return $this->belongsTo(InsuranceProduct::class);
    }

    /**
     * Cancel the insurance.
     */
    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'end_date' => now()->toDateString(),
        ]);
    }

    /**
     * Check if insurance is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active'
            && (!$this->end_date || $this->end_date->isFuture());
    }

    /**
     * Scope for active insurances.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>', now());
            });
    }

    /**
     * Calculate total premium paid to date.
     */
    public function getTotalPremiumPaid(): float
    {
        if (!$this->start_date) {
            return 0;
        }

        $endDate = $this->end_date ?? now();
        $months = $this->start_date->diffInMonths($endDate);

        return $this->monthly_premium * max(1, $months);
    }

    /**
     * Calculate total commission earned.
     */
    public function getTotalCommissionEarned(): float
    {
        if (!$this->start_date) {
            return 0;
        }

        $endDate = $this->end_date ?? now();
        $months = $this->start_date->diffInMonths($endDate);

        return $this->commission_amount * max(1, $months);
    }
}
