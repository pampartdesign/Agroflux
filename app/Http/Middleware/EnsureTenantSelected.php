<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantSelected
{
    public function __construct(protected CurrentTenant $currentTenant) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->is_super_admin) {
            return $next($request);
        }

        $tenant = $this->currentTenant->model();

        if (!$tenant instanceof Tenant) {
            // Truckers are independent drivers — no farm org required.
            // Send them to their logistics workspace instead of the tenant selector.
            if ($request->user()?->isTrucker()) {
                return redirect()->route('logi.dashboard');
            }

            return redirect()->route('tenant.select');
        }

        return $next($request);
    }
}
