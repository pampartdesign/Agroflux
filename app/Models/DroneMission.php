<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DroneMission extends Model
{
    use BelongsToTenant, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'field_boundary_id',
        'drone_id',
        'name',
        'mission_type',
        'status',
        'altitude_m',
        'speed_ms',
        'spacing_m',
        'angle_deg',
        'overlap_pct',
        'buffer_m',
        'waypoints_geojson',
        'notes',
        'planned_at',
        'completed_at',
    ];

    protected $casts = [
        'altitude_m'   => 'decimal:1',
        'speed_ms'     => 'decimal:1',
        'spacing_m'    => 'decimal:1',
        'angle_deg'    => 'integer',
        'overlap_pct'  => 'integer',
        'buffer_m'     => 'decimal:1',
        'planned_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function boundary(): BelongsTo
    {
        return $this->belongsTo(FieldBoundary::class, 'field_boundary_id');
    }

    public function drone(): BelongsTo
    {
        return $this->belongsTo(Drone::class);
    }

    public function waypoints(): HasMany
    {
        return $this->hasMany(DroneWaypoint::class, 'mission_id')->orderBy('sequence');
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'draft'       => 'slate',
            'planned'     => 'blue',
            'in_progress' => 'amber',
            'completed'   => 'emerald',
            'aborted'     => 'red',
            default       => 'slate',
        };
    }

    public function missionTypeLabel(): string
    {
        $key = 'drone.type_' . $this->mission_type;
        $translated = __($key);
        return $translated !== $key ? $translated : ucwords(str_replace('_', ' ', $this->mission_type));
    }
}
