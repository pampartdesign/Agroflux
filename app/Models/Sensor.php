<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sensor extends Model
{
    use BelongsToTenant, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'gateway_id',
        'group_key',
        'name',
        'identifier',
        'unit',
        'status',
    ];

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(IoTGateway::class, 'gateway_id');
    }

    public function readings(): HasMany
    {
        return $this->hasMany(SensorReading::class);
    }
}
