<?php

namespace App\Support;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Collection;

trait NotifiesTenantAdmins
{
    protected function tenantAdmins(Tenant $tenant): Collection
    {
        // Keep it simple: all users attached to tenant who have role Admin or Farmer/Seller.
        // If Spatie roles exist, we filter by role name.
        $q = User::query()
            ->whereHas('tenants', function ($tq) use ($tenant) {
                $tq->where('tenants.id', $tenant->id);
            });

        if (method_exists(User::class, 'role')) {
            // not reliable; ignore
        }

        // If spatie/permission is installed, prefer role check:
        if (method_exists(User::class, 'whereHas')) {
            // We'll check using "roles" relationship if present.
            try {
                return $q->get()->filter(function (User $u) {
                    if (method_exists($u, 'hasAnyRole')) {
                        return $u->hasAnyRole(['Super Admin', 'Admin', 'Farmer/Seller']);
                    }
                    return true;
                })->values();
            } catch (\Throwable $e) {
                return $q->get();
            }
        }

        return $q->get();
    }
}
