@extends('layouts.app')

@section('content')

{{-- Header --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Route Planning</h1>
        <p class="text-sm text-slate-500 mt-1">Your confirmed delivery routes, organised by date.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('logi.dashboard') }}"
           class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition">
            ← Overview
        </a>
        <a href="{{ route('logi.available.index') }}"
           class="inline-flex items-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2 text-sm font-medium transition hover:bg-amber-100"
           style="color:#b45309;">
            Browse Requests
        </a>
    </div>
</div>

{{-- KPI cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm px-5 py-4">
        <div class="text-xs text-slate-500 font-medium uppercase tracking-wide">Delivery Days</div>
        <div class="text-2xl font-bold text-slate-900 mt-1">{{ $grouped->count() }}</div>
        <div class="text-xs text-slate-400 mt-0.5">scheduled route days</div>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm px-5 py-4">
        <div class="text-xs text-slate-500 font-medium uppercase tracking-wide">Total Stops</div>
        <div class="text-2xl font-bold mt-1" style="color:#b45309;">{{ $grouped->flatten()->count() }}</div>
        <div class="text-xs text-slate-400 mt-0.5">confirmed deliveries</div>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm px-5 py-4">
        <div class="text-xs text-slate-500 font-medium uppercase tracking-wide">Total Cargo</div>
        <div class="text-2xl font-bold text-emerald-600 mt-1">
            {{ $totalWeight > 0 ? number_format((float)$totalWeight, 0).' kg' : '—' }}
        </div>
        <div class="text-xs text-slate-400 mt-0.5">across all routes</div>
    </div>
</div>

{{-- Flash --}}
@if(session('status'))
    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm">
        {{ session('status') }}
    </div>
@endif

{{-- My Routes (trucker view) --}}
@if($grouped->isEmpty())
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-8 py-16 text-center">
            <div class="mx-auto h-14 w-14 rounded-2xl flex items-center justify-center mb-4" style="background:#fefce8;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </div>
            <h2 class="text-base font-semibold text-slate-800 mb-1">No routes assigned</h2>
            <p class="text-sm text-slate-500 max-w-sm mx-auto">
                Accepted delivery requests will appear here, grouped by date, so you can plan your daily runs.
            </p>
            <a href="{{ route('logi.available.index') }}"
               class="inline-flex items-center gap-2 mt-5 rounded-xl px-5 py-2.5 text-sm font-medium text-white transition"
               style="background:#f59e0b;"
               onmouseover="this.style.background='#d97706'"
               onmouseout="this.style.background='#f59e0b'">
                Browse Available Requests →
            </a>
        </div>
    </div>
@else
    <div class="space-y-6">
        @foreach($grouped as $dateKey => $stops)
            @php
                $isUnscheduled = $dateKey === 'unscheduled';
                $dateLabel = $isUnscheduled
                    ? 'Unscheduled'
                    : \Carbon\Carbon::parse($dateKey)->format('l, d F Y');
                $isToday  = !$isUnscheduled && \Carbon\Carbon::parse($dateKey)->isToday();
                $isPast   = !$isUnscheduled && \Carbon\Carbon::parse($dateKey)->isPast() && !$isToday;
                $dayWeight = $stops->sum('cargo_weight_kg');
            @endphp

            <div class="rounded-2xl overflow-hidden shadow-sm border
                {{ $isToday ? 'border-emerald-300' : 'border-slate-200' }}">

                {{-- Day header --}}
                <div class="px-5 py-4 flex items-center justify-between
                    {{ $isToday ? 'bg-emerald-50 border-b border-emerald-200' : 'bg-slate-50 border-b border-slate-100' }}">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl flex items-center justify-center shrink-0"
                             style="background:{{ $isToday ? '#059669' : ($isPast ? '#94a3b8' : '#475569') }};">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-sm text-slate-900">{{ $dateLabel }}</div>
                            <div class="text-xs text-slate-500">
                                {{ $stops->count() }} {{ Str::plural('stop', $stops->count()) }}
                                @if($dayWeight > 0)
                                    &middot; {{ number_format((float)$dayWeight, 0) }} kg
                                @endif
                            </div>
                        </div>
                        @if($isToday)
                            <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-semibold"
                                  style="background:#dcfce7;color:#15803d;">TODAY</span>
                        @elseif($isPast)
                            <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-semibold"
                                  style="background:#f1f5f9;color:#64748b;">PAST</span>
                        @endif
                    </div>
                </div>

                {{-- Stops --}}
                <div class="bg-white divide-y divide-slate-50">
                    @foreach($stops as $i => $stop)
                        <div class="px-5 py-5 flex flex-col sm:flex-row sm:items-start gap-4">

                            {{-- Stop badge --}}
                            <div class="shrink-0 h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold text-white"
                                 style="background:#64748b;">
                                {{ $i + 1 }}
                            </div>

                            {{-- Route info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    <span class="text-xs text-slate-400 font-mono">Request #{{ $stop->id }}</span>
                                    @if($stop->farm)
                                        <span class="text-xs px-2 py-0.5 rounded-full" style="background:#f1f5f9;color:#475569;">
                                            {{ $stop->farm->name }}
                                        </span>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="rounded-lg p-3" style="background:#f8fafc;">
                                        <div class="text-xs font-semibold uppercase tracking-wide mb-1" style="color:#94a3b8;">
                                            Pickup from
                                        </div>
                                        <div class="text-sm font-medium text-slate-800">{{ $stop->pickup_address }}</div>
                                    </div>
                                    @if($stop->delivery_address)
                                        <div class="rounded-lg p-3" style="background:#f0fdf4;">
                                            <div class="text-xs font-semibold uppercase tracking-wide mb-1" style="color:#86efac;">
                                                Deliver to
                                            </div>
                                            <div class="text-sm font-medium text-slate-800">{{ $stop->delivery_address }}</div>
                                        </div>
                                    @endif
                                </div>

                                @if($stop->cargo_description || $stop->cargo_weight_kg || $stop->acceptedOffer)
                                    <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-slate-500">
                                        @if($stop->cargo_weight_kg)
                                            <span class="flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                                </svg>
                                                {{ number_format((float)$stop->cargo_weight_kg, 0) }} kg
                                            </span>
                                        @endif
                                        @if($stop->cargo_description)
                                            <span class="truncate max-w-[160px]">{{ $stop->cargo_description }}</span>
                                        @endif
                                        @if($stop->acceptedOffer)
                                            <span class="font-semibold" style="color:#059669;">
                                                €{{ number_format((float)$stop->acceptedOffer->price, 2) }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="shrink-0 flex items-center gap-2 sm:self-center">
                                <a href="{{ route('logi.requests.show', $stop) }}"
                                   class="text-xs font-medium text-blue-600 hover:text-blue-800 transition">
                                    View
                                </a>
                                <form method="POST" action="{{ route('logi.requests.trucker_complete', $stop) }}">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                                            style="background:#f0fdf4;color:#047857;"
                                            onmouseover="this.style.background='#dcfce7'"
                                            onmouseout="this.style.background='#f0fdf4'"
                                            onclick="return confirm('Mark this delivery as completed?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Delivered
                                    </button>
                                </form>
                            </div>

                        </div>
                    @endforeach
                </div>

            </div>
        @endforeach
    </div>
@endif

{{-- Farmer's in-transit deliveries (tenant view) --}}
@if($tenantRoutes->isNotEmpty())
    <div class="mt-10">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-base font-semibold text-slate-800">My Organisation's Active Deliveries</h2>
            <a href="{{ route('logi.shipments.index') }}"
               class="text-sm text-blue-500 hover:text-blue-700 transition">View all shipments →</a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[580px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide w-16">#</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Route</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Date</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Trucker</th>
                            <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($tenantRoutes as $tr)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-5 py-3.5 text-slate-400 font-mono text-xs">#{{ $tr->id }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="text-slate-700 font-medium text-xs truncate max-w-[200px]">{{ $tr->pickup_address }}</div>
                                    @if($tr->delivery_address)
                                        <div class="text-slate-400 text-xs truncate max-w-[200px] mt-0.5">→ {{ $tr->delivery_address }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-slate-500 text-xs whitespace-nowrap">
                                    {{ $tr->requested_date?->format('d M Y') ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($tr->acceptedOffer && $tr->acceptedOffer->trucker)
                                        <div class="text-slate-700 text-xs font-medium">{{ $tr->acceptedOffer->trucker->name }}</div>
                                        <div class="text-xs mt-0.5" style="color:#059669;">€{{ number_format((float)$tr->acceptedOffer->price, 2) }}</div>
                                    @else
                                        <span class="text-slate-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('logi.requests.show', $tr) }}"
                                           class="text-xs font-medium text-blue-600 hover:text-blue-800 transition">View</a>
                                        <form method="POST" action="{{ route('logi.requests.complete', $tr) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="text-xs font-medium transition"
                                                    style="color:#047857;"
                                                    onclick="return confirm('Mark as completed?')">
                                                Complete ✓
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

@endsection
