<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Floor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'building_id',
        'level',
        'name',
        'has_elevator',
        'has_freight_elevator',
        'corridor_width',
    ];

    protected $casts = [
        'level' => 'integer',
        'corridor_width' => 'decimal:2',
        'has_elevator' => 'boolean',
        'has_freight_elevator' => 'boolean',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function boxes(): HasMany
    {
        return $this->hasMany(Box::class);
    }
}
