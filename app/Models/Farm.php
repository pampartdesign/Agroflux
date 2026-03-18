<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farm extends Model
{
    use BelongsToTenant;
    use HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'region_id',
        'name',
        'area_ha',
        'address_line1',
        'city',
        'postal_code',
        'latitude',
        'longitude',
        'notes',
    ];

    protected $casts = [
        'latitude'  => 'decimal:7',
        'longitude' => 'decimal:7',
        'area_ha'   => 'decimal:2',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }
}
