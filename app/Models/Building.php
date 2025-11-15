<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Building extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'site_id',
        'name',
        'type',
        'year_built',
        'total_area',
        'has_climate_control',
        'description',
    ];

    protected $casts = [
        'total_area' => 'decimal:2',
        'year_built' => 'integer',
        'has_climate_control' => 'boolean',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class);
    }
}
