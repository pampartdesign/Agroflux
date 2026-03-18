@extends('layouts.app')
@section('content')

{{-- ── Header ──────────────────────────────────────────────────────── --}}
<div class="flex items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('farm.management_title') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('farm.management_subtitle') }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('farm.crop-types.index') }}"
           class="inline-flex items-center gap-2 h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm font-medium text-slate-700">
            🌱 {{ __('farm.btn_crops') }}
        </a>
        <a href="{{ route('farm.fields.index') }}"
           class="inline-flex items-center gap-2 h-10 px-4 rounded-xl border border-emerald-200 bg-white hover:bg-emerald-50 transition text-sm font-medium">
            {{ __('farm.btn_fields') }}
        </a>
        <a href="{{ route('core.farms.create') }}"
           class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
            {{ __('farm.btn_add_farm') }}
        </a>
    </div>
</div>

{{-- ── KPI Cards ─────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('farm.kpi_total_farms') }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $totalFarms }}</div>
        <div class="text-xs text-slate-500 mt-1">{{ __('farm.kpi_registered_farms') }}</div>
    </div>
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('farm.kpi_crop_profiles') }}</div>
        <div class="text-3xl font-bold" style="color:#059669;">{{ $totalCropTypes }}</div>
        <div class="text-xs mt-1">
            @if($totalCropTypes === 0)
                <a href="{{ route('farm.crop-types.index') }}" class="text-amber-600 hover:underline font-medium">{{ __('farm.kpi_add_crops_first') }}</a>
            @else
                <a href="{{ route('farm.crop-types.index') }}" class="text-slate-500 hover:underline">{{ __('farm.kpi_manage') }}</a>
            @endif
        </div>
    </div>
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('farm.kpi_total_fields') }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $totalFields }}</div>
        <div class="text-xs text-slate-500 mt-1">{{ $activeFields }} {{ __('farm.kpi_active_growing') }}</div>
    </div>
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('farm.kpi_total_hectares') }}</div>
        <div class="text-3xl font-bold text-slate-900">
            {{ $totalHectares > 0 ? number_format($totalHectares, 1) : '—' }}
        </div>
        <div class="text-xs text-slate-500 mt-1">{{ __('farm.kpi_across_all_fields') }}</div>
    </div>
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('farm.kpi_harvests_this_year') }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $harvestsThisYear }}</div>
        <div class="text-xs text-slate-500 mt-1">
            @if($upcomingHarvests > 0)
                <span style="color:#047857;">{{ $upcomingHarvests }} {{ __('farm.kpi_due_in_30_days') }}</span>
            @elseif($overdueHarvests > 0)
                <span style="color:#dc2626;">{{ $overdueHarvests }} {{ __('farm.kpi_overdue') }}</span>
            @else
                {{ __('farm.kpi_no_upcoming') }}
            @endif
        </div>
    </div>
</div>

{{-- ── Main Grid ─────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Farms list --}}
    <div class="lg:col-span-2">
        <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="font-semibold text-slate-900">{{ __('farm.your_farms') }}</div>
                <a href="{{ route('core.farms.index') }}"
                   class="text-xs hover:underline" style="color:#047857;">{{ __('farm.manage_all') }}</a>
            </div>

            @if($farms->isEmpty())
            <div class="p-10 text-center">
                <div class="text-3xl mb-3">🌾</div>
                <p class="text-sm text-slate-500 mb-4">{{ __('farm.no_farms_yet') }}</p>
                <a href="{{ route('core.farms.create') }}"
                   class="inline-flex items-center gap-2 h-9 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
                    {{ __('farm.btn_add_first_farm') }}
                </a>
            </div>
            @else
            <div class="divide-y divide-slate-50">
                @foreach($farms as $farm)
                @php
                    $location = $farm->city ?? null;
                    $ha = $farm->area_ha;
                @endphp
                <div class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50/60 transition">
                    <div class="h-10 w-10 rounded-2xl flex items-center justify-center flex-shrink-0"
                         style="background:#f0fdf4;border:1px solid #d1fae5;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#059669;">
                            <path d="M3 21h18"/><path d="M5 21V7l8-4 6 4v14"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('core.farms.show', $farm) }}"
                           class="font-semibold text-slate-900 hover:text-emerald-700 transition truncate block">
                            {{ $farm->name }}
                        </a>
                        <div class="flex items-center gap-3 text-xs text-slate-500 mt-0.5">
                            @if($location) <span>📍 {{ $location }}</span> @endif
                            @if($ha) <span>{{ number_format((float)$ha, 1) }} ha</span> @endif
                            <span>{{ $farm->fields_count }} field{{ $farm->fields_count !== 1 ? 's' : '' }}</span>
                        </div>
                    </div>
                    <a href="{{ route('core.farms.show', $farm) }}"
                       class="text-xs rounded-lg px-3 py-1.5 border border-slate-200 hover:bg-emerald-50 transition"
                       style="color:#047857;">
                        {{ __('app.view') }} →
                    </a>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-5">

        {{-- Crop breakdown --}}
        <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="font-semibold text-slate-900">{{ __('farm.crops_in_fields') }}</div>
                <a href="{{ route('farm.crop-types.index') }}" class="text-xs hover:underline" style="color:#047857;">{{ __('farm.kpi_manage') }}</a>
            </div>
            @if($cropBreakdown->isEmpty())
            <p class="text-sm text-slate-400">{{ __('farm.no_crop_data') }}
                @if($totalCropTypes === 0)
                    <a href="{{ route('farm.crop-types.index') }}" class="text-emerald-600 hover:underline">{{ __('farm.add_crop_profiles_first') }}</a>
                @else
                    <a href="{{ route('farm.fields.index') }}" class="text-emerald-600 hover:underline">{{ __('farm.assign_crops_to_fields') }}</a>
                @endif
            </p>
            @else
            <div class="space-y-2">
                @php $maxCount = $cropBreakdown->max(); @endphp
                @foreach($cropBreakdown as $crop => $count)
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="text-slate-700 truncate">{{ $crop }}</span>
                        <span class="text-slate-500 text-xs ml-2">{{ $count }} field{{ $count !== 1 ? 's' : '' }}</span>
                    </div>
                    <div class="h-1.5 rounded-full" style="background:#f1f5f9;">
                        <div class="h-1.5 rounded-full" style="background:#059669;width:{{ $maxCount > 0 ? round(($count/$maxCount)*100) : 0 }}%;"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Quick links --}}
        <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
            <div class="font-semibold text-slate-900 mb-3">{{ __('farm.quick_access') }}</div>
            <div class="space-y-2">
                <a href="{{ route('farm.fields.index') }}"
                   class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 hover:bg-emerald-50 transition text-sm">
                    <span class="text-base">🗺️</span>
                    <div>
                        <div class="font-medium text-slate-800">{{ __('app.nav_field_management') }}</div>
                        <div class="text-xs text-slate-500">{{ $totalFields }} field{{ $totalFields !== 1 ? 's' : '' }} · {{ $activeFields }} {{ __('app.active') }}</div>
                    </div>
                </a>
                <a href="{{ route('farm.crop-types.index') }}"
                   class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 hover:bg-emerald-50 transition text-sm">
                    <span class="text-base">🌱</span>
                    <div>
                        <div class="font-medium text-slate-800">{{ __('app.nav_crop_management') }}</div>
                        <div class="text-xs text-slate-500">{{ $totalCropTypes }} {{ __('farm.kpi_crop_profiles') }}</div>
                    </div>
                </a>
                <a href="{{ route('farm.routine.index') }}"
                   class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 hover:bg-emerald-50 transition text-sm">
                    <span class="text-base">📋</span>
                    <div>
                        <div class="font-medium text-slate-800">{{ __('app.nav_routine_monitor') }}</div>
                        <div class="text-xs text-slate-500">{{ __('farm.routine_monitor_desc') }}</div>
                    </div>
                </a>
                <a href="{{ route('core.farms.create') }}"
                   class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 hover:bg-emerald-50 transition text-sm">
                    <span class="text-base">➕</span>
                    <div>
                        <div class="font-medium text-slate-800">{{ __('farm.btn_add_farm') }}</div>
                        <div class="text-xs text-slate-500">{{ $totalFarms }} {{ __('farm.kpi_registered_farms') }}</div>
                    </div>
                </a>
            </div>
        </div>

    </div>
</div>

@endsection
