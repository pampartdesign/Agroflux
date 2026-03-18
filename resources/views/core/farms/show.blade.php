{{-- resources/views/core/farms/show.blade.php --}}
@extends('layouts.app')

@section('content')
@php
    $location = $farm->location ?? $farm->city ?? $farm->region ?? $farm->address ?? null;
    $ha       = $farm->area_ha ?? $farm->hectares ?? $farm->area ?? null;
    $haLabel  = $ha ? rtrim(rtrim(number_format((float)$ha, 2, '.', ''), '0'), '.') . ' ha' : '— ha';
@endphp

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-sm text-slate-500 mb-5">
    <a href="{{ route('core.farms.index') }}" class="hover:text-emerald-700 transition">{{ __('farm.index_title') }}</a>
    <span class="text-slate-300">/</span>
    <span class="text-slate-900 font-medium">{{ $farm->name }}</span>
</nav>

{{-- Page header --}}
<div class="flex items-start justify-between gap-4 mb-6">
    <div class="flex items-center gap-4">
        <div class="h-12 w-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 21h18" />
                <path d="M5 21V7l8-4 6 4v14" />
                <path d="M9 21v-8h6v8" />
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">{{ $farm->name }}</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ __('farm.farm_dashboard_label') }}</p>
        </div>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('core.farms.index') }}"
           class="inline-flex items-center h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm text-slate-600">
            {{ __('farm.all_farms') }}
        </a>
        <a href="{{ route('core.farms.edit', $farm) }}"
           class="inline-flex items-center gap-2 h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-emerald-50 transition text-sm text-slate-700 hover:text-emerald-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            {{ __('farm.edit_farm_btn') }}
        </a>
    </div>
</div>

{{-- Info strip --}}
<div class="rounded-2xl border border-emerald-100 bg-white shadow-sm px-6 py-4 mb-6">
    <div class="flex flex-wrap items-center gap-6 text-sm text-slate-600">
        @if($location)
            <span class="inline-flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10a8 8 0 10-16 0c0 6 8 10 8 10z"/>
                    <path d="M12 11a3 3 0 100-6 3 3 0 000 6z"/>
                </svg>
                {{ $location }}
            </span>
        @endif

        @if($farm->postal_code)
            <span class="inline-flex items-center gap-2 text-slate-500">
                {{ $farm->postal_code }}
            </span>
        @endif

        <span class="inline-flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 3v18h18"/>
                <path d="M7 17V9"/>
                <path d="M12 17V5"/>
                <path d="M17 17v-7"/>
            </svg>
            {{ $haLabel }}
        </span>

        @if($farm->latitude && $farm->longitude)
            <span class="inline-flex items-center gap-2 text-slate-400 text-xs font-mono">
                {{ number_format((float)$farm->latitude, 5) }}, {{ number_format((float)$farm->longitude, 5) }}
            </span>
        @endif
    </div>
</div>

{{-- Stats row --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5">
        <div class="text-xs uppercase tracking-wide text-slate-400">{{ __('farm.fields_label') }}</div>
        <div class="mt-2 text-3xl font-bold text-slate-900">{{ $farm->fields_count ?? 0 }}</div>
        <div class="mt-1 text-xs text-slate-500">{{ __('farm.registered_fields_label') }}</div>
    </div>

    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5">
        <div class="text-xs uppercase tracking-wide text-slate-400">{{ __('farm.livestock_label') }}</div>
        <div class="mt-2 text-3xl font-bold text-slate-900">{{ $farm->livestock_groups_count ?? 0 }}</div>
        <div class="mt-1 text-xs text-slate-500">{{ __('farm.livestock_groups') }}</div>
    </div>

    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5">
        <div class="text-xs uppercase tracking-wide text-slate-400">{{ __('farm.products_label') }}</div>
        <div class="mt-2 text-3xl font-bold text-slate-900">—</div>
        <div class="mt-1 text-xs text-slate-500">{{ __('farm.linked_products') }}</div>
    </div>

    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5">
        <div class="text-xs uppercase tracking-wide text-slate-400">{{ __('farm.batches_label') }}</div>
        <div class="mt-2 text-3xl font-bold text-slate-900">—</div>
        <div class="mt-1 text-xs text-slate-500">{{ __('farm.traceability_batches') }}</div>
    </div>
</div>

{{-- Submodule placeholders --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="font-semibold text-slate-900">{{ __('farm.fields_label') }}</div>
            <span class="text-xs text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">{{ __('farm.coming_soon') }}</span>
        </div>
        <div class="p-5 text-sm text-slate-500">
            {{ __('farm.fields_panel_desc') }}
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="font-semibold text-slate-900">{{ __('farm.livestock_groups_label') }}</div>
            <span class="text-xs text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">{{ __('farm.coming_soon') }}</span>
        </div>
        <div class="p-5 text-sm text-slate-500">
            {{ __('farm.livestock_panel_desc') }}
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="font-semibold text-slate-900">{{ __('farm.inputs_certifications') }}</div>
            <span class="text-xs text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">{{ __('farm.coming_soon') }}</span>
        </div>
        <div class="p-5 text-sm text-slate-500">
            {{ __('farm.inputs_panel_desc') }}
        </div>
    </div>

</div>
@endsection
