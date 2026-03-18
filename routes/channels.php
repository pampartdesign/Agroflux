<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('tenant.{tenantId}', function ($user, int $tenantId) {
    return $user->tenants()->where('tenants.id', $tenantId)->exists();
});
