@extends('layouts.app')

@section('content')
@php
  $isTrucker = $user->isTrucker();
  $isSuperAdmin = $user->is_super_admin;
@endphp

{{-- ═══════════════════════════════════════════════════════════════
     WELCOME HERO
     ═══════════════════════════════════════════════════════════════ --}}
<div class="rounded-2xl mb-6 px-6 py-5 flex items-center gap-5 shadow-sm border border-emerald-100"
     style="background:linear-gradient(135deg,#f0fdf4 0%,#ecfdf5 55%,#f8fafc 100%);">

    {{-- Avatar icon --}}
    <div class="shrink-0 h-16 w-16 rounded-2xl flex items-center justify-center text-3xl shadow-sm border border-emerald-100"
         style="background:#ffffff;">
        @if($isTrucker) 🚛 @elseif($isSuperAdmin) 🛡️ @else 🌾 @endif
    </div>

    {{-- Greeting text --}}
    <div class="flex-1 min-w-0">
        <h1 class="text-2xl font-bold text-slate-800">{{ $greeting }}, {{ $firstName }}!</h1>

        <p class="text-sm text-slate-500 mt-0.5">
            @if($isTrucker)
                {{ __('app.trucker_logged_in') }}
            @elseif($isSuperAdmin)
                {{ __('app.super_admin_viewing') }}
            @elseif($tenant)
                {{ $tenant->name }}
            @else
                {{ __('app.select_org_to_start') }}
            @endif
        </p>

        {{-- Badges row --}}
        <div class="flex flex-wrap items-center gap-2 mt-2">

            {{-- Date --}}
            <span class="inline-flex items-center gap-1 text-xs rounded-full px-2.5 py-0.5 font-medium"
                  style="background:#f1f5f9;color:#475569;">
                📅 {{ now()->format('D, d M Y') }}
            </span>

            @if(!$isTrucker && $tenant)
                {{-- Plan badge --}}
                @if($effectivePlan)
                <span class="inline-flex items-center gap-1 text-xs rounded-full px-2.5 py-0.5 font-medium"
                      style="background:#eff6ff;color:#1d4ed8;">
                    ✦ {{ ucfirst($effectivePlan) }} plan
                </span>
                @endif

                {{-- Trial countdown --}}
                @if($trialEndsAt && \Carbon\Carbon::parse($trialEndsAt)->isFuture())
                @php $daysLeft = (int) now()->diffInDays(\Carbon\Carbon::parse($trialEndsAt), false); @endphp
                <span class="inline-flex items-center gap-1 text-xs rounded-full px-2.5 py-0.5 font-medium"
                      style="background:#fffbeb;color:#b45309;">
                    ⏳ {{ $daysLeft !== 1 ? __('app.trial_badge_plural', ['days' => $daysLeft]) : __('app.trial_badge', ['days' => $daysLeft]) }}
                </span>
                @endif
            @endif

            @if($isTrucker)
                <span class="inline-flex items-center gap-1 text-xs rounded-full px-2.5 py-0.5 font-medium"
                      style="background:#f0fdfa;color:#0f766e;">
                    🚛 {{ __('app.independent_trucker') }}
                </span>
            @endif

            @if($isSuperAdmin)
                <span class="inline-flex items-center gap-1 text-xs rounded-full px-2.5 py-0.5 font-medium"
                      style="background:#faf5ff;color:#7e22ce;">
                    🛡️ {{ __('app.super_admin') }}
                </span>
            @endif
        </div>
    </div>

    {{-- Quick-status links (top-right) --}}
    <div class="shrink-0 hidden sm:flex flex-col items-end gap-2">
        @if($isTrucker)
            <a href="{{ route('logi.pickup.index') }}"
               class="text-xs font-medium rounded-xl px-3 py-1.5 border border-emerald-200 hover:bg-emerald-50 transition"
               style="color:#047857;">
                {{ __('app.browse_requests_link') }}
            </a>
            <a href="{{ route('logi.route_planning.index') }}"
               class="text-xs font-medium rounded-xl px-3 py-1.5 border border-emerald-200 hover:bg-emerald-50 transition"
               style="color:#047857;">
                {{ __('app.my_routes_link') }}
            </a>
        @elseif($tenant)
            <a href="{{ route('logi.requests.create') }}"
               class="text-xs font-medium rounded-xl px-3 py-1.5 border border-emerald-200 hover:bg-emerald-50 transition"
               style="color:#047857;">
                {{ __('app.request_pickup_link') }}
            </a>
            <a href="{{ route('logi.pickup.index') }}"
               class="text-xs font-medium rounded-xl px-3 py-1.5 border border-slate-200 hover:bg-slate-50 transition"
               style="color:#475569;">
                {{ __('app.view_shipments_link') }}
            </a>
        @elseif($isSuperAdmin)
            <a href="{{ route('admin.users.index') }}"
               class="text-xs font-medium rounded-xl px-3 py-1.5 border border-slate-200 hover:bg-slate-50 transition"
               style="color:#475569;">
                {{ __('app.manage_users_link') }}
            </a>
            <a href="{{ route('admin.subscriptions.index') }}"
               class="text-xs font-medium rounded-xl px-3 py-1.5 border border-slate-200 hover:bg-slate-50 transition"
               style="color:#475569;">
                {{ __('app.subscriptions_link') }}
            </a>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     MAIN CONTENT GRID
     ═══════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left / main column --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Quick Actions --}}
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
            <div class="font-semibold mb-4">{{ __('app.quick_actions') }}</div>

            @if($isTrucker)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <a class="rounded-xl border border-slate-200 bg-white hover:bg-emerald-50 px-4 py-4 text-sm font-medium transition"
                   href="{{ route('logi.pickup.index') }}">
                    📦 {{ __('app.browse_requests') }}
                </a>
                <a class="rounded-xl border border-slate-200 bg-white hover:bg-emerald-50 px-4 py-4 text-sm font-medium transition"
                   href="{{ route('logi.route_planning.index') }}">
                    🗺️ {{ __('app.my_routes') }}
                </a>
                <a class="rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-4 py-4 text-sm font-medium transition"
                   href="{{ route('logi.dashboard') }}">
                    🚛 {{ __('app.logitrace') }}
                </a>
            </div>

            @elseif($tenant)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <a class="rounded-xl border border-slate-200 bg-white hover:bg-emerald-50 px-4 py-4 text-sm font-medium transition"
                   href="{{ route('core.farms.create') }}">
                    🌾 {{ __('app.add_farm') }}
                </a>
                <a class="rounded-xl border border-slate-200 bg-white hover:bg-emerald-50 px-4 py-4 text-sm font-medium transition"
                   href="{{ route('core.products.create') }}">
                    🛒 {{ __('app.new_product') }}
                </a>
                <a class="rounded-xl border border-slate-200 bg-white hover:bg-emerald-50 px-4 py-4 text-sm font-medium transition"
                   href="{{ route('core.traceability.batch.create') }}">
                    📋 {{ __('app.create_batch') }}
                </a>
                <a class="rounded-xl border border-slate-200 bg-white hover:bg-emerald-50 px-4 py-4 text-sm font-medium transition"
                   href="{{ route('logi.requests.create') }}">
                    🚚 {{ __('app.request_pickup') }}
                </a>
            </div>

            @elseif($isSuperAdmin)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <a class="rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-4 py-4 text-sm font-medium transition"
                   href="{{ route('admin.users.create') }}">
                    👤 {{ __('app.create_user') }}
                </a>
                <a class="rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-4 py-4 text-sm font-medium transition"
                   href="{{ route('admin.plans.create') }}">
                    ✦ {{ __('app.new_plan') }}
                </a>
                <a class="rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-4 py-4 text-sm font-medium transition"
                   href="{{ route('admin.subscriptions.index') }}">
                    📊 {{ __('app.subscriptions') }}
                </a>
            </div>

            @else
            <p class="text-sm text-slate-500">{{ __('app.select_org_for_actions') }}</p>
            @endif
        </div>

        {{-- Recent Activity --}}
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="font-semibold">{{ __('app.recent_activity') }}</div>
                <a class="text-sm hover:underline" style="color:#047857;" href="{{ route('dashboard') }}">{{ __('app.refresh') }}</a>
            </div>

            @if(!empty($recentActivities) && count($recentActivities))
            <div class="space-y-3">
                @foreach($recentActivities as $a)
                <div class="flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                    <div class="h-9 w-9 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">•</div>
                    <div class="min-w-0">
                        <div class="text-sm font-medium">{{ $a['title'] ?? __('app.activity_default') }}</div>
                        <div class="text-xs text-slate-600">{{ $a['meta'] ?? '' }}</div>
                    </div>
                    <div class="ml-auto text-xs text-slate-500 shrink-0">{{ $a['time'] ?? '' }}</div>
                </div>
                @endforeach
            </div>
            @else
            <div class="rounded-xl border border-dashed border-slate-200 px-5 py-8 text-center">
                <div class="text-2xl mb-2">
                    @if($isTrucker) 🚛 @elseif($tenant) 🌾 @else 🏁 @endif
                </div>
                <p class="text-sm text-slate-500">
                    @if($isTrucker)
                        {{ __('app.trucker_no_activity') }}
                    @elseif($tenant)
                        {{ __('app.tenant_no_activity') }}
                    @else
                        {{ __('app.no_org_no_activity') }}
                    @endif
                </p>
            </div>
            @endif
        </div>

    </div>

    {{-- Right / sidebar column --}}
    <div class="space-y-6">

        {{-- Modules --}}
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
            <div class="font-semibold mb-4">{{ __('app.modules') }}</div>

            @if($isTrucker)
            <div class="space-y-3">
                {{-- Operations --}}
                <a class="block rounded-xl border border-slate-200 hover:bg-emerald-50 px-4 py-4 transition"
                   href="{{ route('logi.available.index') }}">
                    <div class="flex items-center gap-2">
                        <span class="text-base">📦</span>
                        <span class="font-semibold text-sm">{{ __('app.operations') }}</span>
                    </div>
                    <div class="text-xs text-slate-500 mt-1">{{ __('app.operations_desc') }}</div>
                    <div class="flex flex-wrap gap-2 mt-2">
                        <span class="text-xs rounded-full px-2 py-0.5 font-medium" style="background:#f0fdf4;color:#047857;">{{ __('app.available_requests') }}</span>
                        <span class="text-xs rounded-full px-2 py-0.5 font-medium" style="background:#eff6ff;color:#1d4ed8;">{{ __('app.my_offers') }}</span>
                    </div>
                </a>

                {{-- Logistics & Delivery --}}
                <a class="block rounded-xl border border-slate-200 hover:bg-emerald-50 px-4 py-4 transition"
                   href="{{ route('logi.dashboard') }}">
                    <div class="flex items-center gap-2">
                        <span class="text-base">🚛</span>
                        <span class="font-semibold text-sm">{{ __('app.logistics_delivery') }}</span>
                    </div>
                    <div class="text-xs text-slate-500 mt-1">{{ __('app.logistics_delivery_desc') }}</div>
                    <div class="flex flex-wrap gap-2 mt-2">
                        <span class="text-xs rounded-full px-2 py-0.5 font-medium" style="background:#f0fdf4;color:#047857;">{{ __('app.route_planning') }}</span>
                        <span class="text-xs rounded-full px-2 py-0.5 font-medium" style="background:#f0fdfa;color:#0f766e;">{{ __('app.shipments') }}</span>
                    </div>
                </a>

                {{-- Trucker Profile --}}
                <a class="block rounded-xl border border-slate-200 hover:bg-slate-50 px-4 py-4 transition"
                   href="{{ route('logi.trucker.profile.edit') }}">
                    <div class="flex items-center gap-2">
                        <span class="text-base">👤</span>
                        <span class="font-semibold text-sm">{{ __('app.trucker_profile') }}</span>
                    </div>
                    <div class="text-xs text-slate-500 mt-1">{{ __('app.trucker_profile_desc') }}</div>
                </a>
            </div>

            @elseif($tenant)
            <div class="space-y-3">
                <a class="block rounded-xl border border-slate-200 hover:bg-emerald-50 px-4 py-4 transition"
                   href="{{ route('core.farms.index') }}">
                    <div class="font-semibold text-sm">🌾 {{ __('app.agroflux_core') }}</div>
                    <div class="text-xs text-slate-500 mt-1">{{ __('app.agroflux_core_desc') }}</div>
                </a>
                <a class="block rounded-xl border border-slate-200 hover:bg-emerald-50 px-4 py-4 transition"
                   href="{{ route('plus.iot.dashboard') }}">
                    <div class="font-semibold text-sm">📡 {{ __('app.agroflux_plus_iot') }}</div>
                    <div class="text-xs text-slate-500 mt-1">{{ __('app.agroflux_plus_desc') }}</div>
                </a>
                <a class="block rounded-xl border border-slate-200 hover:bg-emerald-50 px-4 py-4 transition"
                   href="{{ route('logi.dashboard') }}">
                    <div class="font-semibold text-sm">🚛 {{ __('app.logitrace') }}</div>
                    <div class="text-xs text-slate-500 mt-1">{{ __('app.logitrace_desc') }}</div>
                </a>
            </div>

            @elseif($isSuperAdmin)
            <div class="space-y-3">
                <a class="block rounded-xl border border-slate-200 hover:bg-slate-50 px-4 py-4 transition"
                   href="{{ route('admin.users.index') }}">
                    <div class="font-semibold text-sm">👤 {{ __('app.users_admin') }}</div>
                    <div class="text-xs text-slate-500 mt-1">{{ __('app.users_admin_desc') }}</div>
                </a>
                <a class="block rounded-xl border border-slate-200 hover:bg-slate-50 px-4 py-4 transition"
                   href="{{ route('admin.plans.index') }}">
                    <div class="font-semibold text-sm">✦ {{ __('app.plans') }}</div>
                    <div class="text-xs text-slate-500 mt-1">{{ __('app.plans_desc') }}</div>
                </a>
                <a class="block rounded-xl border border-slate-200 hover:bg-slate-50 px-4 py-4 transition"
                   href="{{ route('admin.subscriptions.index') }}">
                    <div class="font-semibold text-sm">📊 {{ __('app.subscriptions') }}</div>
                    <div class="text-xs text-slate-500 mt-1">{{ __('app.subscriptions_desc') }}</div>
                </a>
            </div>

            @else
            <p class="text-sm text-slate-500">{{ __('app.select_org_for_modules') }}</p>
            @endif
        </div>

        {{-- Tenant Switch (super admin only) --}}
        @if($isSuperAdmin && !empty($tenants) && count($tenants))
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
            <div class="font-semibold mb-3">
                @if($isSuperAdmin) {{ __('app.switch_organisation') }} @else {{ __('app.my_organisations') }} @endif
            </div>
            <form method="POST" action="{{ route('tenant.switch') }}" class="flex items-center gap-3">
                @csrf
                <select name="tenant_id" class="flex-1 rounded-xl border-slate-200 text-sm">
                    @foreach($tenants as $t)
                        <option value="{{ $t->id }}" @selected(session('tenant_id')==$t->id)>{{ $t->name }}</option>
                    @endforeach
                </select>
                <button class="rounded-xl px-4 py-2 text-white text-sm font-medium hover:bg-emerald-700 transition"
                        style="background:#059669;">
                    {{ __('app.switch') }}
                </button>
            </form>
        </div>
        @endif

    </div>
</div>
@endsection
