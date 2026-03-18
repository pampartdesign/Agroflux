<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceDeliveryRequest extends Model
{
    protected $table = 'marketplace_delivery_requests';

    protected $fillable = [
        'listing_id',
        'tenant_id',
        'name',
        'phone',
        'email',
        'address',
        'qty',
        'frequency',
        'start_date',
        'notes',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];

    public const FREQUENCIES = [
        'daily'     => 'Daily',
        'weekly'    => 'Weekly',
        'biweekly'  => 'Every 2 weeks',
        'monthly'   => 'Monthly',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
