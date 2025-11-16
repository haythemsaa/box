<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'site_id',
        'box_id',
        'reservation_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'desired_start_date',
        'estimated_duration_months',
        'notes',
        'status',
        'confirmed_at',
        'expires_at',
        'converted_at',
        'converted_to_contract_id',
    ];

    protected $casts = [
        'desired_start_date' => 'date',
        'confirmed_at' => 'datetime',
        'expires_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reservation) {
            if (empty($reservation->reservation_number)) {
                $reservation->reservation_number = self::generateReservationNumber();
            }

            // Set expiration to 48 hours from now if not set
            if (empty($reservation->expires_at)) {
                $reservation->expires_at = now()->addHours(48);
            }
        });
    }

    /**
     * Generate a unique reservation number.
     */
    public static function generateReservationNumber(): string
    {
        do {
            $number = 'RES-' . date('Y') . '-' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('reservation_number', $number)->exists());

        return $number;
    }

    /**
     * Get the tenant that owns the reservation.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the site for this reservation.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the box for this reservation.
     */
    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    /**
     * Get the contract this reservation was converted to.
     */
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'converted_to_contract_id');
    }

    /**
     * Get full name of the person making the reservation.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Check if the reservation is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast() && $this->status === 'pending';
    }

    /**
     * Confirm the reservation.
     */
    public function confirm(): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Cancel the reservation.
     */
    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
        ]);
    }

    /**
     * Mark as expired.
     */
    public function expire(): void
    {
        $this->update([
            'status' => 'expired',
        ]);
    }

    /**
     * Convert reservation to contract.
     */
    public function convertToContract(Contract $contract): void
    {
        $this->update([
            'status' => 'converted',
            'converted_at' => now(),
            'converted_to_contract_id' => $contract->id,
        ]);
    }

    /**
     * Scope for pending reservations.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for confirmed reservations.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for expired reservations.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
            ->orWhere(function ($q) {
                $q->where('status', 'pending')
                  ->where('expires_at', '<', now());
            });
    }
}
