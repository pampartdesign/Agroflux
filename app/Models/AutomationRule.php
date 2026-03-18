<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class AutomationRule extends Model
{
    use BelongsToTenant, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'name',
        'is_active',
        'trigger_group_key',
        'condition_operator',
        'condition_value',
        'action_type',
        'action_payload',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'condition_value' => 'decimal:4',
        'action_payload' => 'array',
    ];
}
