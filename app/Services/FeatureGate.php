<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Tenant;
use Carbon\Carbon;

/**
 * FeatureGate centralizes plan/module gating and plan limits.
 * IMPORTANT: Unknown/unfinished modules should never brick the app.
 */
class FeatureGate
{
    public const PLAN_CORE  = 'core';
    public const PLAN_PLUS  = 'plus';
    public const PLAN_DRONE = 'agroflux_drone';

    public const MODULE_CORE         = 'core';
    public const MODULE_PLUS         = 'plus';
    public const MODULE_LOGI         = 'logi';
    public const MODULE_ADMIN        = 'admin';
    public const MODULE_MEDIA        = 'media';
    public const MODULE_FARM         = 'farm';
    public const MODULE_LIVESTOCK    = 'livestock';
    public const MODULE_WATER        = 'water';
    public const MODULE_IOT          = 'iot';
    public const MODULE_IOT_SIM      = 'iot_sim';
    public const MODULE_EQUIPMENT    = 'equipment';
    public const MODULE_INVENTORY    = 'inventory';
    public const MODULE_TRACEABILITY = 'traceability';
    public const MODULE_DRONE        = 'drone';

    /** All toggleable module keys (used in admin plan editor) */
    public const ALL_MODULES = [
        self::MODULE_CORE,
        self::MODULE_FARM,
        self::MODULE_LIVESTOCK,
        self::MODULE_WATER,
        self::MODULE_TRACEABILITY,
        self::MODULE_INVENTORY,
        self::MODULE_EQUIPMENT,
        self::MODULE_IOT_SIM,
        self::MODULE_IOT,
        self::MODULE_LOGI,
        self::MODULE_DRONE,
    ];

    /**
     * Modules included in Core plan (fallback when no DB modules defined).
     * Includes IoT Simulator but NOT real IoT sensors (iot) and NOT logi (standalone).
     */
    protected const CORE_MODULES = [
        self::MODULE_CORE, self::MODULE_FARM, self::MODULE_LIVESTOCK,
        self::MODULE_WATER, self::MODULE_TRACEABILITY,
        self::MODULE_INVENTORY, self::MODULE_EQUIPMENT,
        self::MODULE_IOT_SIM,
    ];

    /**
     * Modules included in Plus plan (fallback when no DB modules defined).
     * Includes everything in Core + real IoT sensors. LogiTrace (logi) is standalone.
     */
    protected const PLUS_MODULES = [
        self::MODULE_CORE, self::MODULE_FARM, self::MODULE_LIVESTOCK,
        self::MODULE_WATER, self::MODULE_TRACEABILITY,
        self::MODULE_INVENTORY, self::MODULE_EQUIPMENT,
        self::MODULE_IOT_SIM, self::MODULE_PLUS, self::MODULE_IOT,
    ];

    /**
     * Modules included in Drone plan.
     * Full suite: everything in Plus + LogiTrace + Drone & Field Mapping.
     */
    protected const DRONE_MODULES = [
        self::MODULE_CORE, self::MODULE_FARM, self::MODULE_LIVESTOCK,
        self::MODULE_WATER, self::MODULE_TRACEABILITY,
        self::MODULE_INVENTORY, self::MODULE_EQUIPMENT,
        self::MODULE_IOT_SIM, self::MODULE_PLUS, self::MODULE_IOT,
        self::MODULE_LOGI, self::MODULE_DRONE,
    ];

    /**
     * Trial => PLUS (full access during trial).
     * Falls back to active subscription plan key, then plan_key column, then CORE.
     */
    public function effectivePlanKey(?Tenant $tenant): string
    {
        if (!$tenant) {
            return self::PLAN_CORE;
        }

        $trialEndsAt = $tenant->trial_ends_at ?? null;
        if ($trialEndsAt) {
            try {
                $trial = $trialEndsAt instanceof Carbon ? $trialEndsAt : Carbon::parse($trialEndsAt);
                if ($trial->isFuture()) {
                    return self::PLAN_PLUS;
                }
            } catch (\Throwable $e) {
                // ignore parse errors; fall back to plan_key
            }
        }

        $planKey = strtolower((string)($tenant->plan_key ?? ''));

        // If no direct plan_key, resolve from active subscription
        if (!$planKey) {
            try {
                $sub = $tenant->activeSubscription()->with('plan')->first();
                $planKey = strtolower((string)($sub?->plan?->key ?? ''));
            } catch (\Throwable $e) {
                $planKey = '';
            }
        }

        return in_array($planKey, [self::PLAN_CORE, self::PLAN_PLUS, self::PLAN_DRONE], true)
            ? $planKey
            : self::PLAN_CORE;
    }

    public function effectivePlan(?Tenant $tenant): string
    {
        return $this->effectivePlanKey($tenant);
    }

    /**
     * Module access by effective plan.
     * Checks DB plan modules first; falls back to hardcoded arrays.
     * Admin/Media are always allowed.
     */
    public function allowsModule(string $effectivePlan, string $moduleKey): bool
    {
        $plan   = strtolower(trim($effectivePlan));
        $module = strtolower(trim($moduleKey));

        // normalize legacy aliases
        if ($module === 'logitrace') $module = self::MODULE_LOGI;
        if ($module === 'plus')      $module = self::MODULE_IOT;

        // Never brick app shell / admin helpers
        if (in_array($module, [self::MODULE_ADMIN, self::MODULE_MEDIA], true)) {
            return true;
        }

        // Drone plan — full suite: Core + Plus + Logi + Drone
        if ($plan === self::PLAN_DRONE) {
            return in_array($module, self::DRONE_MODULES, true);
        }

        // Check DB plan modules if available
        try {
            $dbPlan = Plan::where('key', $plan)->where('is_active', true)->first();
            if ($dbPlan && !empty($dbPlan->modules)) {
                return in_array($module, $dbPlan->modules, true);
            }
        } catch (\Throwable $e) {
            // DB not available — fall through to hardcoded defaults
        }

        $allowed = $plan === self::PLAN_PLUS ? self::PLUS_MODULES : self::CORE_MODULES;

        return in_array($module, $allowed, true);
    }

    /**
     * ---------- PLAN LIMITS ----------
     * Keep these conservative + predictable.
     * Use a big number for "effectively unlimited" in Plus, not INF.
     */
    protected function limitFor(?Tenant $tenant, string $key): int
    {
        $plan = $this->effectivePlanKey($tenant);

        $core = [
            'farms'          => 5,
            'products'       => 50,
            'listings'       => 50,
            'orders_per_day' => 200,
            'batches'        => 200,
            'sensors'        => 10,
            'gateways'       => 2,
            'delivery_req'   => 30,
            'media_items'    => 200,
        ];

        $plus = [
            'farms'          => 2000,
            'products'       => 20000,
            'listings'       => 20000,
            'orders_per_day' => 200000,
            'batches'        => 200000,
            'sensors'        => 1000,
            'gateways'       => 200,
            'delivery_req'   => 20000,
            'media_items'    => 200000,
        ];

        $map = in_array($plan, [self::PLAN_PLUS, self::PLAN_DRONE], true) ? $plus : $core;

        return (int)($map[$key] ?? 0);
    }

    public function maxFarmsForTenant(?Tenant $tenant): int
    {
        return $this->limitFor($tenant, 'farms');
    }

    public function maxProductsForTenant(?Tenant $tenant): int
    {
        return $this->limitFor($tenant, 'products');
    }

    public function maxListingsForTenant(?Tenant $tenant): int
    {
        return $this->limitFor($tenant, 'listings');
    }

    public function maxBatchesForTenant(?Tenant $tenant): int
    {
        return $this->limitFor($tenant, 'batches');
    }

    public function maxSensorsForTenant(?Tenant $tenant): int
    {
        return $this->limitFor($tenant, 'sensors');
    }

    public function maxGatewaysForTenant(?Tenant $tenant): int
    {
        return $this->limitFor($tenant, 'gateways');
    }

    public function maxDeliveryRequestsForTenant(?Tenant $tenant): int
    {
        return $this->limitFor($tenant, 'delivery_req');
    }

    public function maxMediaItemsForTenant(?Tenant $tenant): int
    {
        return $this->limitFor($tenant, 'media_items');
    }
}
