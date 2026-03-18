@extends('layouts.app')
@section('content')

{{-- Header --}}
<div class="flex items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('livestock.management_title') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('livestock.management_subtitle') }}</p>
    </div>
    <a href="{{ route('livestock.stock.index') }}"
       class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
        {{ __('livestock.btn_add_animal') }}
    </a>
</div>

{{-- KPI Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('livestock.kpi_total_animals') }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $totalAnimals }}</div>
        <div class="text-xs text-slate-500 mt-1">{{ __('livestock.kpi_registered_stock') }}</div>
    </div>
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('livestock.kpi_pregnant') }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $pregnantCount }}</div>
        <div class="text-xs text-slate-500 mt-1">
            @if($sickCount > 0)
                <span style="color:#dc2626;">{{ __('livestock.kpi_sick', ['count' => $sickCount]) }}</span>
            @else
                {{ __('livestock.kpi_no_sick') }}
            @endif
        </div>
    </div>
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('livestock.kpi_checks_today') }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $checksToday }}</div>
        <div class="text-xs text-slate-500 mt-1">
            @if($alertsThisMonth > 0)
                <span style="color:#dc2626;">{{ __('livestock.kpi_alerts_month', ['count' => $alertsThisMonth]) }}</span>
            @else
                {{ __('livestock.kpi_no_alerts') }}
            @endif
        </div>
    </div>
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('livestock.kpi_produce_today') }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $produceTodayCount > 0 ? $produceTodayCount : '—' }}</div>
        <div class="text-xs text-slate-500 mt-1">{{ __('livestock.kpi_entries_logged') }}</div>
    </div>
</div>

{{-- Main Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Recent checks --}}
    <div class="lg:col-span-2">
        <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="font-semibold text-slate-900">{{ __('livestock.recent_checks') }}</div>
                <a href="{{ route('livestock.routine.index') }}" class="text-xs hover:underline" style="color:#047857;">{{ __('livestock.view_all') }}</a>
            </div>
            @if($recentChecks->isEmpty())
            <div class="p-10 text-center">
                <div class="text-3xl mb-3">🐄</div>
                <p class="text-sm text-slate-500 mb-4">{{ __('livestock.no_checks_yet') }}</p>
                <a href="{{ route('livestock.routine.index') }}"
                   class="inline-flex items-center gap-2 h-9 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
                    {{ __('livestock.btn_log_first_check') }}
                </a>
            </div>
            @else
            <div class="divide-y divide-slate-50">
                @foreach($recentChecks as $check)
                @php
                    $dot = match($check->status) { 'critical' => '#dc2626', 'alert' => '#f59e0b', default => '#059669' };
                @endphp
                <div class="flex items-center gap-4 px-6 py-3">
                    <div class="h-2.5 w-2.5 rounded-full flex-shrink-0" style="background:{{ $dot }};"></div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-slate-800">{{ $check->type }}</div>
                        <div class="text-xs text-slate-400">
                            {{ $check->checked_at->format('d M Y') }}
                            @if($check->animal) · {{ $check->animal->tag }} @endif
                        </div>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full capitalize"
                          style="background:{{ $check->status === 'normal' ? '#f0fdf4' : ($check->status === 'critical' ? '#fef2f2' : '#fffbeb') }};
                                 color:{{ $check->status === 'normal' ? '#166534' : ($check->status === 'critical' ? '#dc2626' : '#b45309') }};
                                 border:1px solid {{ $check->status === 'normal' ? '#bbf7d0' : ($check->status === 'critical' ? '#fecaca' : '#fde68a') }};">
                        {{ ucfirst($check->status) }}
                    </span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-5">
        {{-- Species breakdown --}}
        <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
            <div class="font-semibold text-slate-900 mb-4">{{ __('livestock.species_title') }}</div>
            @if($speciesBreakdown->isEmpty())
            <p class="text-sm text-slate-400">{{ __('livestock.no_animals_yet') }}</p>
            @else
            <div class="space-y-2">
                @php $maxCount = $speciesBreakdown->max(); @endphp
                @foreach($speciesBreakdown as $species => $count)
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="text-slate-700 truncate">{{ $species }}</span>
                        <span class="text-slate-500 text-xs ml-2">{{ $count }}</span>
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
            <div class="font-semibold text-slate-900 mb-3">{{ __('livestock.quick_access') }}</div>
            <div class="space-y-2">
                <a href="{{ route('livestock.stock.index') }}"
                   class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 hover:bg-emerald-50 transition text-sm">
                    <span class="text-base">🐄</span>
                    <div>
                        <div class="font-medium text-slate-800">{{ __('livestock.stock_management_title') }}</div>
                        <div class="text-xs text-slate-500">
                            {{ $totalAnimals }} {{ $totalAnimals !== 1 ? __('livestock.animals_registered_pl') : __('livestock.animals_registered') }}
                        </div>
                    </div>
                </a>
                <a href="{{ route('livestock.produce.index') }}"
                   class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 hover:bg-emerald-50 transition text-sm">
                    <span class="text-base">🥛</span>
                    <div>
                        <div class="font-medium text-slate-800">{{ __('livestock.produce_management_title') }}</div>
                        <div class="text-xs text-slate-500">{{ __('livestock.produce_management_desc') }}</div>
                    </div>
                </a>
                <a href="{{ route('livestock.routine.index') }}"
                   class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 hover:bg-emerald-50 transition text-sm">
                    <span class="text-base">📋</span>
                    <div>
                        <div class="font-medium text-slate-800">{{ __('livestock.routine_monitor_title') }}</div>
                        <div class="text-xs text-slate-500">
                            {{ $checksToday }} {{ $checksToday !== 1 ? __('livestock.checks_today_plural') : __('livestock.checks_today_singular') }}
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

</div>

@endsection
