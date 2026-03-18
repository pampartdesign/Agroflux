<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Listing extends Model
{
    use BelongsToTenant, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'type',
        'price',
        'available_qty',
        'expected_harvest_at',
        'upfront_percent',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'available_qty' => 'decimal:2',
        'expected_harvest_at' => 'datetime',
        'upfront_percent' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
