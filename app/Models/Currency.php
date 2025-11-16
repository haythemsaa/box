<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'exchange_rate_to_eur',
        'is_active',
    ];

    protected $casts = [
        'exchange_rate_to_eur' => 'decimal:6',
        'is_active' => 'boolean',
    ];

    /**
     * Get tenants using this currency.
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    /**
     * Get sites using this currency.
     */
    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    /**
     * Get invoices in this currency.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get payments in this currency.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get contracts in this currency.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Convert amount from this currency to EUR.
     */
    public function toEur(float $amount): float
    {
        return $amount / $this->exchange_rate_to_eur;
    }

    /**
     * Convert amount from EUR to this currency.
     */
    public function fromEur(float $amountInEur): float
    {
        return $amountInEur * $this->exchange_rate_to_eur;
    }

    /**
     * Format amount with currency symbol.
     */
    public function format(float $amount, int $decimals = 2): string
    {
        return number_format($amount, $decimals) . ' ' . $this->symbol;
    }
}
