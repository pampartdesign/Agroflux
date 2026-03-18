@extends('layouts.app')

@section('content')

{{-- Page header --}}
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Logistics Dashboard</h1>
        <p class="text-sm text-slate-500 mt-1">Overview of pickup requests, active shipments, marketplace activity and trucker offers.</p>
    </div>
    @if($tenant)
        <div class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            {{ $tenant->name }}
        </div>
    @endif
</div>

{{-- ── Sub-feature overview cards ────────────────────────────────────────── --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">

    {{-- Pickup Requests --}}
    <a href="{{ route('logi.pickup.index') }}"
       class="group rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md hover:border-blue-200 transition-all p-5">
        <div class="flex items-start justify-between">
            <div class="h-11 w-11 rounded-xl flex items-center justify-center" style="background:#eff6ff;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-blue-600 bg-blue-50 border border-blue-100 rounded-full px-2 py-0.5">
                {{ $pickupOpen }} open
            </span>
        </div>
        <div class="mt-4">
            <div class="text-3xl font-bold text-slate-900 leading-none">{{ $pickupTotal }}</div>
            <div class="text-sm font-semibold text-slate-700 mt-1">Pickup Requests</div>
            <div class="text-xs text-slate-400 mt-1">{{ $pickupDraft }} draft &middot; {{ $pickupOpen }} awaiting offers</div>
        </div>
        <div class="mt-4 text-xs text-blue-500 group-hover:text-blue-700 font-medium flex items-center gap-1">
            View all <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </div>
    </a>

    {{-- Shipments --}}
    <a href="{{ route('logi.shipments.index') }}"
       class="group rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md hover:border-emerald-200 transition-all p-5">
        <div class="flex items-start justify-between">
            <div class="h-11 w-11 rounded-xl flex items-center justify-center" style="background:#f0fdf4;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-emerald-600 bg-emerald-50 border border-emerald-100 rounded-full px-2 py-0.5">
                {{ $shipmentsActive }} active
            </span>
        </div>
        <div class="mt-4">
            <div class="text-3xl font-bold text-slate-900 leading-none">{{ $shipmentsActive + $shipmentsCompleted }}</div>
            <div class="text-sm font-semibold text-slate-700 mt-1">Shipments</div>
            <div class="text-xs text-slate-400 mt-1">{{ $shipmentsOffered }} offered &middot; {{ $shipmentsCompleted }} completed</div>
        </div>
        <div class="mt-4 text-xs text-emerald-500 group-hover:text-emerald-700 font-medium flex items-center gap-1">
            View all <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </div>
    </a>

    {{-- Marketplace --}}
    <a href="{{ route('logi.available.index') }}"
       class="group rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md hover:border-violet-200 transition-all p-5">
        <div class="flex items-start justify-between">
            <div class="h-11 w-11 rounded-xl flex items-center justify-center" style="background:#f5f3ff;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-violet-600 bg-violet-50 border border-violet-100 rounded-full px-2 py-0.5">
                live
            </span>
        </div>
        <div class="mt-4">
            <div class="text-3xl font-bold text-slate-900 leading-none">{{ $marketplaceOpen }}</div>
            <div class="text-sm font-semibold text-slate-700 mt-1">Marketplace Listings</div>
            <div class="text-xs text-slate-400 mt-1">Open requests awaiting trucker bids</div>
        </div>
        <div class="mt-4 text-xs text-violet-500 group-hover:text-violet-700 font-medium flex items-center gap-1">
            Browse <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </div>
    </a>

    {{-- Route Planning --}}
    <a href="{{ route('logi.route_planning.index') }}"
       class="group rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md hover:border-amber-200 transition-all p-5">
        <div class="flex items-start justify-between">
            <div class="h-11 w-11 rounded-xl flex items-center justify-center" style="background:#fefce8;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-amber-600 bg-amber-50 border border-amber-100 rounded-full px-2 py-0.5">
                soon
            </span>
        </div>
        <div class="mt-4">
            <div class="text-3xl font-bold text-slate-900 leading-none">—</div>
            <div class="text-sm font-semibold text-slate-700 mt-1">Route Planning</div>
            <div class="text-xs text-slate-400 mt-1">Optimise multi-stop delivery routes</div>
        </div>
        <div class="mt-4 text-xs text-amber-500 group-hover:text-amber-700 font-medium flex items-center gap-1">
            Preview <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </div>
    </a>

</div>

{{-- ── KPI stat bar ───────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm px-5 py-4">
        <div class="text-xs text-slate-500 font-medium uppercase tracking-wide">My Requests</div>
        <div class="text-2xl font-bold text-slate-900 mt-1">{{ $tenantRequestCount }}</div>
        <div class="text-xs text-slate-400 mt-0.5">for this organisation</div>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm px-5 py-4">
        <div class="text-xs text-slate-500 font-medium uppercase tracking-wide">My Offers</div>
        <div class="text-2xl font-bold text-slate-900 mt-1">{{ $myOffersTotal }}</div>
        <div class="text-xs text-slate-400 mt-0.5">{{ $myOffersPending }} pending &middot; {{ $myOffersAccepted }} accepted</div>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm px-5 py-4">
        <div class="text-xs text-slate-500 font-medium uppercase tracking-wide">Active Shipments</div>
        <div class="text-2xl font-bold text-slate-900 mt-1">{{ $shipmentsActive }}</div>
        <div class="text-xs text-slate-400 mt-0.5">accepted &amp; in transit</div>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm px-5 py-4">
        <div class="text-xs text-slate-500 font-medium uppercase tracking-wide">Completed</div>
        <div class="text-2xl font-bold text-slate-900 mt-1">{{ $shipmentsCompleted }}</div>
        <div class="text-xs text-slate-400 mt-0.5">delivered or self-delivered</div>
    </div>
</div>

{{-- ── Recent activity feeds ──────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Latest Open Requests --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                <span class="text-sm font-semibold text-slate-800">Open Pickup Requests</span>
            </div>
            <a href="{{ route('logi.pickup.index') }}" class="text-xs text-slate-500 hover:text-blue-600 transition">View all →</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($recentOpenRequests as $r)
                <a href="{{ route('logi.available.show', $r) }}"
                   class="block px-5 py-3.5 hover:bg-slate-50 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-800">Request #{{ $r->id }}</span>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-100">OPEN</span>
                    </div>
                    <div class="text-xs text-slate-500 mt-0.5 truncate">{{ $r->pickup_address }}</div>
                    @if($r->requested_date)
                        <div class="text-xs text-slate-400 mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline h-3 w-3 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $r->requested_date->format('d M Y') }}
                        </div>
                    @endif
                </a>
            @empty
                <div class="px-5 py-8 text-center">
                    <div class="text-slate-400 text-sm">No open requests at the moment.</div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Recent Shipments --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                <span class="text-sm font-semibold text-slate-800">Recent Shipments</span>
            </div>
            <a href="{{ route('logi.shipments.index') }}" class="text-xs text-slate-500 hover:text-emerald-600 transition">View all →</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($recentShipments as $r)
                <div class="px-5 py-3.5">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-800">Request #{{ $r->id }}</span>
                        @php
                            $statusColor = match($r->status) {
                                'accepted'       => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                'completed'      => 'bg-slate-100 text-slate-600 border-slate-200',
                                'self_delivered' => 'bg-teal-50 text-teal-700 border-teal-100',
                                default          => 'bg-slate-50 text-slate-500 border-slate-100',
                            };
                        @endphp
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full border {{ $statusColor }}">
                            {{ strtoupper(str_replace('_', ' ', $r->status)) }}
                        </span>
                    </div>
                    <div class="text-xs text-slate-500 mt-0.5 truncate">{{ $r->delivery_address }}</div>
                    @if($r->cargo_weight_kg)
                        <div class="text-xs text-slate-400 mt-0.5">{{ number_format((float)$r->cargo_weight_kg, 0) }} kg cargo</div>
                    @endif
                </div>
            @empty
                <div class="px-5 py-8 text-center">
                    <div class="text-slate-400 text-sm">No shipments in progress.</div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- My Recent Offers --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="h-2 w-2 rounded-full bg-violet-500"></div>
                <span class="text-sm font-semibold text-slate-800">My Recent Offers</span>
            </div>
            <a href="{{ route('logi.offers.mine') }}" class="text-xs text-slate-500 hover:text-violet-600 transition">View all →</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($recentMyOffers as $o)
                <div class="px-5 py-3.5">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-800">€{{ number_format((float)$o->price, 2) }}</span>
                        @php
                            $offerColor = match($o->status) {
                                'accepted' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                'rejected' => 'bg-red-50 text-red-600 border-red-100',
                                default    => 'bg-amber-50 text-amber-700 border-amber-100',
                            };
                        @endphp
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full border {{ $offerColor }}">
                            {{ strtoupper($o->status) }}
                        </span>
                    </div>
                    <div class="text-xs text-slate-500 mt-0.5">Request #{{ $o->delivery_request_id }}</div>
                    @if($o->message)
                        <div class="text-xs text-slate-400 mt-0.5 truncate italic">"{{ $o->message }}"</div>
                    @endif
                </div>
            @empty
                <div class="px-5 py-8 text-center">
                    <div class="text-slate-400 text-sm">No offers submitted yet.</div>
                </div>
            @endforelse
        </div>
    </div>

</div>

@endsection
