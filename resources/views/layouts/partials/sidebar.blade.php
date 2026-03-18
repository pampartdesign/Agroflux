@php
    $roleService = app(\App\Services\UserRoleService::class);
    $gate = app(\App\Services\FeatureGate::class);
    $tenant = app(\App\Services\CurrentTenant::class)->model();
    $planKey = $tenant ? $gate->effectivePlan($tenant) : 'core';
@endphp

<aside class="w-72 shrink-0 border-r bg-white">
    <div class="px-5 py-5">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-semibold">A</div>
            <div>
                <div class="font-semibold leading-5">{{ __('app.app_name') }}</div>
                <div class="text-xs text-slate-500">{{ __('app.tagline') }}</div>
            </div>
        </div>

        @if($tenant)
            <div class="mt-4 rounded-xl border bg-slate-50 px-3 py-3">
                <div class="text-xs text-slate-500">{{ __('app.organization') }}</div>
                <div class="text-sm font-medium truncate">{{ $tenant->name }}</div>
                <div class="mt-1 text-xs text-slate-500">{{ __('app.plan') }}: <span class="font-medium">{{ strtoupper($planKey) }}</span></div>
            </div>
        @endif
    </div>

    <nav class="px-3 pb-6 text-sm">
        <div class="mb-2 px-2 text-xs font-semibold text-slate-400 uppercase">{{ __('app.nav_main') }}</div>
        <a href="/dashboard" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_dashboard') }}</a>

        @if($roleService->isAdmin() || $roleService->isFarmer())
            <div class="mt-6 mb-2 px-2 text-xs font-semibold text-slate-400 uppercase">{{ __('app.nav_agroflux_core') }}</div>
            <a href="/core/farms" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_farms') }}</a>
            <a href="/core/products" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_products_catalog') }}</a>
            <a href="/core/orders" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_orders') }}</a>
            <a href="/core/traceability" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_traceability') }}</a>

            <div class="mt-6 mb-2 px-2 text-xs font-semibold text-slate-400 uppercase">{{ __('app.nav_farm_management') }}</div>
            <a href="{{ route('farm.fields.index') }}"
               class="flex items-center gap-2 rounded-lg px-3 py-2 hover:bg-slate-100">
                <span class="text-base leading-none">🗺️</span> {{ __('app.nav_field_management') }}
            </a>
            <a href="{{ route('farm.crop-types.index') }}"
               class="flex items-center gap-2 rounded-lg px-3 py-2 hover:bg-slate-100">
                <span class="text-base leading-none">🌱</span> {{ __('app.nav_crop_management') }}
            </a>
            <a href="{{ route('farm.routine.index') }}"
               class="flex items-center gap-2 rounded-lg px-3 py-2 hover:bg-slate-100">
                <span class="text-base leading-none">📋</span> {{ __('app.nav_routine_monitor') }}
            </a>
        @endif

        @if(($roleService->isAdmin() || $roleService->isFarmer()) && $planKey === 'plus')
            <div class="mt-6 mb-2 px-2 text-xs font-semibold text-slate-400 uppercase">{{ __('app.nav_agroflux_plus') }}</div>
            <a href="/plus/iot/dashboard" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_monitoring') }}</a>
            <a href="{{ route('plus.iot.sensors.index') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_sensors') }}</a>
            <a href="{{ route('plus.iot.rules.index') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_sensor_rules') }}</a>
            <a href="/plus/iot/simulator" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_iot_simulator') }}</a>
            <a href="/plus/iot/manual-entry" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_iot_manual_entry') }}</a>
        @endif

        @if(auth()->user()?->isTrucker())
            {{-- ── TRUCKER: Operations ── --}}
            <div class="mt-6 mb-2 px-2 text-xs font-semibold text-slate-400 uppercase">{{ __('app.nav_operations') }}</div>
            <a href="{{ route('logi.available.index') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_available_requests') }}</a>
            <a href="{{ route('logi.offers.mine') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_my_offers') }}</a>

            {{-- ── TRUCKER: Logistics & Delivery ── --}}
            <div class="mt-6 mb-2 px-2 text-xs font-semibold text-slate-400 uppercase">{{ __('app.nav_logistics_delivery') }}</div>
            <a href="{{ route('logi.dashboard') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_dashboard') }}</a>
            <a href="{{ route('logi.route_planning.index') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_route_planning') }}</a>
            <a href="{{ route('logi.shipments.index') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_shipments') }}</a>
            <a href="{{ route('logi.trucker.profile.edit') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_trucker_profile') }}</a>
        @else
            {{-- ── FARMER / ADMIN: LogiTrace ── --}}
            <div class="mt-6 mb-2 px-2 text-xs font-semibold text-slate-400 uppercase">{{ __('app.nav_logitracer') }}</div>
            <a href="{{ route('logi.dashboard') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_dashboard') }}</a>
            @if($roleService->isAdmin() || !$roleService->isTrucker())
                <a href="{{ route('logi.requests.index') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_my_delivery_requests') }}</a>
            @endif
            <a href="{{ route('logi.pickup.index') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_pickup_requests') }}</a>
            <a href="{{ route('logi.shipments.index') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_shipments') }}</a>
            <a href="{{ route('logi.route_planning.index') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_route_planning') }}</a>
        @endif

        @if($roleService->isAdmin())
            <div class="mt-6 mb-2 px-2 text-xs font-semibold text-slate-400 uppercase">{{ __('app.nav_organization') }}</div>
            <a href="/org/members" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.members') }}</a>
        @endif

        @if(auth()->user()?->is_super_admin)
            <div class="mt-6 mb-2 px-2 text-xs font-semibold text-slate-400 uppercase">{{ __('app.nav_super_admin') }}</div>
            <a href="{{ route('admin.plans.index') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_subscription_plans') }}</a>
            <a href="{{ route('admin.subscriptions.index') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.nav_tenant_subscriptions') }}</a>
            <a href="{{ route('admin.users.index') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.users') }}</a>
        @endif

        <div class="mt-6 mb-2 px-2 text-xs font-semibold text-slate-400 uppercase">{{ __('app.nav_account') }}</div>
        @if (\Illuminate\Support\Facades\Route::has('profile.edit'))
            <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.profile') }}</a>
        @else
            <a href="/profile" class="block rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.profile') }}</a>
        @endif
        <form method="POST" action="{{ route('logout') }}" class="mt-1">
            @csrf
            <button class="w-full text-left rounded-lg px-3 py-2 hover:bg-slate-100">{{ __('app.logout') }}</button>
        </form>

        {{-- Marketplace public link --}}
        <div class="mt-6 pt-4 border-t border-slate-100">
            <a href="{{ route('public.marketplace') }}"
               target="_blank"
               class="flex items-center gap-2 rounded-lg px-3 py-2 text-emerald-700 hover:bg-emerald-50 transition group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-500 group-hover:text-emerald-700 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="text-sm font-medium">{{ __('app.nav_marketplace') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-auto text-slate-400 group-hover:text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
        </div>
    </nav>
</aside>
