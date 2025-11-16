<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VatRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_code',
        'country_name',
        'standard_rate',
        'reduced_rate',
        'super_reduced_rate',
        'is_eu_member',
        'is_active',
    ];

    protected $casts = [
        'standard_rate' => 'decimal:2',
        'reduced_rate' => 'decimal:2',
        'super_reduced_rate' => 'decimal:2',
        'is_eu_member' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get VAT rate by country code.
     */
    public static function getByCountryCode(string $countryCode): ?self
    {
        return self::where('country_code', $countryCode)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Calculate VAT amount from subtotal.
     */
    public function calculateVat(float $subtotal, string $rateType = 'standard'): float
    {
        $rate = match ($rateType) {
            'reduced' => $this->reduced_rate,
            'super_reduced' => $this->super_reduced_rate,
            default => $this->standard_rate,
        };

        return $subtotal * ($rate / 100);
    }

    /**
     * Calculate total with VAT.
     */
    public function calculateTotal(float $subtotal, string $rateType = 'standard'): float
    {
        return $subtotal + $this->calculateVat($subtotal, $rateType);
    }

    /**
     * Get active rate based on type.
     */
    public function getRate(string $rateType = 'standard'): float
    {
        return match ($rateType) {
            'reduced' => $this->reduced_rate ?? $this->standard_rate,
            'super_reduced' => $this->super_reduced_rate ?? $this->reduced_rate ?? $this->standard_rate,
            default => $this->standard_rate,
        };
    }
}
