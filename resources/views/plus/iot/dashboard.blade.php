@extends('layouts.app')

@section('content')

@php
    // Format a raw sensor value: 1 decimal place, comma as decimal separator (e.g. 86,0 or 22,5)
    $fmt = fn($v) => number_format((float) $v, 1, ',', '.');
@endphp

{{-- Page header --}}
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">IoT Dashboard</h1>
        <p class="text-sm text-slate-500 mt-1">Real-time monitoring overview for all connected sensors.</p>
    </div>
    <a href="{{ route('plus.iot.sensors.create') }}"
       class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm flex-shrink-0">
        + Add Sensor
    </a>
</div>

{{-- ── Top KPI strip ───────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    {{-- Total sensors --}}
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Total Sensors</div>
        <div class="text-3xl font-bold text-slate-900">{{ $sensorCount }}</div>
    </div>
    {{-- Online --}}
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Online</div>
        <div class="text-3xl font-bold text-emerald-600">{{ $onlineCount }}</div>
    </div>
    {{-- Offline --}}
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Offline</div>
        <div class="text-3xl font-bold text-slate-400">{{ $sensorCount - $onlineCount }}</div>
    </div>
    {{-- Active alerts --}}
    <div class="rounded-2xl border {{ $alertCount > 0 ? 'border-red-200 bg-red-50' : 'border-slate-100 bg-white' }} shadow-sm p-5">
        <div class="text-xs font-semibold {{ $alertCount > 0 ? 'text-red-400' : 'text-slate-400' }} uppercase tracking-wide mb-1">Active Alerts</div>
        <div class="text-3xl font-bold {{ $alertCount > 0 ? 'text-red-600' : 'text-slate-900' }}">{{ $alertCount }}</div>
    </div>
</div>

{{-- ── Per-sensor KPI cards ──────────────────────────────────────── --}}
@if($sensors->isEmpty())
    <div class="rounded-2xl border border-slate-100 bg-white p-12 text-center">
        <div class="h-12 w-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center mx-auto mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-slate-700">No sensors configured yet</p>
        <p class="text-xs text-slate-400 mt-1">Add your first sensor to start collecting readings.</p>
        <a href="{{ route('plus.iot.sensors.create') }}"
           class="mt-4 inline-flex items-center gap-2 h-9 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
            + Add Sensor
        </a>
    </div>
@else
    @php $groups = $sensors->groupBy('group_key'); @endphp

    {{-- Groups displayed side-by-side: 1 col on mobile, 2 cols on lg+ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    @foreach($groups as $groupKey => $groupSensors)
    @php $onlineSensors = $groupSensors->where('status', 'online'); @endphp
    @if($onlineSensors->isNotEmpty())
    <div>
        <div class="flex items-center gap-2 mb-3">
            <span class="text-xs font-semibold uppercase tracking-widest text-slate-400">{{ $groupKey }}</span>
            <div class="flex-1 border-t border-slate-100"></div>
        </div>

        {{-- Cards left · Chart right ──────────────────────────────────────── --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; align-items:stretch;">

            {{-- LEFT: ONLINE sensor KPI cards only --}}
            <div style="display:flex; flex-direction:column; gap:8px;">
                @foreach($onlineSensors as $sensor)
                @php
                    $reading  = $sensor->latestReading;
                    $isOnline = $sensor->status === 'online';
                    $ago      = $reading ? $reading->recorded_at->diffForHumans() : null;
                @endphp
                <div class="rounded-2xl bg-white shadow-sm {{ $isOnline ? 'border border-emerald-100' : 'border border-slate-100' }}"
                     style="padding:12px; display:flex; flex-direction:column; gap:6px;">
                    {{-- Name + status --}}
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:4px;">
                        <div class="font-semibold text-slate-900 truncate" style="font-size:0.75rem; line-height:1.3;">{{ $sensor->name }}</div>
                        @if($isOnline)
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 font-medium text-emerald-700 flex-shrink-0" style="font-size:0.6rem; padding:1px 6px;">
                                <span class="rounded-full bg-emerald-500 animate-pulse" style="width:5px;height:5px;display:inline-block;"></span> On
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-50 border border-slate-200 font-medium text-slate-400 flex-shrink-0" style="font-size:0.6rem; padding:1px 6px;">
                                <span class="rounded-full bg-slate-300" style="width:5px;height:5px;display:inline-block;"></span> Off
                            </span>
                        @endif
                    </div>
                    {{-- Value --}}
                    @if($reading)
                        <div class="font-bold text-slate-900 leading-none" style="font-size:1.5rem;">
                            {{ $fmt($reading->value) }}<span class="text-slate-400 font-normal" style="font-size:0.8rem; margin-left:3px;">{{ $sensor->unit }}</span>
                        </div>
                        <div class="text-slate-400" style="font-size:0.65rem;">{{ $ago }}</div>
                    @else
                        <div class="font-bold text-slate-300" style="font-size:1.25rem;">—</div>
                        <div class="text-slate-400" style="font-size:0.65rem;">No reading yet</div>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- RIGHT: 24h trend chart --}}
            @php $gc = $groupCharts[$groupKey] ?? ['hasData' => false, 'datasets' => []]; @endphp
            <div class="rounded-2xl border border-slate-100 bg-white shadow-sm" style="padding:12px; display:flex; flex-direction:column;">
                <div class="text-slate-400 font-semibold uppercase" style="font-size:0.6rem; letter-spacing:0.08em; margin-bottom:8px;">24h Trend</div>
                @if($gc['hasData'])
                    <div style="position:relative; flex:1; min-height:130px;">
                        <canvas id="grpchart_{{ $groupKey }}"></canvas>
                    </div>
                @else
                    <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:16px 0; text-align:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" style="height:28px;width:28px;color:#cbd5e1;margin-bottom:6px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                        </svg>
                        <span class="text-slate-300" style="font-size:0.7rem;">No readings<br>in last 24h</span>
                    </div>
                @endif
            </div>

        </div>
    </div>
    @endif {{-- /onlineSensors --}}
    @endforeach
    </div>{{-- /groups grid --}}

    {{-- ── Recent readings feed ─────────────────────────────────── --}}
    @if($latestReadings->isNotEmpty())
    <div class="mt-2">
        <div class="flex items-center gap-2 mb-3">
            <span class="text-xs font-semibold uppercase tracking-widest text-slate-400">Recent Activity</span>
            <div class="flex-1 border-t border-slate-100"></div>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/60">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Sensor</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Group</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Value</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Recorded</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($latestReadings as $r)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-5 py-3 font-medium text-slate-900">{{ $r->sensor?->name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-slate-100 text-xs font-medium text-slate-500">
                                {{ $r->sensor?->group_key ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 font-mono text-slate-900">
                            {{ $fmt($r->value) }}
                            @if($r->sensor?->unit)
                                <span class="text-slate-400 text-xs">{{ $r->sensor->unit }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-slate-400 text-xs">{{ $r->recorded_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── Chart.js: one mini line chart per sensor group ──────────────── --}}
    @if(collect($groupCharts)->some(fn($g) => $g['hasData']))
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    (function () {
        const groupCharts = @json($groupCharts);

        Object.entries(groupCharts).forEach(([groupKey, chart]) => {
            if (!chart.hasData) return;
            const ctx = document.getElementById('grpchart_' + groupKey);
            if (!ctx) return;

            new Chart(ctx, {
                type: 'line',
                data: { datasets: chart.datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            display: chart.datasets.length > 1,
                            position: 'top',
                            labels: { usePointStyle: true, padding: 6, font: { size: 9 } }
                        },
                        tooltip: {
                            callbacks: {
                                label: c => ` ${c.dataset.label}: ${c.parsed.y}`
                            }
                        }
                    },
                    scales: {
                        x: {
                            type: 'category',
                            ticks: { maxTicksLimit: 5, font: { size: 8 }, maxRotation: 0 },
                            grid: { color: '#f1f5f9' }
                        },
                        y: {
                            ticks: { font: { size: 8 }, maxTicksLimit: 5 },
                            grid: { color: '#f1f5f9' }
                        }
                    }
                }
            });
        });
    })();
    </script>
    @endif
@endif

@endsection
