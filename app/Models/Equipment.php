<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use BelongsToTenant, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'name',
        'category',
        'serial',
        'status',
        'purchased_at',
        'next_service_at',
        'notes',
    ];

    protected $casts = [
        'purchased_at'    => 'date',
        'next_service_at' => 'date',
    ];
}
