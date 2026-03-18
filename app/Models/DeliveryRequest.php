<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryRequest extends Model
{
    protected $fillable = [
        'tenant_id',
        'farm_id',
        'status',
        'delivery_mode',
        'pickup_address',
        'delivery_address',
        'cargo_description',
        'cargo_weight_kg',
        'requested_date',
        'accepted_offer_id',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'cargo_weight_kg' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(DeliveryOffer::class);
    }

    public function acceptedOffer(): BelongsTo
    {
        return $this->belongsTo(DeliveryOffer::class, 'accepted_offer_id');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
}
