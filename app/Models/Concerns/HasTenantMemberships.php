<?php

namespace App\Models\Concerns;

use App\Models\TenantMember;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasTenantMemberships
{
    public function tenantMemberships(): HasMany
    {
        return $this->hasMany(TenantMember::class, 'user_id');
    }
}
