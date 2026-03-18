<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class ActivityEvent extends Model
{
    use BelongsToTenant;
    use HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'actor_user_id',
        'type',
        'message',
        'meta',
    ];

    protected $casts = ['meta' => 'array'];
}
