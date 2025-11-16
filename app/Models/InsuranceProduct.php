<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InsuranceProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'max_coverage_amount',
        'coverage_details',
        'exclusions',
        'monthly_price',
        'yearly_price',
        'commission_rate',
        'is_active',
        'is_mandatory',
    ];

    protected $casts = [
        'max_coverage_amount' => 'decimal:2',
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'is_mandatory' => 'boolean',
    ];

    /**
     * Get the tenant that owns this insurance product.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get all contract insurances using this product.
     */
    public function contractInsurances(): HasMany
    {
        return $this->hasMany(ContractInsurance::class);
    }

    /**
     * Calculate commission amount for a given premium.
     */
    public function calculateCommission(float $premium): float
    {
        return $premium * ($this->commission_rate / 100);
    }

    /**
     * Get active insurance products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get mandatory insurance products.
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Format price with currency.
     */
    public function formatMonthlyPrice(string $symbol = '€'): string
    {
        return number_format($this->monthly_price, 2) . ' ' . $symbol . '/mois';
    }

    /**
     * Format yearly price with currency.
     */
    public function formatYearlyPrice(string $symbol = '€'): string
    {
        if (!$this->yearly_price) {
            return number_format($this->monthly_price * 12, 2) . ' ' . $symbol . '/an';
        }
        return number_format($this->yearly_price, 2) . ' ' . $symbol . '/an';
    }

    /**
     * Calculate yearly savings if yearly price is set.
     */
    public function getYearlySavings(): float
    {
        if (!$this->yearly_price) {
            return 0;
        }
        return ($this->monthly_price * 12) - $this->yearly_price;
    }
}
