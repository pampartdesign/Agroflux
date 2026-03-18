<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IoTGateway extends Model
{
    use BelongsToTenant, HasTenantScope;

    protected $table = 'iot_gateways';

    protected $fillable = [
        'tenant_id',
        'name',
        'identifier',
        'status',
    ];

    public function sensors(): HasMany
    {
        return $this->hasMany(Sensor::class, 'gateway_id');
    }
}
