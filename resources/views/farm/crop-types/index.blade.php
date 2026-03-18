@extends('layouts.app')
@section('content')

{{-- ── Header ──────────────────────────────────────────────────────── --}}
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
            <a href="{{ route('farm.dashboard') }}" class="hover:text-emerald-700 transition">{{ __('farm.breadcrumb_farm_management') }}</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-900 font-medium">{{ __('farm.crop_management_title') }}</span>
        </div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('farm.crop_management_title') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('farm.crop_management_subtitle') }}</p>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="document.getElementById('addCropModal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm flex-shrink-0">
            {{ __('farm.btn_add_crop_profile') }}
        </button>
    </div>
</div>

{{-- ── Flash messages ────────────────────────────────────────────────── --}}
@if(session('success'))
<div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 flex items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
    </svg>
    {{ session('error') }}
</div>
@endif
@if($errors->any())
<div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
    {{ $errors->first() }}
</div>
@endif

{{-- ── Step guide banner ─────────────────────────────────────────────── --}}
<div class="mb-5 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 text-sm text-slate-600 flex items-center gap-3">
    <span class="inline-flex items-center gap-1.5">
        <span class="h-5 w-5 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">1</span>
        <a href="{{ route('core.farms.create') }}" class="hover:underline">{{ __('farm.step_add_farm') }}</a>
    </span>
    <span class="text-slate-300">→</span>
    <span class="inline-flex items-center gap-1.5 font-semibold text-emerald-700">
        <span class="h-5 w-5 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">2</span>
        {{ __('farm.step_add_crops') }} <span class="text-emerald-500">{{ __('farm.step_you_are_here') }}</span>
    </span>
    <span class="text-slate-300">→</span>
    <span class="inline-flex items-center gap-1.5">
        <span class="h-5 w-5 rounded-full bg-slate-300 text-white text-xs font-bold flex items-center justify-center">3</span>
        <a href="{{ route('farm.fields.index') }}" class="hover:underline">{{ __('farm.step_add_fields') }}</a>
    </span>
</div>

{{-- ── KPI Cards ────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('farm.kpi_crop_profiles') }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $cropTypes->count() }}</div>
    </div>
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('farm.kpi_with_water_req') }}</div>
        <div class="text-3xl font-bold" style="color:#0284c7;">{{ $cropTypes->whereNotNull('min_daily_water_lt')->count() }}</div>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('farm.kpi_with_temp_range') }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $cropTypes->whereNotNull('min_temperature_c')->count() }}</div>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('farm.kpi_avg_growing_days') }}</div>
        <div class="text-3xl font-bold text-slate-900">
            @php $avg = $cropTypes->whereNotNull('growing_days')->avg('growing_days'); @endphp
            {{ $avg ? round($avg) : '—' }}
        </div>
    </div>
</div>

{{-- ── Crop Types Table ─────────────────────────────────────────────── --}}
<div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div class="font-semibold text-slate-900">{{ __('farm.my_crop_profiles') }}</div>
        <div class="text-xs text-slate-400">{{ $cropTypes->count() }} {{ __('app.total') }}</div>
    </div>

    @if($cropTypes->isEmpty())
    <div class="p-12 text-center">
        <div class="h-14 w-14 rounded-2xl flex items-center justify-center mx-auto mb-4"
             style="background:#f0fdf4;border:1px solid #d1fae5;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="#34d399" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <p class="text-sm font-semibold text-slate-700 mb-1">{{ __('farm.no_crop_profiles') }}</p>
        <p class="text-xs text-slate-400 max-w-xs mx-auto mb-4">
            {{ __('farm.no_crop_profiles_hint') }}
        </p>
        <button onclick="document.getElementById('addCropModal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 h-9 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
            {{ __('farm.btn_add_first_crop') }}
        </button>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[960px]">
            <thead>
                <tr class="bg-slate-50 text-left">
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('farm.label_crop') }}</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('farm.col_scientific_name') }}</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('farm.col_soil_moisture') }}</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('farm.col_daily_water') }}</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('farm.col_temp_range') }}</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('farm.col_soil_ph') }}</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('farm.col_sunlight') }}</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('farm.col_growing_days') }}</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('farm.col_assigned_fields') }}</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide text-right">{{ __('app.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($cropTypes as $ct)
                @php
                    $displayName = $ct->crop?->name ?? $ct->name ?? __('app.unknown');
                    $sciName     = $ct->crop?->scientific_name ?? '';
                @endphp
                <tr class="hover:bg-slate-50/60 transition">
                    <td class="px-5 py-3">
                        <div class="font-semibold text-slate-900">{{ $displayName }}</div>
                        @if($ct->crop?->category?->name)
                            <div class="text-xs text-slate-400">{{ $ct->crop->category->name }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($sciName)
                            <span class="text-xs italic text-slate-500">{{ $sciName }}</span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        @if($ct->min_soil_moisture_pct !== null)
                            <span class="inline-flex items-center gap-1">
                                <span class="h-2 w-2 rounded-full" style="background:#0284c7;"></span>
                                ≥ {{ number_format((float)$ct->min_soil_moisture_pct, 0) }}%
                            </span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        @if($ct->min_daily_water_lt !== null)
                            ≥ {{ number_format((float)$ct->min_daily_water_lt, 1) }} Lt
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        @if($ct->min_temperature_c !== null || $ct->max_temperature_c !== null)
                            @php
                                $tMin = $ct->min_temperature_c !== null ? number_format((float)$ct->min_temperature_c, 0).'°C' : '?';
                                $tMax = $ct->max_temperature_c !== null ? number_format((float)$ct->max_temperature_c, 0).'°C' : '?';
                            @endphp
                            {{ $tMin }} – {{ $tMax }}
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        @if($ct->min_soil_ph !== null || $ct->max_soil_ph !== null)
                            @php
                                $pMin = $ct->min_soil_ph !== null ? number_format((float)$ct->min_soil_ph, 1) : '?';
                                $pMax = $ct->max_soil_ph !== null ? number_format((float)$ct->max_soil_ph, 1) : '?';
                            @endphp
                            {{ $pMin }} – {{ $pMax }}
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        @if($ct->min_sunlight_h !== null)
                            ≥ {{ number_format((float)$ct->min_sunlight_h, 1) }} h/day
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        @if($ct->growing_days)
                            {{ $ct->growing_days }} days
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($ct->fields->isEmpty())
                            <span class="text-slate-300 text-xs">{{ __('farm.none_yet') }}</span>
                        @else
                            <div class="space-y-0.5">
                                @foreach($ct->fields as $f)
                                <div class="text-xs text-slate-700">
                                    <span class="font-medium">{{ $f->name }}</span>
                                    @if($f->farm)
                                        <span class="text-slate-400"> · {{ $f->farm->name }}</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <button onclick="openCropEdit(
                                    {{ $ct->id }},
                                    {{ $ct->crop_id ?? 'null' }},
                                    '{{ $ct->min_soil_moisture_pct ?? '' }}',
                                    '{{ $ct->min_daily_water_lt ?? '' }}',
                                    '{{ $ct->min_temperature_c ?? '' }}',
                                    '{{ $ct->max_temperature_c ?? '' }}',
                                    '{{ $ct->min_soil_ph ?? '' }}',
                                    '{{ $ct->max_soil_ph ?? '' }}',
                                    '{{ $ct->min_sunlight_h ?? '' }}',
                                    '{{ $ct->growing_days ?? '' }}',
                                    @js($ct->notes ?? '')
                                )"
                                class="text-xs font-medium hover:underline mr-2" style="color:#047857;">
                            {{ __('app.edit') }}
                        </button>
                        <form method="POST" action="{{ route('farm.crop-types.destroy', $ct) }}"
                              class="inline"
                              onsubmit="return confirm('{{ __('farm.confirm_remove_crop', ['name' => addslashes($displayName)]) }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-medium hover:underline" style="color:#dc2626;">{{ __('app.delete') }}</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════════════
     ADD CROP TYPE MODAL
══════════════════════════════════════════════════════════════════ --}}
<div id="addCropModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl border border-slate-100 max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="font-semibold text-slate-900">{{ __('farm.modal_add_crop') }}</div>
            <button onclick="document.getElementById('addCropModal').classList.add('hidden')"
                    class="h-8 w-8 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition text-xl leading-none">×</button>
        </div>
        <div class="overflow-y-auto flex-1">
        <form method="POST" action="{{ route('farm.crop-types.store') }}" class="px-6 py-5 space-y-5">
            @csrf

            {{-- Crop selector (global library) --}}
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">
                    {{ __('farm.label_crop') }} <span class="text-red-500">*</span>
                    <span class="text-slate-300 font-normal">{{ __('farm.label_crop_hint') }}</span>
                </label>
                <select name="crop_id" required
                        class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    <option value="">{{ __('farm.select_a_crop') }}</option>
                    @foreach($cropsByCategory as $cat)
                        @if($cat->crops->isNotEmpty())
                        <optgroup label="{{ $cat->name }}">
                            @foreach($cat->crops as $crop)
                            <option value="{{ $crop->id }}">
                                {{ $crop->name }}{{ $crop->scientific_name ? ' ('.$crop->scientific_name.')' : '' }}
                            </option>
                            @endforeach
                        </optgroup>
                        @endif
                    @endforeach
                </select>
            </div>

            {{-- Section: Minimum Requirements --}}
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex-1 border-t border-slate-100"></div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('farm.section_min_requirements') }} <span class="font-normal text-slate-300">({{ __('app.optional') }})</span></span>
                    <div class="flex-1 border-t border-slate-100"></div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">
                            {{ __('farm.label_min_soil_moisture') }} <span class="text-slate-300 font-normal">(% volumetric)</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="min_soil_moisture_pct" step="0.1" min="0" max="100"
                                   placeholder="e.g. 40"
                                   class="w-full h-10 pl-3 pr-10 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none">%</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">
                            {{ __('farm.label_min_daily_water') }} <span class="text-slate-300 font-normal">(Lt/100m²/day)</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="min_daily_water_lt" step="0.1" min="0"
                                   placeholder="e.g. 5.0"
                                   class="w-full h-10 pl-3 pr-10 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none">Lt</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_min_temperature') }}</label>
                        <div class="relative">
                            <input type="number" name="min_temperature_c" step="0.5" min="-50" max="60"
                                   placeholder="e.g. 10"
                                   class="w-full h-10 pl-3 pr-10 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none">°C</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_max_temperature') }}</label>
                        <div class="relative">
                            <input type="number" name="max_temperature_c" step="0.5" min="-50" max="60"
                                   placeholder="e.g. 35"
                                   class="w-full h-10 pl-3 pr-10 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none">°C</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_min_soil_ph') }}</label>
                        <input type="number" name="min_soil_ph" step="0.1" min="0" max="14"
                               placeholder="e.g. 5.5"
                               class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_max_soil_ph') }}</label>
                        <input type="number" name="max_soil_ph" step="0.1" min="0" max="14"
                               placeholder="e.g. 7.0"
                               class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_min_sunlight') }}</label>
                        <div class="relative">
                            <input type="number" name="min_sunlight_h" step="0.5" min="0" max="24"
                                   placeholder="e.g. 6"
                                   class="w-full h-10 pl-3 pr-14 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none">h/day</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_avg_growing_season') }}</label>
                        <div class="relative">
                            <input type="number" name="growing_days" step="1" min="1" max="1825"
                                   placeholder="e.g. 90"
                                   class="w-full h-10 pl-3 pr-12 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none">days</span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_notes') }}</label>
                <textarea name="notes" rows="2" placeholder="{{ __('farm.notes_crop_placeholder') }}"
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 resize-none"></textarea>
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('addCropModal').classList.add('hidden')"
                        class="h-10 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition">{{ __('app.cancel') }}</button>
                <button type="submit"
                        class="h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
                    {{ __('farm.btn_save_crop_profile') }}
                </button>
            </div>
        </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════
     EDIT CROP TYPE MODAL
══════════════════════════════════════════════════════════════════ --}}
<div id="editCropModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl border border-slate-100 max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="font-semibold text-slate-900">{{ __('farm.modal_edit_crop') }}</div>
            <button onclick="document.getElementById('editCropModal').classList.add('hidden')"
                    class="h-8 w-8 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition text-xl leading-none">×</button>
        </div>
        <div class="overflow-y-auto flex-1">
        <form id="editCropForm" method="POST" action="" class="px-6 py-5 space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">
                    {{ __('farm.label_crop') }} <span class="text-red-500">*</span>
                </label>
                <select id="editCtCropId" name="crop_id" required
                        class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    <option value="">{{ __('farm.select_a_crop') }}</option>
                    @foreach($cropsByCategory as $cat)
                        @if($cat->crops->isNotEmpty())
                        <optgroup label="{{ $cat->name }}">
                            @foreach($cat->crops as $crop)
                            <option value="{{ $crop->id }}">
                                {{ $crop->name }}{{ $crop->scientific_name ? ' ('.$crop->scientific_name.')' : '' }}
                            </option>
                            @endforeach
                        </optgroup>
                        @endif
                    @endforeach
                </select>
            </div>

            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex-1 border-t border-slate-100"></div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('farm.section_min_requirements') }}</span>
                    <div class="flex-1 border-t border-slate-100"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_min_soil_moisture') }} <span class="text-slate-300 font-normal">(%)</span></label>
                        <div class="relative">
                            <input type="number" id="editCtMoisture" name="min_soil_moisture_pct" step="0.1" min="0" max="100"
                                   class="w-full h-10 pl-3 pr-10 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none">%</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_min_daily_water') }} <span class="text-slate-300 font-normal">(Lt/100m²)</span></label>
                        <div class="relative">
                            <input type="number" id="editCtWater" name="min_daily_water_lt" step="0.1" min="0"
                                   class="w-full h-10 pl-3 pr-10 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none">Lt</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_min_temperature') }}</label>
                        <div class="relative">
                            <input type="number" id="editCtMinTemp" name="min_temperature_c" step="0.5"
                                   class="w-full h-10 pl-3 pr-10 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none">°C</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_max_temperature') }}</label>
                        <div class="relative">
                            <input type="number" id="editCtMaxTemp" name="max_temperature_c" step="0.5"
                                   class="w-full h-10 pl-3 pr-10 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none">°C</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_min_soil_ph') }}</label>
                        <input type="number" id="editCtMinPh" name="min_soil_ph" step="0.1" min="0" max="14"
                               class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_max_soil_ph') }}</label>
                        <input type="number" id="editCtMaxPh" name="max_soil_ph" step="0.1" min="0" max="14"
                               class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_min_sunlight') }}</label>
                        <div class="relative">
                            <input type="number" id="editCtSunlight" name="min_sunlight_h" step="0.5" min="0" max="24"
                                   class="w-full h-10 pl-3 pr-14 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none">h/day</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_avg_growing_season') }}</label>
                        <div class="relative">
                            <input type="number" id="editCtGrowingDays" name="growing_days" step="1" min="1" max="1825"
                                   class="w-full h-10 pl-3 pr-12 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none">days</span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_notes') }}</label>
                <textarea id="editCtNotes" name="notes" rows="2"
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 resize-none"></textarea>
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('editCropModal').classList.add('hidden')"
                        class="h-10 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition">{{ __('app.cancel') }}</button>
                <button type="submit"
                        class="h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
                    {{ __('app.save_changes') }}
                </button>
            </div>
        </form>
        </div>
    </div>
</div>

<script>
function openCropEdit(id, cropId, moisture, water, minTemp, maxTemp, minPh, maxPh, sunlight, growingDays, notes) {
    document.getElementById('editCropForm').action      = '{{ url('farm/crop-types') }}/' + id;
    document.getElementById('editCtCropId').value       = cropId !== null ? String(cropId) : '';
    document.getElementById('editCtMoisture').value     = moisture;
    document.getElementById('editCtWater').value        = water;
    document.getElementById('editCtMinTemp').value      = minTemp;
    document.getElementById('editCtMaxTemp').value      = maxTemp;
    document.getElementById('editCtMinPh').value        = minPh;
    document.getElementById('editCtMaxPh').value        = maxPh;
    document.getElementById('editCtSunlight').value     = sunlight;
    document.getElementById('editCtGrowingDays').value  = growingDays;
    document.getElementById('editCtNotes').value        = notes;
    document.getElementById('editCropModal').classList.remove('hidden');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('addCropModal').classList.add('hidden');
        document.getElementById('editCropModal').classList.add('hidden');
    }
});
</script>

@endsection
