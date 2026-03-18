<?php

namespace App\Http\Middleware;

use App\Services\CurrentTenant;
use App\Services\FeatureGate;
use Closure;
use Illuminate\Http\Request;

class EnsureModuleAccess
{
    public function handle(Request $request, Closure $next, ?string $moduleKey = null)
    {
        /** @var CurrentTenant $tenantService */
        $tenantService = app(CurrentTenant::class);

        /** @var FeatureGate $gate */
        $gate = app(FeatureGate::class);

        $tenant = $tenantService->model();

        // If tenant not selected yet, let tenant.selected middleware handle redirect.
        if (!$tenant) {
            return $next($request);
        }

        // Try to infer module key from URL/route if not provided.
        $moduleKey = $moduleKey ?: $this->inferModuleKey($request);

        // If still unknown, do NOT block (avoid locking yourself out).
        if (!$moduleKey) {
            return $next($request);
        }

        // Admin routes should be protected by dedicated admin middleware, not plan gating.
        if ($moduleKey === 'admin') {
            return $next($request);
        }

        $effectivePlan = $gate->effectivePlanKey($tenant);

        if (!$gate->allowsModule($effectivePlan, $moduleKey)) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Your plan does not include this module.');
        }

        return $next($request);
    }

    protected function inferModuleKey(Request $request): ?string
    {
        $path = ltrim($request->path(), '/');

        // IoT Simulator paths are available in Core (iot_sim), not Plus-only
        if (str_starts_with($path, 'plus/iot/simulator') || str_starts_with($path, 'plus/iot/ping')) {
            return 'iot_sim';
        }

        // Stable inference by URL prefix
        $prefixMap = [
            'core'        => 'core',
            'plus'        => 'iot',
            'farm'        => 'farm',
            'livestock'   => 'livestock',
            'water'       => 'water',
            'equipment'   => 'equipment',
            'inventory'   => 'inventory',
            'logi'        => 'logi',
            'drone'       => 'drone',
            'admin'       => 'admin',
            'marketplace' => 'marketplace',
        ];

        foreach ($prefixMap as $prefix => $module) {
            if (str_starts_with($path, $prefix)) {
                return $module;
            }
        }

        return null;
    }
}
