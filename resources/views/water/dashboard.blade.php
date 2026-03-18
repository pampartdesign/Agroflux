@extends('layouts.app')

@section('content')

@php
/**
 * Moisture level helpers (blade-scoped closures to avoid polluting global namespace)
 */
// Returns inline CSS for the sensor-table reading badge (soft tints)
$moistureClass = function(mixed $val): string {
    if ($val === null) return 'background:#f1f5f9; border-color:#e2e8f0; color:#64748b;';
    $v = (float) $val;
    if ($v < 30) return 'background:#fee2e2; border-color:#fca5a5; color:#991b1b;';
    if ($v < 50) return 'background:#fef3c7; border-color:#fcd34d; color:#92400e;';
    if ($v < 70) return 'background:#d1fae5; border-color:#6ee7b7; color:#065f46;';
    return 'background:#e0f2fe; border-color:#7dd3fc; color:#075985;';
};
$moistureLabel = function(mixed $val): string {
    if ($val === null) return __('water.legend_no_data');
    $v = (float) $val;
    if ($v < 30) return __('water.moisture_dry');
    if ($v < 50) return __('water.moisture_low');
    if ($v < 70) return __('water.moisture_good');
    return __('water.moisture_wet');
};
$moistureIcon = function(mixed $val): string {
    if ($val === null) return '❓';
    $v = (float) $val;
    if ($v < 30) return '🔴';
    if ($v < 50) return '🟡';
    if ($v < 70) return '🟢';
    return '🔵';
};
// Returns inline CSS for heatmap cells (more saturated palette)
$heatmapClass = function(mixed $val): string {
    if ($val === null) return 'background:#f1f5f9; border-color:#cbd5e1; color:#94a3b8;';
    $v = (float) $val;
    if ($v < 30) return 'background:#fca5a5; border-color:#ef4444; color:#450a0a;';  // Dry  — red
    if ($v < 50) return 'background:#fcd34d; border-color:#f59e0b; color:#451a03;';  // Low  — amber
    if ($v < 70) return 'background:#6ee7b7; border-color:#10b981; color:#022c22;';  // Good — emerald
    return             'background:#7dd3fc; border-color:#0ea5e9; color:#082f49;';   // Wet  — sky
};
@endphp

{{-- ── Page header ─────────────────────────────────────────────────────────── --}}
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-slate-900">{{ __('water.dashboard_title') }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ __('water.dashboard_subtitle') }}</p>
</div>

{{-- ── Section A: KPI strip ─────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">{{ __('water.kpi_avg_moisture') }}</div>
        <div class="text-3xl font-bold text-slate-900">
            {{ $avgMoisture !== null ? $avgMoisture . '%' : '—' }}
        </div>
        <div class="text-xs text-slate-400 mt-1">{{ __('water.kpi_avg_moisture_sub') }}</div>
    </div>

    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">{{ __('water.kpi_active_sensors') }}</div>
        <div class="text-3xl font-bold {{ $activeSensors > 0 ? 'text-emerald-600' : 'text-slate-900' }}">
            {{ $activeSensors }}
        </div>
        <div class="text-xs text-slate-400 mt-1">{{ __('water.kpi_active_sensors_sub') }}</div>
    </div>

    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">{{ __('water.kpi_controllers') }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $controllerCount }}</div>
        <div class="text-xs text-slate-400 mt-1">{{ __('water.kpi_controllers_sub') }}</div>
    </div>

    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">{{ __('water.kpi_water_used') }}</div>
        <div class="text-3xl font-bold text-slate-900">
            {{ $waterUsed !== null ? $waterUsed . ' L' : '—' }}
        </div>
        <div class="text-xs text-slate-400 mt-1">{{ __('water.kpi_water_used_sub') }}</div>
    </div>

</div>

{{-- ── Section B: Water Resources + Weather (preserved) ─────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-3">
            <div class="h-8 w-8 rounded-xl bg-sky-50 border border-sky-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.5C3 14.194 7.03 18 12 18s9-3.806 9-8.5S16.97 1 12 1 3 4.806 3 9.5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 22c1.333-.667 4-1.4 4-4 0 2.6 2.667 3.333 4 4" />
                </svg>
            </div>
            <div class="font-semibold text-sm text-slate-900">{{ __('water.panel_water_resources') }}</div>
        </div>
        <p class="text-sm text-slate-500 mb-4">{{ __('water.panel_water_resources_desc') }}</p>
        <a href="{{ route('water.resources.index') }}"
           class="inline-flex h-9 items-center px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
            {{ __('water.btn_manage_resources') }}
        </a>
    </div>

    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-3">
            <div class="h-8 w-8 rounded-xl bg-sky-50 border border-sky-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                </svg>
            </div>
            <div class="font-semibold text-sm text-slate-900">{{ __('water.panel_weather_report') }}</div>
        </div>
        <p class="text-sm text-slate-500 mb-4">{{ __('water.panel_weather_report_desc') }}</p>
        <a href="{{ route('water.weather.index') }}"
           class="inline-flex h-9 items-center px-4 rounded-xl border border-emerald-200 bg-white text-sm font-medium hover:bg-emerald-50 transition">
            {{ __('water.btn_view_weather') }}
        </a>
    </div>
</div>

{{-- ── Sections C + D: Moisture Map & Trend — side by side ──────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- LEFT: Moisture Map — 24h hourly heatmap (6 cols × 4 rows = 24 hours) ──── --}}
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden flex flex-col">

        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
            <div class="h-8 w-8 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </div>
            <div>
                <div class="font-semibold text-sm text-slate-900">{{ __('water.moisture_map_title') }}</div>
                <div class="text-xs text-slate-400">{{ __('water.moisture_map_subtitle') }}</div>
            </div>
        </div>

        <div class="p-5 flex flex-col flex-1">
            @if($moistureSensors->count() > 0)
                {{-- 6 × 4 grid = 24 hourly slots, oldest top-left → newest bottom-right --}}
                <div style="display:grid; grid-template-columns:repeat(6,1fr); gap:6px;">
                    @foreach($heatmap as $slot)
                        @php
                            $val     = $slot['value'];
                            $heatCls = $heatmapClass($val);
                        @endphp
                        <div style="{{ $heatCls }} aspect-ratio:1/1; border-radius:0.75rem; border-width:2px; border-style:solid; display:flex; align-items:center; justify-content:center; cursor:default; transition:opacity .15s;"
                             onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'"
                             title="{{ $slot['label'] }}: {{ $val !== null ? $val.'%' : 'No data' }}">
                            <span style="font-size:0.7rem; font-weight:700; line-height:1; user-select:none;">{{ $val !== null ? $val : '—' }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Legend --}}
                <div class="flex items-center flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500 mt-4 pt-3 border-t border-slate-100">
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded inline-block" style="background:#fca5a5;"></span> {{ __('water.legend_dry') }}</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded inline-block" style="background:#fcd34d;"></span> {{ __('water.legend_low') }}</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded inline-block" style="background:#6ee7b7;"></span> {{ __('water.legend_good') }}</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded inline-block" style="background:#7dd3fc;"></span> {{ __('water.legend_wet') }}</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded inline-block" style="background:#f1f5f9; border:1px solid #cbd5e1;"></span> {{ __('water.legend_no_data') }}</span>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center py-12 text-center">
                    <div class="text-4xl mb-3">💧</div>
                    <p class="text-slate-500 text-sm">{{ __('water.no_moisture_sensors') }}</p>
                    <p class="text-slate-400 text-xs mt-1">
                        {{ __('water.sensor_hint_add_pre') }} <code class="bg-slate-100 px-1 rounded font-mono">humidity</code>
                        {{ __('water.sensor_hint_or') }} <code class="bg-slate-100 px-1 rounded font-mono">trough_level</code>
                        @if(Route::has('plus.iot.sensors.create'))
                            {{ __('water.sensor_hint_via') }} <a href="{{ route('plus.iot.sensors.create') }}" class="underline text-emerald-600">{{ __('water.sensor_hint_manager') }}</a>.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- RIGHT: Moisture Trend (24h line chart) ─────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden flex flex-col">

        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
            <div class="h-8 w-8 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
            </div>
            <div>
                <div class="font-semibold text-sm text-slate-900">{{ __('water.moisture_trends_title') }}</div>
                <div class="text-xs text-slate-400">{{ __('water.moisture_trends_subtitle') }}</div>
            </div>
        </div>

        <div class="p-6 flex-1 flex flex-col justify-center">
            @if($hasChartData)
                <div style="position:relative; height:280px">
                    <canvas id="moistureChart"></canvas>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <div class="text-4xl mb-3">📈</div>
                    <p class="text-slate-500 text-sm">{{ __('water.no_readings') }}</p>
                    <p class="text-slate-400 text-xs mt-1">
                        {{ __('water.use_the') }}
                        @if(Route::has('plus.iot.manual'))
                            <a href="{{ route('plus.iot.manual') }}" class="underline text-emerald-600">{{ __('water.manual_entry') }}</a>
                            {{ __('water.sensor_hint_or') }}
                            <a href="{{ route('plus.iot.simulator') }}" class="underline text-emerald-600">{{ __('water.simulator') }}</a>
                        @else
                            {{ __('water.manual_entry') }} {{ __('water.sensor_hint_or') }} {{ __('water.simulator') }}
                        @endif
                        {{ __('water.to_add_readings') }}
                    </p>
                </div>
            @endif
        </div>
    </div>

</div>

{{-- ── Section E: Sensor data tables ──────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- Moisture Sensors --}}
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3C12 3 6 10 6 14a6 6 0 0012 0c0-4-6-11-6-11z"/>
                    </svg>
                </div>
                <div>
                    <div class="font-semibold text-sm text-slate-900">{{ __('water.moisture_sensors_title') }}</div>
                    <div class="text-xs text-slate-400">{{ __('water.moisture_sensors_sub') }}</div>
                </div>
            </div>
            @if(Route::has('plus.iot.sensors.create'))
                <a href="{{ route('plus.iot.sensors.create') }}"
                   class="text-xs text-emerald-600 hover:underline">{{ __('water.add_link') }}</a>
            @endif
        </div>

        @if($moistureSensors->count() > 0)
            <div class="divide-y divide-slate-50">
                @foreach($moistureSensors as $sensor)
                    <div class="flex items-center justify-between px-5 py-3 hover:bg-slate-50/50 transition">
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-slate-800 truncate">{{ $sensor->name }}</div>
                            <div class="text-xs text-slate-400 font-mono">{{ $sensor->group_key }}</div>
                        </div>
                        <div class="flex items-center gap-3 ml-3 flex-shrink-0">
                            @php $reading = $sensor->latestReading; @endphp
                            @if($reading)
                                <div class="text-right">
                                    <div class="text-sm font-bold px-2 py-0.5 rounded-lg border" style="{{ $moistureClass($reading->value) }}">
                                        {{ number_format((float)$reading->value, 1) }} {{ $sensor->unit ?? '%' }}
                                    </div>
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $reading->recorded_at->diffForHumans() }}</div>
                                </div>
                            @else
                                <span class="text-xs text-slate-400">{{ __('water.no_reading') }}</span>
                            @endif
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $sensor->status === 'online' ? 'bg-emerald-50 border border-emerald-200 text-emerald-700' : 'bg-slate-100 border border-slate-200 text-slate-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $sensor->status === 'online' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ __('water.status_' . $sensor->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-6 py-10 text-center text-sm text-slate-400">
                {{ __('water.no_moisture_sensors_yet') }}
            </div>
        @endif
    </div>

    {{-- Irrigation Controllers --}}
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-xl bg-sky-50 border border-sky-100 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div>
                    <div class="font-semibold text-sm text-slate-900">{{ __('water.irrigation_ctrl_title') }}</div>
                    <div class="text-xs text-slate-400">{{ __('water.irrigation_ctrl_sub') }}</div>
                </div>
            </div>
            @if(Route::has('plus.iot.sensors.create'))
                <a href="{{ route('plus.iot.sensors.create') }}"
                   class="text-xs text-emerald-600 hover:underline">{{ __('water.add_link') }}</a>
            @endif
        </div>

        @if($irrigationSensors->count() > 0)
            <div class="divide-y divide-slate-50">
                @foreach($irrigationSensors as $sensor)
                    <div class="flex items-center justify-between px-5 py-3 hover:bg-slate-50/50 transition">
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-slate-800 truncate">{{ $sensor->name }}</div>
                            <div class="text-xs text-slate-400 font-mono">{{ $sensor->identifier ?? '—' }}</div>
                        </div>
                        <div class="flex items-center gap-3 ml-3 flex-shrink-0">
                            @php $reading = $sensor->latestReading; @endphp
                            @if($reading)
                                <div class="text-right">
                                    <div class="text-sm font-bold text-sky-700 bg-sky-50 border border-sky-200 px-2 py-0.5 rounded-lg">
                                        {{ number_format((float)$reading->value, 1) }} {{ $sensor->unit ?? 'L' }}
                                    </div>
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $reading->recorded_at->diffForHumans() }}</div>
                                </div>
                            @else
                                <span class="text-xs text-slate-400">{{ __('water.no_reading') }}</span>
                            @endif
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $sensor->status === 'online' ? 'bg-emerald-50 border border-emerald-200 text-emerald-700' : 'bg-slate-100 border border-slate-200 text-slate-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $sensor->status === 'online' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ __('water.status_' . $sensor->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-6 py-10 text-center text-sm text-slate-400">
                {{ __('water.no_irrigation_ctrl_yet') }}
            </div>
        @endif
    </div>

</div>

{{-- ── Chart.js (loaded only when there is data) ───────────────────────────── --}}
@if($hasChartData)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    const ctx = document.getElementById('moistureChart');
    if (!ctx) return;

    const chartData = @json($chartData);

    new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'top',
                    labels: { usePointStyle: true, padding: 16, font: { size: 12 } }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y}%`
                    }
                }
            },
            scales: {
                x: {
                    type: 'category',
                    title: { display: true, text: '{{ __('water.chart_x_label') }}', font: { size: 11 } },
                    ticks: { maxTicksLimit: 12, font: { size: 11 } },
                    grid: { color: '#f1f5f9' }
                },
                y: {
                    min: 0,
                    max: 100,
                    title: { display: true, text: '{{ __('water.chart_y_label') }}', font: { size: 11 } },
                    ticks: { callback: v => v + '%', stepSize: 20, font: { size: 11 } },
                    grid: { color: '#f1f5f9' }
                }
            }
        }
    });
})();
</script>
@endif

@endsection
