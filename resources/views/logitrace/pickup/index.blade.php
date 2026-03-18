@extends('layouts.app')

@section('content')

{{-- Header --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Pickup Requests</h1>
        <p class="text-sm text-slate-500 mt-1">Create and manage delivery requests — publish to marketplace or self-deliver.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('logi.dashboard') }}"
           class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition">
            ← Overview
        </a>
        <a href="{{ route('logi.requests.create') }}"
           class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-sm font-medium transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            New Request
        </a>
    </div>
</div>

{{-- KPI cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm px-5 py-4">
        <div class="text-xs text-slate-500 font-medium uppercase tracking-wide">Draft</div>
        <div class="text-2xl font-bold text-slate-700 mt-1">{{ $kpiDraft }}</div>
        <div class="text-xs text-slate-400 mt-0.5">not yet published</div>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm px-5 py-4">
        <div class="text-xs text-slate-500 font-medium uppercase tracking-wide">Open</div>
        <div class="text-2xl font-bold text-blue-600 mt-1">{{ $kpiOpen }}</div>
        <div class="text-xs text-slate-400 mt-0.5">awaiting trucker bids</div>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm px-5 py-4">
        <div class="text-xs text-slate-500 font-medium uppercase tracking-wide">Offered</div>
        <div class="text-2xl font-bold mt-1" style="color:#b45309;">{{ $kpiOffered }}</div>
        <div class="text-xs text-slate-400 mt-0.5">has trucker bids</div>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm px-5 py-4">
        <div class="text-xs text-slate-500 font-medium uppercase tracking-wide">Accepted</div>
        <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $kpiAccepted }}</div>
        <div class="text-xs text-slate-400 mt-0.5">trucker assigned</div>
    </div>
</div>

{{-- Status filter pills --}}
<div class="mb-4 flex flex-wrap gap-2">
    @foreach(['all' => 'All', 'draft' => 'Draft', 'open' => 'Open', 'offered' => 'Offered', 'accepted' => 'Accepted', 'completed' => 'Completed', 'self_delivered' => 'Self-Delivered', 'cancelled' => 'Cancelled'] as $key => $label)
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
        <table class="w-full text-sm min-w-[700px]">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide w-16">#</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Farm</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Pickup Address</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Cargo</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Date</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($requests as $r)
                    @php
                        [$bg, $tc] = match($r->status) {
                            'draft'          => ['#f1f5f9', '#475569'],
                            'open'           => ['#eff6ff', '#1d4ed8'],
                            'offered'        => ['#fffbeb', '#b45309'],
                            'accepted'       => ['#f0fdf4', '#047857'],
                            'completed'      => ['#f0f9ff', '#334155'],
                            'self_delivered' => ['#f0fdfa', '#0f766e'],
                            'cancelled'      => ['#fef2f2', '#dc2626'],
                            default          => ['#f8fafc', '#475569'],
                        };
                    @endphp
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3.5 text-slate-400 font-mono text-xs">#{{ $r->id }}</td>
                        <td class="px-5 py-3.5 text-slate-700 font-medium">{{ $r->farm->name ?? ('Farm #'.$r->farm_id) }}</td>
                        <td class="px-5 py-3.5 text-slate-600">
                            <div class="truncate max-w-[200px]">{{ $r->pickup_address }}</div>
                            @if($r->delivery_address)
                                <div class="text-xs text-slate-400 truncate max-w-[200px] mt-0.5">→ {{ $r->delivery_address }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-slate-500 text-xs">
                            @if($r->cargo_weight_kg)
                                <span class="font-medium text-slate-700">{{ number_format((float)$r->cargo_weight_kg, 0) }} kg</span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                            @if($r->cargo_description)
                                <div class="text-slate-400 truncate max-w-[100px] mt-0.5">{{ $r->cargo_description }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-slate-500 text-xs whitespace-nowrap">
                            {{ $r->requested_date?->format('d M Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                                  style="background:{{ $bg }};color:{{ $tc }};">
                                {{ strtoupper(str_replace('_', ' ', $r->status)) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('logi.requests.show', $r) }}"
                               class="text-xs font-medium text-blue-600 hover:text-blue-800 transition">
                                View →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="mx-auto h-12 w-12 rounded-2xl flex items-center justify-center mb-3" style="background:#f0fdf4;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-slate-700">No requests found</p>
                            <p class="text-xs text-slate-400 mt-1">
                                @if($statusFilter !== 'all')
                                    No <strong>{{ str_replace('_', ' ', $statusFilter) }}</strong> requests.
                                    <a href="{{ route('logi.pickup.index') }}" class="text-blue-500 hover:underline">View all</a>
                                @else
                                    Create your first pickup request to get started.
                                @endif
                            </p>
                            @if($statusFilter === 'all')
                                <a href="{{ route('logi.requests.create') }}"
                                   class="inline-flex items-center gap-1.5 mt-4 rounded-xl bg-emerald-600 text-white px-4 py-2 text-sm font-medium hover:bg-emerald-700 transition">
                                    + New Request
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($requests->hasPages())
    <div class="mt-4">{{ $requests->links() }}</div>
@endif

@endsection
