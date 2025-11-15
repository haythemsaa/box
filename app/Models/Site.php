<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Site extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'address',
        'city',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'phone',
        'email',
        'manager_name',
        'opening_hours',
        'access_hours',
        'access_control_type',
        'has_surveillance',
        'has_alarm',
        'equipments',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'opening_hours' => 'array',
        'access_hours' => 'array',
        'equipments' => 'array',
        'settings' => 'array',
        'has_surveillance' => 'boolean',
        'has_alarm' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the tenant that owns the site.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the buildings for the site.
     */
    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }

    /**
     * Get all boxes through buildings and floors.
     */
    public function boxes(): HasManyThrough
    {
        return $this->hasManyThrough(
            Box::class,
            Building::class,
            'site_id',
            'floor_id',
            'id',
            'id'
        )->join('floors', 'floors.building_id', '=', 'buildings.id');
    }

    /**
     * Get the contracts for the site.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get occupancy rate.
     */
    public function getOccupancyRate(): float
    {
        $totalBoxes = $this->boxes()->count();
        if ($totalBoxes === 0) {
            return 0;
        }

        $occupiedBoxes = $this->boxes()->where('status', 'occupied')->count();
        return ($occupiedBoxes / $totalBoxes) * 100;
    }

    /**
     * Get available boxes count.
     */
    public function getAvailableBoxesCount(): int
    {
        return $this->boxes()->where('status', 'available')->count();
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('plans')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
    }
}
