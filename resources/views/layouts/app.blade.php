<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'AgroFlux')) — {{ config('app.name', 'AgroFlux') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-white text-slate-900">
@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();
    $tenant = $tenant ?? app(\App\Services\CurrentTenant::class)->model();
    $gate = app(\App\Services\FeatureGate::class);

    $effectivePlan = $tenant ? $gate->effectivePlanKey($tenant) : 'core';

    // Trial days left display: integer days only
    $trialDaysLeft = null;
    if ($tenant && !empty($tenant->trial_ends_at)) {
        try {
            $ends = \Carbon\Carbon::parse($tenant->trial_ends_at);
            if (now()->lt($ends)) {
                $trialDaysLeft = (int) ceil(now()->diffInSeconds($ends) / 86400);
            }
        } catch (\Throwable $e) {}
    }

    $locale  = app()->getLocale();
    $locales = config('agroflux.locales'); // single source of truth — add langs in config/agroflux.php

    // Resolve current member record (for per-member permission overrides)
    $currentMember = null;
    if ($tenant && $user) {
        $currentMember = \App\Models\TenantMember::query()
            ->where('tenant_id', $tenant->id)
            ->where('user_id', $user->id)
            ->first();
    }

    /**
     * $can('module') → true if:
     *   - user is super admin, OR
     *   - plan allows the module AND (member has no explicit restrictions OR member has this module checked)
     */
    $can = function(string $module) use ($user, $tenant, $gate, $effectivePlan, $currentMember): bool {
        if ($user?->is_super_admin) return true;
        if (!$tenant) return false;
        if (!$gate->allowsModule($effectivePlan, $module)) return false;
        if ($currentMember && $currentMember->permissions !== null) {
            return $currentMember->hasModuleAccess($module);
        }
        return true;
    };
@endphp

<div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-72 flex-shrink-0 bg-white/70 backdrop-blur border-r border-emerald-100 px-5 py-6">
        <div class="flex items-center gap-3 mb-6">
            <img src="/agroflux_logo.webp" alt="AgroFlux" class="h-15 w-auto">
        </div>

        @if($tenant)
            <div class="rounded-2xl bg-emerald-50 border border-emerald-100 p-4 mb-6">
                <div class="text-xs text-slate-500">{{ __('app.organization') }}</div>
                <div class="font-semibold">{{ $tenant->name }}</div>
                <div class="inline-flex items-center gap-2 mt-2 text-xs">
                    <span class="px-2 py-1 rounded-full bg-white border border-emerald-100">
                        {{ __('app.plan') }}: {{ strtoupper($effectivePlan) }}
                    </span>
                </div>
            </div>
        @endif

        {{-- ── Nav helpers ────────────────────────────────────────── --}}
        @php
        /**
         * $navLink(route, label, icon_svg, pattern)
         *   Top-level nav item with icon, consistent active state
         */
        $navLink = function(string $route, string $label, string $icon, string $pattern) {
            $active = request()->routeIs($pattern);
            $base  = 'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors ';
            $style = $active
                ? $base . 'bg-emerald-600 text-white shadow-sm'
                : $base . 'text-slate-600 hover:bg-slate-100 hover:text-slate-900';
            return '<a href="'.route($route).'" class="'.$style.'">'
                . '<span class="flex-shrink-0 h-4 w-4 opacity-80">'.$icon.'</span>'
                . '<span>'.$label.'</span>'
                . '</a>';
        };

        /**
         * $navSub(route, label, pattern)
         *   Sub-item (indented, no icon, lighter)
         */
        $navSub = function(string $route, string $label, string $pattern) {
            $active = request()->routeIs($pattern);
            $base  = 'flex items-center gap-2 pl-9 pr-3 py-1.5 rounded-xl text-sm transition-colors ';
            $style = $active
                ? $base . 'bg-emerald-50 text-emerald-700 font-semibold'
                : $base . 'text-slate-500 hover:bg-slate-100 hover:text-slate-700';
            return '<a href="'.route($route).'" class="'.$style.'">'
                . '<span class="h-1 w-1 rounded-full bg-current opacity-50 flex-shrink-0"></span>'
                . '<span>'.$label.'</span>'
                . '</a>';
        };

        /**
         * $navSection(label)
         *   Section label divider
         */
        $navSection = fn(string $label) =>
            '<div class="px-3 pt-3 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">'.$label.'</div>';

        // SVG icons (inline, all 16×16 viewBox)
        $icons = [
            'dashboard'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
            'farm'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
            'livestock'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 9h.01M15 9h.01M9.5 14a3.5 3.5 0 005 0"/></svg>',
            'water'      => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3C12 3 5 10 5 15a7 7 0 0014 0c0-5-7-12-7-12z"/></svg>',
            'trace'      => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>',
            'iot'        => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>',
            'equipment'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
            'inventory'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>',
            'delivery'   => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>',
            'market'     => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
            'products'   => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>',
            'listings'   => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
            'orders'     => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>',
            'users'      => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
            'regions'    => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/><circle cx="12" cy="12" r="9"/></svg>',
            'languages'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>',
            'categories' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>',
            'sell'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
            'extlink'    => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6M15 3h6v6M10 14L21 3"/></svg>',
            'drone'      => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="6" cy="6" r="2"/><circle cx="18" cy="6" r="2"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="18" r="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 6h8M6 8v8M18 8v8M8 18h8M12 12m-2 0a2 2 0 104 0 2 2 0 00-4 0"/></svg>',
            'plans'      => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
            'tenants'    => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
        ];
        @endphp

        <nav class="space-y-0.5 pb-6">

            {{-- ── Dashboard ──────────────────────────────────────── --}}
            {!! $navLink('dashboard', __('app.nav_dashboard'), $icons['dashboard'], 'dashboard') !!}

            {{-- ── Farm Management ────────────────────────────────── --}}
            @if($can('farm'))
            {!! $navSection(__('app.nav_farm_management')) !!}
            {!! $navLink('farm.dashboard', __('app.nav_farm_overview'), $icons['farm'], 'farm.dashboard') !!}
            {!! $navSub('farm.fields.index', __('app.nav_field_management'), 'farm.fields.*') !!}
            {!! $navSub('farm.crop-types.index', __('app.nav_crop_management'), 'farm.crop-types.*') !!}
            {!! $navSub('farm.routine.index', __('app.nav_routine_monitor'), 'farm.routine.*') !!}
            @endif

            {{-- ── Livestock Management ───────────────────────────── --}}
            @if($can('livestock'))
            {!! $navSection(__('app.nav_livestock')) !!}
            {!! $navLink('livestock.dashboard', __('app.nav_livestock_management'), $icons['livestock'], 'livestock.dashboard') !!}
            {!! $navSub('livestock.stock.index', __('app.nav_stock_management'), 'livestock.stock.*') !!}
            {!! $navSub('livestock.produce.index', __('app.nav_produce_management'), 'livestock.produce.*') !!}
            {!! $navSub('livestock.routine.index', __('app.nav_routine_monitor'), 'livestock.routine.*') !!}
            @endif

            {{-- ── Drones & Mapping ────────────────────────────────── --}}
            @if($can('drone') && \Illuminate\Support\Facades\Route::has('drone.dashboard'))
            {!! $navSection(__('app.nav_drones_mapping')) !!}
            {!! $navLink('drone.dashboard', __('app.nav_drone_management'), $icons['drone'], 'drone.dashboard') !!}
            {!! $navSub('drone.fields.index', __('app.nav_field_maps'), 'drone.fields.*') !!}
            {!! $navSub('drone.drones.index', __('app.nav_drone_configuration'), 'drone.drones.*') !!}
            {!! $navSub('drone.missions.index', __('app.nav_mission_planning'), 'drone.missions.*') !!}
            @endif

            {{-- ── Water Management ───────────────────────────────── --}}
            @if($can('water'))
            {!! $navSection(__('app.nav_water')) !!}
            {!! $navLink('water.dashboard', __('app.nav_water_management'), $icons['water'], 'water.dashboard') !!}
            {!! $navSub('water.resources.index', __('app.nav_water_resources'), 'water.resources.*') !!}
            {!! $navSub('water.weather.index', __('app.nav_weather_report'), 'water.weather.*') !!}
            @endif

            {{-- ── Traceability ────────────────────────────────────── --}}
            @if($can('traceability'))
            {!! $navSection(__('app.nav_quality')) !!}
            {!! $navLink('core.traceability.index', __('app.nav_traceability'), $icons['trace'], 'core.traceability.*') !!}
            @endif

            {{-- ── IoT Simulator (Core + Plus) / Full IoT (Plus only) ── --}}
            @if($can('iot_sim') || $can('iot'))
            {!! $navSection(__('app.nav_iot_automation')) !!}
            @if($can('iot'))
            {!! $navLink('plus.iot.dashboard', __('app.nav_iot_dashboard'), $icons['iot'], 'plus.iot.dashboard') !!}
            {!! $navSub('plus.iot.sensors.index', __('app.nav_iot_configuration'), 'plus.iot.sensors.*') !!}
            @endif
            @if($can('iot_sim'))
            {!! $navSub('plus.iot.simulator', __('app.nav_iot_simulator'), 'plus.iot.simulator') !!}
            @endif
            @endif

            {{-- ── Equipment ───────────────────────────────────────── --}}
            @if($can('equipment'))
            {!! $navSection(__('app.nav_operations')) !!}
            {!! $navLink('equipment.index', __('app.nav_equipment'), $icons['equipment'], 'equipment.*') !!}
            @if($can('inventory'))
            {!! $navLink('inventory.index', __('app.nav_inventory'), $icons['inventory'], 'inventory.*') !!}
            @endif
            @elseif($can('inventory'))
            {!! $navSection(__('app.nav_operations')) !!}
            {!! $navLink('inventory.index', __('app.nav_inventory'), $icons['inventory'], 'inventory.*') !!}
            @endif

            {{-- ── Logistics & Delivery (LogiTrace — standalone module) ── --}}
            @if($user?->isTrucker())
            {{-- Truckers are independent — no tenant required --}}
            {!! $navSection(__('app.nav_operations')) !!}
            {!! $navLink('logi.available.index', __('app.nav_available_requests'), $icons['delivery'], 'logi.available.*') !!}
            {!! $navSub('logi.offers.mine', __('app.nav_my_offers'), 'logi.offers.*') !!}

            {!! $navSection(__('app.nav_logistics_delivery')) !!}
            {!! $navLink('logi.dashboard', __('app.nav_overview'), $icons['delivery'], 'logi.dashboard') !!}
            {!! $navSub('logi.route_planning.index', __('app.nav_route_planning'), 'logi.route_planning.*') !!}
            {!! $navSub('logi.shipments.index', __('app.nav_shipments'), 'logi.shipments.*') !!}
            {!! $navSub('logi.trucker.profile.edit', __('app.nav_trucker_profile'), 'logi.trucker.profile.*') !!}
            @elseif($can('logi'))
            {{-- Farmers / Admins — tenant scoped --}}
            {!! $navSection(__('app.nav_logistics_delivery')) !!}
            {!! $navLink('logi.dashboard', __('app.nav_overview'), $icons['delivery'], 'logi.dashboard') !!}
            {!! $navSub('logi.pickup.index', __('app.nav_pickup_requests'), 'logi.pickup.*') !!}
            {!! $navSub('logi.shipments.index', __('app.nav_shipments'), 'logi.shipments.*') !!}
            {!! $navSub('logi.route_planning.index', __('app.nav_route_planning'), 'logi.route_planning.*') !!}
            @endif

            {{-- ── Marketplace + Catalog & Sales ──────────────────── --}}
            {!! $navSection(__('app.nav_sales')) !!}
            <a href="{{ route('public.marketplace') }}" target="_blank"
               class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors text-slate-600 hover:bg-slate-100 hover:text-slate-900">
                <span class="flex-shrink-0 h-4 w-4 opacity-80">{!! $icons['market'] !!}</span>
                <span>{{ __('app.nav_marketplace') }}</span>
                <span class="ml-auto flex-shrink-0 h-3 w-3 opacity-40">{!! $icons['extlink'] !!}</span>
            </a>

            @if($can('core'))
            {!! $navLink('core.sell.dashboard', __('app.nav_sell_on_marketplace'), $icons['sell'], 'core.sell.*') !!}
            {!! $navSub('core.products.index', __('app.nav_products_catalog'), 'core.products.*') !!}
            {!! $navSub('core.listings.index', __('app.nav_my_listings'), 'core.listings.*') !!}
            {!! $navSub('core.orders.index', __('app.nav_orders_inbox'), 'core.orders.*') !!}
            @endif

            {{-- ── SUPER ADMIN ─────────────────────────────────────── --}}
            @if($user?->is_super_admin)
            <div class="pt-2 border-t border-emerald-100 mt-2"></div>
            {!! $navSection(__('app.nav_super_admin')) !!}
            {!! $navLink('admin.plans.index', __('app.nav_subscription_plans'), $icons['plans'], 'admin.plans.*') !!}
            {!! $navLink('admin.subscriptions.index', __('app.nav_tenant_subscriptions'), $icons['tenants'], 'admin.subscriptions.*') !!}
            {!! $navLink('admin.users.index', __('app.users'), $icons['users'], 'admin.users.*') !!}
            {!! $navLink('admin.categories.index', __('app.nav_categories'), $icons['categories'], 'admin.categories.*') !!}
            {!! $navLink('admin.regions.index', __('app.nav_regions'), $icons['regions'], 'admin.regions.*') !!}
            {!! $navLink('admin.languages.index', __('app.languages'), $icons['languages'], 'admin.languages.*') !!}
            {!! $navLink('admin.authorized-sellers.index', __('app.nav_authorized_sellers'), '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>', 'admin.authorized-sellers.*') !!}
            @endif

        </nav>
    </aside>

    <!-- Main -->
    <main class="flex-1">
        <!-- Top bar -->
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-emerald-100">
            <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
                <div class="text-sm text-slate-500">
                    @if($trialDaysLeft !== null)
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-white border border-emerald-100">
                            {{ __('app.trial') }}: {{ $trialDaysLeft }} {{ __('app.trial_days_left') }}
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <!-- Authorized Sellers -->
                    <a href="{{ route('authorized-sellers.index') }}"
                       class="hidden sm:inline-flex items-center gap-2 h-9 px-3 rounded-xl border border-emerald-200 bg-white hover:bg-emerald-50 transition text-sm font-medium text-slate-700">
                        <span class="font-semibold" style="color:#059669;">€</span>
                        <span>{{ __('app.nav_authorized_sellers') }}</span>
                    </a>

                    <!-- My Sales Dashboard -->
                    @if($can('core'))
                    <a href="{{ route('core.sell.dashboard') }}"
                       class="hidden sm:inline-flex items-center gap-2 h-9 px-3 rounded-xl border border-emerald-200 bg-white hover:bg-emerald-50 transition text-sm font-medium text-slate-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>{{ __('app.nav_my_sales') }}</span>
                    </a>
                    @endif

                    <!-- Notifications -->
                    @php $unreadCount = $user ? $user->unreadNotifications()->count() : 0; @endphp
                    <div class="relative">
                        <button id="notifBtn"
                                class="relative inline-flex items-center justify-center h-9 w-9 rounded-xl border border-emerald-200 bg-white hover:bg-emerald-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 01-6 0" />
                            </svg>
                            @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 flex items-center justify-center h-4 min-w-4 px-1 rounded-full text-white text-[10px] font-bold leading-none"
                                      style="background:#dc2626;">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                            @endif
                        </button>

                        <div id="notifMenu"
                             class="hidden absolute right-0 mt-2 w-80 rounded-2xl bg-white border border-emerald-100 shadow-lg overflow-hidden z-50">
                            <div class="px-4 py-3 border-b border-emerald-100 flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-slate-900">{{ __('app.notifications') }}</div>
                                    @if($unreadCount > 0)
                                        <div class="text-xs text-slate-500">{{ $unreadCount }} {{ __('app.unread') }}</div>
                                    @else
                                        <div class="text-xs text-slate-500">{{ __('app.all_caught_up') }}</div>
                                    @endif
                                </div>
                                @if($unreadCount > 0)
                                <form method="POST" action="{{ route('notifications.read_all') }}" class="shrink-0">
                                    @csrf
                                    <button type="submit" class="text-xs font-medium hover:underline" style="color:#047857;">
                                        {{ __('app.mark_all_read') }}
                                    </button>
                                </form>
                                @endif
                            </div>

                            <div class="max-h-80 overflow-auto divide-y divide-slate-50">
                                @if($user && $user->notifications()->count() > 0)
                                    @foreach($user->notifications()->latest()->limit(6)->get() as $n)
                                    @php $isUnread = is_null($n->read_at); @endphp
                                    <div class="px-4 py-3 {{ $isUnread ? '' : 'opacity-60' }}"
                                         style="{{ $isUnread ? 'background:#f0fdf4;' : '' }}">
                                        <div class="flex items-start gap-2">
                                            @if($isUnread)
                                                <div class="mt-1.5 h-2 w-2 rounded-full flex-shrink-0" style="background:#059669;"></div>
                                            @else
                                                <div class="mt-1.5 h-2 w-2 rounded-full flex-shrink-0 bg-slate-300"></div>
                                            @endif
                                            <div class="min-w-0">
                                                <div class="text-sm font-medium text-slate-800 truncate">
                                                    {{ data_get($n->data, 'title', __('app.notification_default')) }}
                                                </div>
                                                <div class="text-xs text-slate-500 mt-0.5">
                                                    {{ data_get($n->data, 'message', '') }}
                                                </div>
                                                <div class="text-xs text-slate-400 mt-1">{{ $n->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="px-4 py-8 text-center">
                                        <div class="text-2xl mb-2">🔔</div>
                                        <div class="text-sm text-slate-500">{{ __('app.no_notifications') }}</div>
                                    </div>
                                @endif
                            </div>

                            <div class="px-4 py-3 border-t border-slate-100 text-center">
                                <a href="{{ route('notifications.index') }}"
                                   class="text-xs font-medium hover:underline" style="color:#047857;">
                                    {{ __('app.view_all_notifications') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Language -->
                    <div class="relative">
                        <button id="langBtn"
                                class="inline-flex items-center gap-2 h-9 px-3 rounded-xl border border-emerald-200 bg-white hover:bg-emerald-50 transition">
                            <span class="text-base">{{ $locales[$locale]['flag'] ?? '🌐' }}</span>
                            <span class="text-sm font-medium">{{ strtoupper($locale) }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div id="langMenu"
                             class="hidden absolute right-0 mt-2 w-64 rounded-2xl bg-white border border-emerald-100 shadow-lg overflow-hidden">
                            <div class="px-4 py-3 border-b border-emerald-100">
                                <div class="font-semibold">{{ __('app.language_label') }}</div>
                                <div class="text-xs text-slate-500">{{ __('app.language_hint') }}</div>
                            </div>

                            <div class="py-2">
                                @foreach($locales as $key => $meta)
                                    <a href="{{ route('locale.switch', ['locale' => $key]) }}"
                                       class="flex items-center justify-between px-4 py-2 hover:bg-emerald-50">
                                        <span class="flex items-center gap-2">
                                            <span class="text-base">{{ $meta['flag'] }}</span>
                                            <span class="text-sm">{{ $meta['label'] }}</span>
                                        </span>
                                        @if($locale === $key)
                                            <span class="text-emerald-600 font-semibold">✓</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Profile -->
                    <a href="{{ route('profile.edit') }}"
                       class="inline-flex items-center gap-2 h-9 px-3 rounded-xl border border-emerald-200 bg-white hover:bg-emerald-50 transition">
                        <!-- user icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5.1 20a7 7 0 0113.8 0M12 12a4 4 0 100-8 4 4 0 000 8z" />
                        </svg>
                        <span>{{ __('app.profile') }}</span>
                    </a>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 h-9 px-3 rounded-xl border border-emerald-200 bg-white hover:bg-emerald-50 transition">
                            <!-- logout icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H9m4-7V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2h6a2 2 0 002-2v-1" />
                        </svg>
                        <span>{{ __('app.logout') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Page -->
        <div class="max-w-6xl mx-auto px-6 py-8">
            @if(session('error'))
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<script>
    (function () {
        const notifBtn = document.getElementById('notifBtn');
        const notifMenu = document.getElementById('notifMenu');
        const langBtn = document.getElementById('langBtn');
        const langMenu = document.getElementById('langMenu');

        function toggle(menu) {
            menu.classList.toggle('hidden');
        }

        function hideAll() {
            notifMenu?.classList.add('hidden');
            langMenu?.classList.add('hidden');
        }

        notifBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            langMenu?.classList.add('hidden');
            toggle(notifMenu);
        });

        langBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            notifMenu?.classList.add('hidden');
            toggle(langMenu);
        });

        document.addEventListener('click', hideAll);
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') hideAll();
        });
    })();
</script>

@stack('scripts')
</body>
</html>
