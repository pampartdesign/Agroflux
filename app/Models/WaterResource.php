<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class WaterResource extends Model
{
    use BelongsToTenant, HasTenantScope;

    protected $table = 'water_resources';

    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'capacity_m3',
        'level_pct',
        'notes',
    ];

    protected $casts = [
        'capacity_m3' => 'float',
        'level_pct'   => 'integer',
    ];
}
