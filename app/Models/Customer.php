<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Customer extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'tenant_id',
        'type',
        'civility',
        'first_name',
        'last_name',
        'company_name',
        'siret',
        'vat_number',
        'email',
        'phone_mobile',
        'phone_landline',
        'birth_date',
        'address',
        'address_complement',
        'postal_code',
        'city',
        'country',
        'billing_address',
        'billing_postal_code',
        'billing_city',
        'billing_country',
        'language',
        'communication_preferences',
        'acquisition_source',
        'tags',
        'is_verified',
        'is_vip',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'communication_preferences' => 'array',
        'tags' => 'array',
        'is_verified' => 'boolean',
        'is_vip' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->type === 'company' && $this->company_name
            ? $this->company_name
            : $this->full_name;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('identity_documents')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'application/pdf']);

        $this->addMediaCollection('proof_of_address')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'application/pdf']);
    }
}
