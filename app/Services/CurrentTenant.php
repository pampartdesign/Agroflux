<?php

namespace App\Services;

use App\Models\Tenant;

class CurrentTenant
{
    public function id(): ?int
    {
        $id = session('tenant_id');
        return $id ? (int) $id : null;
    }

    public function model(): ?Tenant
    {
        $id = $this->id();
        if (!$id) return null;

        // IMPORTANT: tenant scoping must NOT block tenant lookup
        return Tenant::query()->withoutGlobalScopes()->find($id);
    }

    public function set(int $tenantId): void
    {
        session(['tenant_id' => $tenantId]);
    }

    public function clear(): void
    {
        session()->forget('tenant_id');
    }
}
