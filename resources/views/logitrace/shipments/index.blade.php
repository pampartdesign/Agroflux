@extends('layouts.app')

@section('content')

{{-- Header --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Shipments</h1>
        <p class="text-sm text-slate-500 mt-1">Track all deliveries — offered, active, completed, and self-delivered.</p>
    </div>
    <a href="{{ route('logi.dashboard') }}"
       class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition">
        ← Overview
    </a>
</div>

{{-- KPI cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm px-5 py-4">
        <div class="text-xs text-slate-500 font-medium uppercase tracking-wide">Offered</div>
        <div class="text-2xl font-bold mt-1" style="color:#b45309;">{{ $kpiOffered }}</div>
        <div class="text-xs text-slate-400 mt-0.5">awaiting farmer decision</div>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm px-5 py-4">
        <div class="text-xs text-slate-500 font-medium uppercase tracking-wide">Active</div>
        <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $kpiAccepted }}</div>
        <div class="text-xs text-slate-400 mt-0.5">trucker in transit</div>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm px-5 py-4">
        <div class="text-xs text-slate-500 font-medium uppercase tracking-wide">Completed</div>
        <div class="text-2xl font-bold text-slate-700 mt-1">{{ $kpiCompleted }}</div>
        <div class="text-xs text-slate-400 mt-0.5">delivered by trucker</div>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm px-5 py-4">
        <div class="text-xs text-slate-500 font-medium uppercase tracking-wide">Self-Delivered</div>
        <div class="text-2xl font-bold mt-1" style="color:#0f766e;">{{ $kpiSelfDelivered }}</div>
        <div class="text-xs text-slate-400 mt-0.5">handled internally</div>
    </div>
</div>

{{-- Status filter pills --}}
<div class="mb-4 flex flex-wrap gap-2">
    @foreach(['all' => 'All', 'offered' => 'Offered', 'accepted' => 'Active', 'completed' => 'Completed', 'self_delivered' => 'Self-Delivered', 'cancelled' => 'Cancelled'] as $key => $label)
        <a href="{{ request()->fullUrlWithQuery(['status' => $key]) }}"
           class="px-3 py-1.5 rounded-full text-xs font-medium border transition
               {{ $statusFilter === $key
                   ? 'bg-slate-800 text-white border-slate-800'
                   : 'bg-white text-slate-600 border-slate-200 hover:border-slate-400' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

{{-- Flash --}}
@if(session('status'))
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm">
        {{ session('status') }}
    </div>
@endif

{{-- Table --}}
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[780px]">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide w-16">#</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Route</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Cargo</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Date</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Trucker / Price</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($shipments as $s)
                    @php
                        [$bg, $tc] = match($s->status) {
                            'offered'        => ['#fffbeb', '#b45309'],
                            'accepted'       => ['#f0fdf4', '#047857'],
                            'completed'      => ['#f8fafc', '#334155'],
                            'self_delivered' => ['#f0fdfa', '#0f766e'],
                            'cancelled'      => ['#fef2f2', '#dc2626'],
                            default          => ['#f8fafc', '#475569'],
                        };
                    @endphp
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3.5 text-slate-400 font-mono text-xs">#{{ $s->id }}</td>
                        <td class="px-5 py-3.5">
                            <div class="text-slate-700 font-medium text-xs truncate max-w-[180px]">{{ $s->pickup_address }}</div>
                            @if($s->delivery_address)
                                <div class="flex items-center gap-1 text-slate-400 text-xs truncate max-w-[180px] mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                    </svg>
                                    {{ $s->delivery_address }}
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-slate-500 text-xs">
                            @if($s->cargo_weight_kg)
                                <span class="font-medium text-slate-700">{{ number_format((float)$s->cargo_weight_kg, 0) }} kg</span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                            @if($s->cargo_description)
                                <div class="text-slate-400 truncate max-w-[100px] mt-0.5">{{ $s->cargo_description }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-slate-500 text-xs whitespace-nowrap">
                            {{ $s->requested_date?->format('d M Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            @if($s->acceptedOffer && $s->acceptedOffer->trucker)
                                <div class="text-slate-700 text-xs font-medium">{{ $s->acceptedOffer->trucker->name }}</div>
                                <div class="text-xs mt-0.5" style="color:#059669;">€{{ number_format((float)$s->acceptedOffer->price, 2) }}</div>
                            @elseif($s->status === 'self_delivered')
                                <span class="text-xs font-medium" style="color:#0f766e;">Self</span>
                            @else
                                <span class="text-slate-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                                  style="background:{{ $bg }};color:{{ $tc }};">
                                {{ strtoupper(str_replace('_', ' ', $s->status)) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('logi.requests.show', $s) }}"
                                   class="text-xs font-medium text-blue-600 hover:text-blue-800 transition">View</a>
                                @if($s->status === 'accepted')
                                    <form method="POST" action="{{ route('logi.requests.complete', $s) }}">
                                        @csrf
                                        <button type="submit"
                                                class="text-xs font-medium transition"
                                                style="color:#047857;"
                                                onclick="return confirm('Mark this shipment as completed?')">
                                            Complete ✓
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="mx-auto h-12 w-12 rounded-2xl flex items-center justify-center mb-3" style="background:#eff6ff;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-slate-700">No shipments found</p>
                            <p class="text-xs text-slate-400 mt-1">
                                @if($statusFilter !== 'all')
                                    No <strong>{{ str_replace('_', ' ', $statusFilter) }}</strong> shipments.
                                    <a href="{{ route('logi.shipments.index') }}" class="text-blue-500 hover:underline">View all</a>
                                @else
                                    Shipments appear here once a trucker offer is accepted or a request is self-delivered.
                                @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($shipments->hasPages())
    <div class="mt-4">{{ $shipments->links() }}</div>
@endif

@endsection
