<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Field extends Model
{
    use BelongsToTenant, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'farm_id',
        'name',
        'area_ha',
        'crop_id',       // FK → global crops library (what's growing in this field)
        'crop_type_id',  // FK → tenant crop_types (optional detailed profile / requirements)
        'crop_type',     // Denormalised string for dashboard breakdown chart
        'status',
        'planted_at',
        'harvest_at',
        'notes',
    ];

    protected $casts = [
        'planted_at' => 'date',
        'harvest_at' => 'date',
        'area_ha'    => 'decimal:2',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    /** Direct link to the global crops library. */
    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class, 'crop_id');
    }

    /** Optional tenant-defined detailed crop profile. */
    public function cropType(): BelongsTo
    {
        return $this->belongsTo(CropType::class);
    }

    /** Resolved crop display name for the UI. */
    public function getCropNameAttribute(): string
    {
        return $this->crop?->name
            ?? $this->cropType?->crop?->name
            ?? $this->cropType?->name
            ?? $this->crop_type
            ?? '—';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'active'    => 'Active / Growing',
            'fallow'    => 'Fallow',
            'harvested' => 'Harvested',
            'prep'      => 'Preparation',
            default     => ucfirst($this->status),
        };
    }
}
