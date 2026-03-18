<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Models\TenantMember;
use App\Services\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantMember
{
    public function __construct(protected CurrentTenant $currentTenant) {}

    /**
     * Ensures the authenticated user is a member of the selected tenant.
     * Optionally can require a role: tenant.member:admin
     */
    public function handle(Request $request, Closure $next, ?string $requiredRole = null): Response
    {
        $tenant = $this->currentTenant->model();

        if (!$tenant instanceof Tenant) {
            return redirect()->route('tenant.select');
        }

        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $member = TenantMember::query()
            ->where('tenant_id', $tenant->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$member) {
            abort(403, 'You are not a member of this organization.');
        }

        if ($requiredRole && strtolower((string)$member->role_key) !== strtolower($requiredRole)) {
            abort(403, 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
