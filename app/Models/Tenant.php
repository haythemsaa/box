<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'subdomain',
        'custom_domain',
        'email',
        'phone',
        'country',
        'language',
        'currency',
        'plan',
        'max_sites',
        'max_boxes',
        'settings',
        'is_active',
        'trial_ends_at',
        'subscription_ends_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'max_sites' => 'integer',
        'max_boxes' => 'integer',
    ];

    /**
     * Get the sites for the tenant.
     */
    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    /**
     * Get the customers for the tenant.
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Get the contracts for the tenant.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get the invoices for the tenant.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the payments for the tenant.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Check if tenant is on trial.
     */
    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if tenant subscription is active.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->is_active &&
               (!$this->subscription_ends_at || $this->subscription_ends_at->isFuture());
    }

    /**
     * Get total number of boxes across all sites.
     */
    public function getTotalBoxesCount(): int
    {
        return $this->sites()
            ->withCount(['buildings.floors.boxes'])
            ->get()
            ->sum('buildings_count');
    }
}
