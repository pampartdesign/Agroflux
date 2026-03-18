<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FieldBoundary extends Model
{
    use BelongsToTenant, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'field_id',
        'crop_id',
        'name',
        'geojson',
        'area_ha',
        'centroid_lat',
        'centroid_lng',
        'perimeter_m',
        'notes',
    ];

    protected $casts = [
        'area_ha'      => 'decimal:4',
        'centroid_lat' => 'decimal:7',
        'centroid_lng' => 'decimal:7',
        'perimeter_m'  => 'decimal:2',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function missions(): HasMany
    {
        return $this->hasMany(DroneMission::class);
    }

    /** Decoded GeoJSON as PHP array */
    public function geojsonArray(): ?array
    {
        return $this->geojson ? json_decode($this->geojson, true) : null;
    }
}
