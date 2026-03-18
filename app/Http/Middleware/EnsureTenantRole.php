<?php

namespace App\Http\Middleware;

use App\Models\TenantUser;
use App\Services\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantRole
{
    /**
     * Usage: ->middleware('tenant.role:admin') or 'tenant.role:farmer,trucker'
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $tenant = app(CurrentTenant::class)->model();
        $user = $request->user();

        if (!$tenant || !$user) {
            abort(403);
        }

        if ($user->is_super_admin) {
            return $next($request);
        }

        $member = TenantUser::query()
            ->where('tenant_id', $tenant->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$member) {
            abort(403);
        }

        if (count($roles) === 0) {
            return $next($request);
        }

        if (!in_array($member->role, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
