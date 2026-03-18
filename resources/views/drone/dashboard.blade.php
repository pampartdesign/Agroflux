@extends('layouts.app')
@section('content')

<div class="flex items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('drone.management_title') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('drone.management_subtitle') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('drone.fields.map') }}"
           class="inline-flex items-center gap-2 h-10 px-4 rounded-xl text-sm font-medium text-white shadow-sm transition"
           style="background:#059669;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            {{ __('drone.btn_map_field') }}
        </a>
        <a href="{{ route('drone.missions.plan') }}"
           class="inline-flex items-center gap-2 h-10 px-4 rounded-xl text-sm font-medium transition border border-slate-200 bg-white hover:bg-slate-50"
           style="color:#047857;">
            {{ __('drone.btn_plan_mission') }}
        </a>
    </div>
</div>

{{-- KPI Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    @php
    $kpis = [
        ['label' => __('drone.kpi_total_drones'),    'value' => $totalDrones,                   'icon' => '🚁', 'sub' => __('drone.kpi_active_sub', ['count' => $activeDrones])],
        ['label' => __('drone.kpi_field_boundaries'),'value' => $totalBoundaries,               'icon' => '🗺️', 'sub' => __('drone.kpi_mapped_areas')],
        ['label' => __('drone.kpi_total_area'),      'value' => number_format($totalAreaHa, 1), 'icon' => '📐', 'sub' => __('drone.kpi_hectares_mapped')],
        ['label' => __('drone.kpi_missions'),        'value' => $totalMissions,                 'icon' => '📋', 'sub' => __('drone.kpi_completed_sub', ['count' => $missionsByStatus['completed'] ?? 0])],
        ['label' => __('drone.kpi_in_progress'),     'value' => $missionsByStatus['in_progress'] ?? 0, 'icon' => '✈️', 'sub' => __('drone.kpi_planned_sub', ['count' => $missionsByStatus['planned'] ?? 0])],
    ];
    @endphp
    @foreach($kpis as $k)
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-4">
        <div class="flex items-center gap-2 mb-2">
            <span class="text-xl">{{ $k['icon'] }}</span>
            <span class="text-xs text-slate-500">{{ $k['label'] }}</span>
        </div>
        <div class="text-2xl font-bold text-slate-900">{{ $k['value'] }}</div>
        <div class="text-xs text-slate-400 mt-0.5">{{ $k['sub'] }}</div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Recent Missions --}}
    <div class="lg:col-span-2 rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-50">
            <h2 class="font-semibold text-slate-900 text-sm">{{ __('drone.recent_missions') }}</h2>
            <a href="{{ route('drone.missions.index') }}" class="text-xs hover:underline" style="color:#059669;">{{ __('drone.view_all') }}</a>
        </div>
        @forelse($recentMissions as $m)
        @php
            $colors = ['draft'=>'slate','planned'=>'blue','in_progress'=>'amber','completed'=>'emerald','aborted'=>'red'];
            $c = $colors[$m->status] ?? 'slate';
            $bgMap  = ['slate'=>'#f8fafc','blue'=>'#eff6ff','amber'=>'#fffbeb','emerald'=>'#f0fdf4','red'=>'#fef2f2'];
            $txtMap = ['slate'=>'#475569','blue'=>'#1d4ed8','amber'=>'#92400e','emerald'=>'#166534','red'=>'#991b1b'];
        @endphp
        <div class="flex items-center gap-4 px-5 py-3 border-b border-slate-50 hover:bg-slate-50/60 transition">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-900 truncate">{{ $m->name }}</p>
                <p class="text-xs text-slate-400 truncate">
                    {{ $m->boundary?->name ?? '—' }} · {{ ucfirst($m->mission_type) }} · {{ $m->altitude_m }}m
                </p>
            </div>
            <span class="flex-shrink-0 text-xs px-2 py-0.5 rounded-full font-medium"
                  style="background:{{ $bgMap[$c] }};color:{{ $txtMap[$c] }};">
                {{ ucwords(str_replace('_', ' ', $m->status)) }}
            </span>
            <a href="{{ route('drone.missions.plan.edit', $m) }}"
               class="flex-shrink-0 text-xs text-slate-400 hover:text-emerald-700 transition">{{ __('drone.edit_action') }}</a>
        </div>
        @empty
        <div class="py-10 text-center text-slate-400 text-sm">{{ __('drone.no_missions_yet') }}</div>
        @endforelse
    </div>

    {{-- Quick links --}}
    <div class="space-y-4">
        <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
            <h2 class="font-semibold text-slate-900 text-sm mb-4">{{ __('drone.quick_actions') }}</h2>
            <div class="space-y-2">
                <a href="{{ route('drone.fields.map') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl border border-slate-100 hover:bg-emerald-50 hover:border-emerald-200 transition text-sm text-slate-700">
                    <span class="text-base">🗺️</span> {{ __('drone.draw_field_boundary') }}
                </a>
                <a href="{{ route('drone.missions.plan') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl border border-slate-100 hover:bg-emerald-50 hover:border-emerald-200 transition text-sm text-slate-700">
                    <span class="text-base">✈️</span> {{ __('drone.plan_new_mission') }}
                </a>
                <a href="{{ route('drone.drones.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl border border-slate-100 hover:bg-emerald-50 hover:border-emerald-200 transition text-sm text-slate-700">
                    <span class="text-base">🚁</span> {{ __('drone.manage_drones') }}
                </a>
                <a href="{{ route('drone.fields.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl border border-slate-100 hover:bg-emerald-50 hover:border-emerald-200 transition text-sm text-slate-700">
                    <span class="text-base">📐</span> {{ __('drone.view_field_maps') }}
                </a>
            </div>
        </div>

        {{-- Mission status breakdown --}}
        @if($totalMissions > 0)
        <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
            <h2 class="font-semibold text-slate-900 text-sm mb-3">{{ __('drone.mission_status') }}</h2>
            @foreach(['draft'=>['drone.status_draft','slate'],'planned'=>['drone.status_planned','blue'],'in_progress'=>['drone.status_in_progress','amber'],'completed'=>['drone.status_completed','emerald'],'aborted'=>['drone.status_aborted','red']] as $s => [$labelKey, $c])
            @if(($missionsByStatus[$s] ?? 0) > 0)
            @php
                $bgMap  = ['slate'=>'#f8fafc','blue'=>'#eff6ff','amber'=>'#fffbeb','emerald'=>'#f0fdf4','red'=>'#fef2f2'];
                $txtMap = ['slate'=>'#475569','blue'=>'#1d4ed8','amber'=>'#92400e','emerald'=>'#166534','red'=>'#991b1b'];
            @endphp
            <div class="flex items-center justify-between py-1.5">
                <span class="text-sm text-slate-600">{{ __($labelKey) }}</span>
                <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                      style="background:{{ $bgMap[$c] }};color:{{ $txtMap[$c] }};">
                    {{ $missionsByStatus[$s] }}
                </span>
            </div>
            @endif
            @endforeach
        </div>
        @endif
    </div>

</div>

@endsection
