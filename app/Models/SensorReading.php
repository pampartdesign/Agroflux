<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorReading extends Model
{
    use BelongsToTenant, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'sensor_id',
        'value',
        'payload',
        'is_manual',
        'recorded_at',
    ];

    protected $casts = [
        'value' => 'decimal:4',
        'payload' => 'array',
        'is_manual' => 'boolean',
        'recorded_at' => 'datetime',
    ];

    public function sensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class);
    }
}
