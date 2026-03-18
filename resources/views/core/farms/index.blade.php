{{-- resources/views/core/farms/index.blade.php --}}
@extends('layouts.app')

@section('content')
@php
  $farms = $farms ?? collect();
@endphp

<div class="flex items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('farm.index_title') }}</h1>
        <p class="text-sm text-slate-600 mt-1">{{ __('farm.index_subtitle') }}</p>
    </div>

    <a href="{{ route('core.farms.create') }}"
       class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-sm">
        <span class="text-lg leading-none">+</span>
        <span class="text-sm font-medium">{{ __('farm.btn_add_farm_label') }}</span>
    </a>
</div>

@if ($farms->isEmpty())
    {{-- Empty State --}}
    <div class="rounded-2xl border border-emerald-100 bg-white p-10 shadow-sm">
        <div class="max-w-xl">
            <div class="h-12 w-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 21h18" />
                    <path d="M5 21V7l8-4 6 4v14" />
                    <path d="M9 21v-8h6v8" />
                </svg>
            </div>

            <h2 class="mt-4 text-lg font-semibold text-slate-900">{{ __('farm.no_farms_title') }}</h2>
            <p class="mt-1 text-sm text-slate-600">
                {{ __('farm.no_farms_desc') }}
            </p>

            <div class="mt-6 flex items-center gap-3">
                <a href="{{ route('core.farms.create') }}"
                   class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-sm">
                    <span class="text-lg leading-none">+</span>
                    <span class="text-sm font-medium">{{ __('farm.btn_add_first_farm_long') }}</span>
                </a>

                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center h-10 px-4 rounded-xl border border-emerald-200 bg-white hover:bg-emerald-50 transition text-sm">
                    {{ __('farm.back_to_dashboard') }}
                </a>
            </div>
        </div>
    </div>
@else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Farms list --}}
        <div class="lg:col-span-1">
            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-emerald-100 flex items-center justify-between">
                    <div class="font-semibold text-slate-900">{{ __('farm.your_farms_list') }}</div>
                    <div class="text-xs text-slate-500">{{ $farms->count() }} {{ __('app.total') }}</div>
                </div>

                <div class="p-4 space-y-3">
                    @foreach($farms as $farm)
                        @php
                            $location = $farm->location ?? $farm->city ?? $farm->region ?? $farm->address ?? null;
                            $ha = $farm->area_ha ?? $farm->hectares ?? $farm->area ?? null;
                        @endphp

                        <a href="{{ route('core.farms.show', $farm) }}"
                           class="block rounded-2xl border border-slate-200 bg-white hover:bg-emerald-50/40 transition p-4">
                            <div class="flex items-start gap-3">
                                <div class="h-10 w-10 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 21h18" />
                                        <path d="M5 21V7l8-4 6 4v14" />
                                    </svg>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="font-semibold text-slate-900 truncate">{{ $farm->name }}</div>
                                        <span class="text-xs text-slate-400">›</span>
                                    </div>

                                    <div class="mt-1 flex items-center gap-2 text-sm text-slate-600">
                                        @if($location)
                                            <span class="inline-flex items-center gap-1 truncate">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M12 22s8-4 8-10a8 8 0 10-16 0c0 6 8 10 8 10z"/>
                                                    <path d="M12 11a3 3 0 100-6 3 3 0 000 6z"/>
                                                </svg>
                                                <span class="truncate">{{ $location }}</span>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-50 border border-slate-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M3 3v18h18"/>
                                                <path d="M7 17V9"/>
                                                <path d="M12 17V5"/>
                                                <path d="M17 17v-7"/>
                                            </svg>
                                            {{ $ha ? (rtrim(rtrim(number_format((float)$ha, 2, '.', ''), '0'), '.').' ha') : '— ha' }}
                                        </span>

                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-50 border border-slate-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                                            {{ __('farm.fields_label') }}: {{ $farm->fields_count ?? 0 }}
                                        </span>

                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-50 border border-slate-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                                            {{ __('farm.livestock_label') }}: {{ $farm->livestock_groups_count ?? 0 }}
                                        </span>
                                    </div>
                                </div>

                                <a href="{{ route('core.farms.edit', $farm) }}"
                                   onclick="event.stopPropagation();"
                                   class="flex-shrink-0 inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white hover:bg-emerald-50 transition text-slate-500 hover:text-emerald-700"
                                   title="{{ __('app.edit') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </a>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right: Summary panel --}}
        <div class="lg:col-span-2">
            @php
              $preview = $farms->first();
              $location = $preview->location ?? $preview->city ?? $preview->region ?? $preview->address ?? null;
              $ha = $preview->area_ha ?? $preview->hectares ?? $preview->area ?? null;
            @endphp

            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-emerald-100 flex items-start justify-between gap-4">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-slate-400">{{ __('farm.quick_overview') }} — {{ $preview->name }}</div>
                        <a href="{{ route('core.farms.show', $preview) }}"
                           class="mt-1 block text-lg font-semibold text-slate-900 hover:text-emerald-700 transition">
                            {{ $preview->name }}
                        </a>

                        <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-slate-600">
                            @if($location)
                                <span class="inline-flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 22s8-4 8-10a8 8 0 10-16 0c0 6 8 10 8 10z"/>
                                        <path d="M12 11a3 3 0 100-6 3 3 0 000 6z"/>
                                    </svg>
                                    {{ $location }}
                                </span>
                            @endif

                            <span class="inline-flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 3v18h18"/>
                                    <path d="M7 17V9"/>
                                    <path d="M12 17V5"/>
                                    <path d="M17 17v-7"/>
                                </svg>
                                {{ $ha ? (rtrim(rtrim(number_format((float)$ha, 2, '.', ''), '0'), '.').' '.__('farm.hectares_label')) : '— '.__('farm.hectares_label') }}
                            </span>

                            <span class="inline-flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 7h18"/>
                                    <path d="M3 12h18"/>
                                    <path d="M3 17h18"/>
                                </svg>
                                {{ $preview->fields_count ?? 0 }} {{ __('farm.fields_count') }}
                            </span>

                            <span class="inline-flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 7l-8-4-8 4 8 4 8-4z"/>
                                    <path d="M4 7v10l8 4 8-4V7"/>
                                    <path d="M12 11v10"/>
                                </svg>
                                {{ $preview->livestock_groups_count ?? 0 }} {{ __('farm.livestock_groups') }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('core.farms.show', $preview) }}"
                           class="inline-flex items-center gap-1.5 h-10 px-3 rounded-xl border border-emerald-200 bg-white hover:bg-emerald-50 transition text-sm text-emerald-700"
                           title="{{ __('farm.farm_dashboard_btn') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"/>
                                <rect x="14" y="3" width="7" height="7"/>
                                <rect x="3" y="14" width="7" height="7"/>
                                <rect x="14" y="14" width="7" height="7"/>
                            </svg>
                            {{ __('farm.farm_dashboard_btn') }}
                        </a>

                        <a href="{{ route('core.farms.edit', $preview) }}"
                           class="inline-flex items-center justify-center h-10 w-10 rounded-xl border border-slate-200 bg-white hover:bg-emerald-50 transition text-slate-500 hover:text-emerald-700"
                           title="{{ __('app.edit') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="p-6">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <div class="font-semibold text-slate-900">{{ __('farm.next_steps') }}</div>
                        <ul class="mt-2 text-sm text-slate-600 space-y-1">
                            <li>• {{ __('farm.fields_label') }} (CRUD)</li>
                            <li>• {{ __('farm.livestock_groups_label') }} (CRUD)</li>
                            <li>• {{ __('farm.inputs_certifications') }}</li>
                        </ul>
                        <div class="mt-4 text-xs text-slate-500">
                            {{ __('farm.click_farm_hint') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
