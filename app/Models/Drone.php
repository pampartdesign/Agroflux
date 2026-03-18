<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Drone extends Model
{
    use BelongsToTenant, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'name',
        'model',
        'serial_number',
        'status',
        'default_altitude_m',
        'default_speed_ms',
        'default_overlap_pct',
        'default_spacing_m',
        'default_buffer_m',
        'notes',
        'last_flight_at',
    ];

    protected $casts = [
        'default_altitude_m'  => 'decimal:1',
        'default_speed_ms'    => 'decimal:1',
        'default_overlap_pct' => 'integer',
        'default_spacing_m'   => 'decimal:1',
        'default_buffer_m'    => 'decimal:1',
        'last_flight_at'      => 'datetime',
    ];

    public function missions(): HasMany
    {
        return $this->hasMany(DroneMission::class);
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'active'      => 'Active',
            'maintenance' => 'Maintenance',
            'retired'     => 'Retired',
            default       => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'active'      => 'emerald',
            'maintenance' => 'amber',
            'retired'     => 'slate',
            default       => 'slate',
        };
    }
}
