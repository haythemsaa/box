<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Box extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'floor_id',
        'number',
        'type',
        'length',
        'width',
        'height',
        'volume',
        'area',
        'monthly_price',
        'status',
        'has_vehicle_access',
        'is_ground_floor',
        'has_power_outlet',
        'features',
        'description',
    ];

    protected $casts = [
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'volume' => 'decimal:3',
        'area' => 'decimal:2',
        'monthly_price' => 'decimal:2',
        'has_vehicle_access' => 'boolean',
        'is_ground_floor' => 'boolean',
        'has_power_outlet' => 'boolean',
        'features' => 'array',
    ];

    /**
     * Get the floor that owns the box.
     */
    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    /**
     * Get the contracts for the box.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get the active contract for the box.
     */
    public function activeContract()
    {
        return $this->hasOne(Contract::class)->where('status', 'active');
    }

    /**
     * Check if box is available.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Check if box is occupied.
     */
    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }

    /**
     * Get the site through floor and building.
     */
    public function site()
    {
        return $this->floor->building->site ?? null;
    }

    /**
     * Calculate volume automatically.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($box) {
            // Calculate volume in m³ (dimensions are in cm)
            $box->volume = ($box->length * $box->width * $box->height) / 1000000;
            // Calculate area in m²
            $box->area = ($box->length * $box->width) / 10000;
        });
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }
}
