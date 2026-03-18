@extends('layouts.app')
@php use App\Services\TomorrowWeatherService as W; @endphp

@section('content')

{{-- ── Header ──────────────────────────────────────────────────────── --}}
<div class="flex items-start justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
            <a href="{{ route('water.dashboard') }}" class="hover:text-emerald-700 transition">{{ __('water.breadcrumb_water') }}</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-900 font-medium">{{ __('water.breadcrumb_weather') }}</span>
        </div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('water.weather_title') }}</h1>
        <p class="text-sm text-slate-500 mt-1">
            <span class="inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ $locationName }}
            </span>
            @if(!$hasLocation)
                — <a href="{{ route('admin.users.index') }}" class="text-emerald-600 hover:underline">{{ __('water.set_farm_location') }}</a>
            @endif
        </p>
    </div>
    <div class="text-xs text-slate-400 flex-shrink-0 pt-1">
        {{ __('water.powered_by') }} <span class="font-semibold text-slate-500">Tomorrow.io</span><br>
        {{ __('water.updated_every') }}
    </div>
</div>

@if($forecast['error'])
{{-- ── API Error ───────────────────────────────────────────────────── --}}
<div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 flex items-start gap-3 mb-6">
    <div class="text-xl flex-shrink-0">⚠️</div>
    <div>
        <div class="font-semibold text-red-800 text-sm">{{ __('water.weather_error_title') }}</div>
        <p class="text-xs text-red-700 mt-0.5">{{ $forecast['error'] }}</p>
    </div>
</div>
@else
@php
    $cur   = $forecast['current'];
    $daily = $forecast['daily'];
    $hourly= $forecast['hourly'];

    [$curLabel, $curEmoji] = W::weatherLabel($cur['weather_code']);
    [$uvLabel, $uvColor, $uvBg] = W::uvRisk($cur['uv_index']);

    // 7-day total rain
    $totalRain7d = collect($daily)->sum('rain_mm');

    // Today's soil (from first daily)
    $soilMoist = $daily[0]['soil_moisture'] ?? null;
    $soilTemp  = $daily[0]['soil_temp'] ?? null;
    $evapo     = $daily[0]['evapotranspiration'] ?? null;
@endphp

{{-- ── Current Conditions (Hero) ───────────────────────────────────── --}}
<div class="rounded-2xl border border-sky-100 bg-gradient-to-br from-sky-50 to-white shadow-sm p-6 mb-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
        {{-- Main temp --}}
        <div class="flex items-center gap-4 flex-1">
            <div class="text-6xl leading-none">{{ $curEmoji }}</div>
            <div>
                <div class="text-5xl font-bold text-slate-900 leading-none">{{ $cur['temp'] }}°<span class="text-2xl font-medium text-slate-400">C</span></div>
                <div class="text-sm text-slate-500 mt-1">{{ __('water.feels_like') }} {{ $cur['feels_like'] }}°C · <span class="font-medium text-slate-700">{{ $curLabel }}</span></div>
            </div>
        </div>
        {{-- Secondary metrics --}}
        <div class="grid grid-cols-3 sm:grid-cols-3 gap-x-6 gap-y-2 text-sm">
            <div>
                <div class="text-xs text-slate-400 uppercase tracking-wide">{{ __('water.label_humidity') }}</div>
                <div class="font-semibold text-slate-800">{{ $cur['humidity'] }}%</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 uppercase tracking-wide">{{ __('water.label_wind') }}</div>
                <div class="font-semibold text-slate-800">{{ $cur['wind_speed'] }} m/s {{ W::windDir($cur['wind_dir']) }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 uppercase tracking-wide">{{ __('water.label_gusts') }}</div>
                <div class="font-semibold text-slate-800">{{ $cur['wind_gust'] }} m/s</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 uppercase tracking-wide">{{ __('water.label_cloud_cover') }}</div>
                <div class="font-semibold text-slate-800">{{ $cur['cloud_cover'] }}%</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 uppercase tracking-wide">{{ __('water.label_visibility') }}</div>
                <div class="font-semibold text-slate-800">{{ $cur['visibility'] }} km</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 uppercase tracking-wide">{{ __('water.label_pressure') }}</div>
                <div class="font-semibold text-slate-800">{{ $cur['pressure'] }} hPa</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 uppercase tracking-wide">{{ __('water.label_dew_point') }}</div>
                <div class="font-semibold text-slate-800">{{ $cur['dew_point'] }}°C</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 uppercase tracking-wide">{{ __('water.label_rain_chance') }}</div>
                <div class="font-semibold text-slate-800">{{ $cur['precip_prob'] }}%</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 uppercase tracking-wide">{{ __('water.label_uv_index') }}</div>
                <div class="font-semibold {{ $uvColor }}">{{ $cur['uv_index'] }} <span class="text-xs font-normal">({{ $uvLabel }})</span></div>
            </div>
        </div>
    </div>
</div>

{{-- ── KPI Strip ────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('water.kpi_rain_7days') }}</div>
        <div class="text-3xl font-bold text-sky-600">{{ number_format($totalRain7d,1) }} <span class="text-base font-medium text-slate-400">mm</span></div>
    </div>
    <div class="rounded-2xl border {{ $uvBg }} shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('water.kpi_uv_index') }}</div>
        <div class="text-3xl font-bold {{ $uvColor }}">{{ $cur['uv_index'] }}</div>
        <div class="text-xs {{ $uvColor }} mt-0.5">{{ $uvLabel }}</div>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('water.kpi_soil_moisture') }}</div>
        @if($soilMoist !== null)
            <div class="text-3xl font-bold text-amber-600">{{ $soilMoist*100 | 0 }}<span class="text-base font-medium text-slate-400"> %</span></div>
            <div class="text-xs text-slate-400 mt-0.5">{{ __('water.kpi_soil_depth') }}</div>
        @else
            <div class="text-2xl font-bold text-slate-300">—</div>
        @endif
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('water.kpi_evapotranspiration') }}</div>
        @if($evapo !== null)
            <div class="text-3xl font-bold text-emerald-600">{{ $evapo }} <span class="text-base font-medium text-slate-400">mm/d</span></div>
        @else
            <div class="text-2xl font-bold text-slate-300">—</div>
        @endif
    </div>
</div>

{{-- ── 7-Day Forecast ───────────────────────────────────────────────── --}}
<div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-900">{{ __('water.forecast_7day') }}</div>
    <div class="grid grid-cols-7 divide-x divide-slate-50">
        @foreach($daily as $i => $day)
        @php
            [$dayLabel, $dayEmoji] = W::weatherLabel($day['weather_code']);
            $dt = \Carbon\Carbon::parse($day['time']);
        @endphp
        <div class="p-4 text-center {{ $i === 0 ? 'bg-sky-50/60' : '' }}">
            <div class="text-xs font-bold text-slate-500 mb-1">{{ $i === 0 ? __('water.today') : $dt->format('D') }}</div>
            <div class="text-xs text-slate-400 mb-2">{{ $dt->format('M j') }}</div>
            <div class="text-2xl mb-2" title="{{ $dayLabel }}">{{ $dayEmoji }}</div>
            <div class="text-sm font-bold text-slate-900">{{ $day['temp_max'] }}°</div>
            <div class="text-xs text-slate-400">{{ $day['temp_min'] }}°</div>
            <div class="mt-2 text-xs text-sky-600 font-medium">{{ $day['rain_mm'] }} mm</div>
            <div class="text-xs text-slate-400">{{ $day['precip_prob'] }}% 💧</div>
        </div>
        @endforeach
    </div>
</div>

{{-- ── Hourly Forecast (24h) ────────────────────────────────────────── --}}
<div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-900">{{ __('water.hourly_forecast') }}</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[900px]">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="text-left px-4 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('water.col_time') }}</th>
                    <th class="text-center px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('water.col_condition') }}</th>
                    <th class="text-right px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('water.col_temp') }}</th>
                    <th class="text-right px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('water.col_feels') }}</th>
                    <th class="text-right px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('water.col_humidity') }}</th>
                    <th class="text-right px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('water.col_rain_pct') }}</th>
                    <th class="text-right px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('water.col_rain_mm') }}</th>
                    <th class="text-right px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('water.col_wind') }}</th>
                    <th class="text-right px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('water.col_uv') }}</th>
                    <th class="text-right px-4 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('water.col_clouds') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($hourly as $h)
                @php
                    [$hLabel, $hEmoji] = W::weatherLabel($h['weather_code']);
                    $ht = \Carbon\Carbon::parse($h['time']);
                @endphp
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-4 py-2 font-medium text-slate-700 whitespace-nowrap">{{ $ht->format('H:i') }} <span class="text-xs text-slate-400 font-normal">{{ $ht->format('D') }}</span></td>
                    <td class="px-3 py-2 text-center" title="{{ $hLabel }}">{{ $hEmoji }}</td>
                    <td class="px-3 py-2 text-right font-semibold text-slate-900">{{ $h['temp'] }}°C</td>
                    <td class="px-3 py-2 text-right text-slate-500">{{ $h['feels_like'] }}°C</td>
                    <td class="px-3 py-2 text-right text-slate-500">{{ $h['humidity'] }}%</td>
                    <td class="px-3 py-2 text-right {{ $h['precip_prob'] > 60 ? 'text-sky-600 font-semibold' : 'text-slate-500' }}">{{ $h['precip_prob'] }}%</td>
                    <td class="px-3 py-2 text-right {{ $h['precip_mm'] > 0 ? 'text-sky-600 font-semibold' : 'text-slate-400' }}">{{ $h['precip_mm'] > 0 ? $h['precip_mm'].' mm' : '—' }}</td>
                    <td class="px-3 py-2 text-right text-slate-500">{{ $h['wind_speed'] }}</td>
                    <td class="px-3 py-2 text-right">
                        @php [$uLabel,,$uBg] = W::uvRisk($h['uv_index']); @endphp
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium border {{ $uBg }}">{{ $h['uv_index'] }}</span>
                    </td>
                    <td class="px-4 py-2 text-right text-slate-500">{{ $h['cloud_cover'] }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ── Agricultural Data (Soil / Evapotranspiration 7-day) ─────────── --}}
@if(collect($daily)->first()['soil_moisture'] > 0 || collect($daily)->first()['evapotranspiration'] > 0)
<div class="rounded-2xl border border-emerald-100 bg-white shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-emerald-100 font-semibold text-slate-900">
        {{ __('water.agri_data_title') }}
    </div>
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-emerald-50 bg-emerald-50/40">
                <th class="text-left px-5 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('water.col_date') }}</th>
                <th class="text-right px-5 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('water.col_soil_moisture') }}</th>
                <th class="text-right px-5 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('water.col_soil_temp') }}</th>
                <th class="text-right px-5 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('water.col_evapotranspiration') }}</th>
                <th class="text-right px-5 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('water.col_rain_sum') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @foreach($daily as $i => $day)
            @php $dt = \Carbon\Carbon::parse($day['time']); @endphp
            <tr class="hover:bg-slate-50/50 transition {{ $i===0 ? 'font-medium' : '' }}">
                <td class="px-5 py-2 text-slate-700">{{ $i===0 ? __('water.today') : $dt->format('D, M j') }}</td>
                <td class="px-5 py-2 text-right">
                    @if($day['soil_moisture'])
                        @php $sm = round($day['soil_moisture'] * 100); @endphp
                        <span class="{{ $sm < 20 ? 'text-red-500' : ($sm < 40 ? 'text-amber-500' : 'text-emerald-600') }} font-semibold">{{ $sm }}%</span>
                    @else —
                    @endif
                </td>
                <td class="px-5 py-2 text-right text-slate-600">{{ $day['soil_temp'] ? $day['soil_temp'].'°C' : '—' }}</td>
                <td class="px-5 py-2 text-right text-blue-600">{{ $day['evapotranspiration'] ? $day['evapotranspiration'].' mm/d' : '—' }}</td>
                <td class="px-5 py-2 text-right text-sky-600">{{ $day['rain_mm'] > 0 ? $day['rain_mm'].' mm' : '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- ── Sunrise / Sunset 7-day ───────────────────────────────────────── --}}
<div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-900">{{ __('water.sunrise_sunset') }}</div>
    <div class="grid grid-cols-7 divide-x divide-slate-50">
        @foreach($daily as $i => $day)
        @php
            $dt = \Carbon\Carbon::parse($day['time']);
            $rise = $day['sunrise'] ? \Carbon\Carbon::parse($day['sunrise'])->format('H:i') : '—';
            $set  = $day['sunset']  ? \Carbon\Carbon::parse($day['sunset'])->format('H:i')  : '—';
        @endphp
        <div class="p-4 text-center {{ $i===0 ? 'bg-amber-50/40' : '' }}">
            <div class="text-xs font-bold text-slate-500 mb-1">{{ $i===0 ? __('water.today') : $dt->format('D') }}</div>
            <div class="text-lg mb-1">🌅</div>
            <div class="text-xs font-semibold text-amber-600">{{ $rise }}</div>
            <div class="text-lg mt-2 mb-1">🌇</div>
            <div class="text-xs font-semibold text-orange-500">{{ $set }}</div>
        </div>
        @endforeach
    </div>
</div>

@endif {{-- end no-error block --}}

@endsection
