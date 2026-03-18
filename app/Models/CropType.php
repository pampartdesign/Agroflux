<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CropType extends Model
{
    use BelongsToTenant, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'crop_id',
        'name',
        'min_soil_moisture_pct',
        'min_daily_water_lt',
        'min_temperature_c',
        'max_temperature_c',
        'min_soil_ph',
        'max_soil_ph',
        'min_sunlight_h',
        'growing_days',
        'notes',
    ];

    protected $casts = [
        'min_soil_moisture_pct' => 'decimal:2',
        'min_daily_water_lt'    => 'decimal:2',
        'min_temperature_c'     => 'decimal:2',
        'max_temperature_c'     => 'decimal:2',
        'min_soil_ph'           => 'decimal:2',
        'max_soil_ph'           => 'decimal:2',
        'min_sunlight_h'        => 'decimal:2',
        'growing_days'          => 'integer',
    ];

    /** The global crop this profile is based on. */
    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class, 'crop_id');
    }

    /** Fields using this crop profile. */
    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }

    /** Resolved display name: global crop name or stored name fallback. */
    public function getDisplayNameAttribute(): string
    {
        return $this->crop?->name ?? $this->name ?? 'Unknown';
    }
}
