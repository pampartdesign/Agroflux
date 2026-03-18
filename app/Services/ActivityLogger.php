<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Tenant;
use Illuminate\Support\Arr;

class ActivityLogger
{
    public function log(
        Tenant $tenant,
        ?int $userId,
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        array $meta = []
    ): ActivityLog {
        return ActivityLog::create([
            'tenant_id'    => $tenant->id,
            'user_id'      => $userId,
            'action'       => $action,
            'entity_type'  => $entityType,
            'entity_id'    => $entityId,
            'meta'         => Arr::except($meta, ['password', 'token']),
        ]);
    }
}
