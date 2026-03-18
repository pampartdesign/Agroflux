@extends('layouts.app')
@section('content')

{{-- ── Header ──────────────────────────────────────────────────────── --}}
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
            <a href="{{ route('farm.dashboard') }}" class="hover:text-emerald-700 transition">{{ __('farm.breadcrumb_farm_management') }}</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-900 font-medium">{{ __('farm.field_management_title') }}</span>
        </div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('farm.field_management_title') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('farm.field_management_subtitle') }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('core.farms.index') }}"
           class="inline-flex items-center gap-2 h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm font-medium text-slate-700">
            🏡 {{ __('app.farms') }}
        </a>
        @if($cropTypes->isNotEmpty() && $farms->isNotEmpty())
        <button onclick="document.getElementById('addFieldModal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm flex-shrink-0">
            {{ __('farm.btn_add_field') }}
        </button>
        @endif
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
    <span class="inline-flex items-center gap-1.5">
        <span class="h-5 w-5 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">2</span>
        <a href="{{ route('farm.crop-types.index') }}" class="hover:underline">{{ __('farm.step_add_crops') }}</a>
    </span>
    <span class="text-slate-300">→</span>
    <span class="inline-flex items-center gap-1.5 font-semibold text-emerald-700">
        <span class="h-5 w-5 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">3</span>
        {{ __('farm.step_add_fields') }} <span class="text-emerald-500">{{ __('farm.step_you_are_here') }}</span>
    </span>
</div>

{{-- ── Prerequisite guards ──────────────────────────────────────────── --}}
@if($farms->isEmpty())
<div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 flex items-center gap-3">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
    </svg>
    <span>{{ __('farm.need_farm_first') }}
        <a href="{{ route('core.farms.create') }}" class="font-semibold underline hover:no-underline">{{ __('farm.add_a_farm_link') }}</a>
        {{ __('farm.before_registering_fields') }}
    </span>
</div>
@elseif($cropTypes->isEmpty())
<div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 flex items-center gap-3">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
    </svg>
    <span>{{ __('farm.no_crop_profiles_yet') }}
        <a href="{{ route('farm.crop-types.index') }}" class="font-semibold underline hover:no-underline">{{ __('farm.add_crops_first_link') }}</a>
        {{ __('farm.fields_require_crops') }}
    </span>
</div>
@endif

{{-- ── KPI Cards ─────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('farm.kpi_total_fields') }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $totalFields }}</div>
    </div>
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('farm.kpi_active_growing_label') }}</div>
        <div class="text-3xl font-bold" style="color:#059669;">{{ $activeFields }}</div>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('farm.kpi_total_hectares') }}</div>
        <div class="text-3xl font-bold text-slate-900">
            {{ $totalHectares > 0 ? number_format((float)$totalHectares, 1) : '—' }}
        </div>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('farm.kpi_harvests_this_year') }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $harvestsThisYear }}</div>
    </div>
</div>

{{-- ── Fields Table ─────────────────────────────────────────────────── --}}
<div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div class="font-semibold text-slate-900">{{ __('farm.registered_fields') }}</div>
        <div class="text-xs text-slate-400">{{ $totalFields }} {{ __('app.total') }}</div>
    </div>

    @if($fields->isEmpty())
    <div class="p-12 text-center">
        <div class="h-14 w-14 rounded-2xl flex items-center justify-center mx-auto mb-4"
             style="background:#f0fdf4;border:1px solid #d1fae5;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="color:#34d399;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <p class="text-sm font-semibold text-slate-700 mb-1">{{ __('farm.no_fields_yet') }}</p>
        @if($farms->isEmpty())
            <p class="text-xs text-slate-400 max-w-xs mx-auto mb-4">{{ __('farm.start_by_adding_farm') }}</p>
            <a href="{{ route('core.farms.create') }}"
               class="inline-flex items-center gap-2 h-9 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
                {{ __('farm.step1_add_farm') }}
            </a>
        @elseif($cropTypes->isEmpty())
            <p class="text-xs text-slate-400 max-w-xs mx-auto mb-4">{{ __('farm.have_farm_no_crops') }}</p>
            <a href="{{ route('farm.crop-types.index') }}"
               class="inline-flex items-center gap-2 h-9 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
                {{ __('farm.step2_add_crop_profiles') }}
            </a>
        @else
            <p class="text-xs text-slate-400 max-w-xs mx-auto mb-4">{{ __('farm.add_first_field_hint') }}</p>
            <button onclick="document.getElementById('addFieldModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 h-9 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
                {{ __('farm.btn_add_first_field') }}
            </button>
        @endif
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[780px]">
            <thead>
                <tr class="bg-slate-50 text-left">
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('farm.col_field') }}</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('farm.col_farm') }}</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('farm.col_crop_profile') }}</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('farm.col_area') }}</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('farm.col_status') }}</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('farm.col_planted') }}</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('farm.col_harvest') }}</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide text-right">{{ __('app.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($fields as $field)
                @php
                    [$bg, $tx] = match($field->status) {
                        'active'    => ['#f0fdf4', '#047857'],
                        'fallow'    => ['#f1f5f9', '#475569'],
                        'harvested' => ['#f0fdfa', '#0f766e'],
                        'prep'      => ['#fffbeb', '#b45309'],
                        default     => ['#f1f5f9', '#475569'],
                    };
                    $cropName = $field->cropType?->crop?->name
                             ?? $field->cropType?->name
                             ?? $field->crop_type
                             ?? null;
                    $sciName  = $field->cropType?->crop?->scientific_name ?? null;
                @endphp
                <tr class="hover:bg-slate-50/60 transition">
                    <td class="px-5 py-3 font-medium text-slate-900">
                        {{ $field->name }}
                        @if($field->notes)
                        <div class="text-xs text-slate-400 truncate max-w-[180px]" title="{{ $field->notes }}">{{ $field->notes }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-600">{{ $field->farm?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-600">
                        @if($cropName)
                            <div class="font-medium text-slate-800">{{ $cropName }}</div>
                            @if($sciName)
                                <div class="text-xs italic text-slate-400">{{ $sciName }}</div>
                            @endif
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        {{ $field->area_ha ? number_format((float)$field->area_ha, 2).' ha' : '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                              style="background:{{ $bg }};color:{{ $tx }};">
                            {{ $field->statusLabel() }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-slate-500 text-xs">
                        {{ $field->planted_at ? $field->planted_at->format('d M Y') : '—' }}
                    </td>
                    <td class="px-4 py-3 text-xs">
                        @if($field->harvest_at)
                            @php $overdue = $field->status === 'active' && $field->harvest_at->isPast(); @endphp
                            <span style="{{ $overdue ? 'color:#dc2626;font-weight:600;' : 'color:#64748b;' }}">
                                {{ $field->harvest_at->format('d M Y') }}
                                @if($overdue) ⚠ @endif
                            </span>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button onclick="openFieldEdit(
                                    {{ $field->id }},
                                    {{ $field->farm_id }},
                                    @js($field->name),
                                    '{{ $field->area_ha ?? '' }}',
                                    {{ $field->crop_type_id ?? 'null' }},
                                    '{{ $field->status }}',
                                    '{{ $field->planted_at?->format('Y-m-d') ?? '' }}',
                                    '{{ $field->harvest_at?->format('Y-m-d') ?? '' }}',
                                    @js($field->notes ?? '')
                                )"
                                class="text-xs font-medium hover:underline mr-2" style="color:#047857;">
                            {{ __('app.edit') }}
                        </button>
                        <form method="POST" action="{{ route('farm.fields.destroy', $field) }}"
                              class="inline"
                              onsubmit="return confirm('{{ __('farm.confirm_remove_field') }}')">
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
     ADD FIELD MODAL
══════════════════════════════════════════════════════════════════ --}}
@if($cropTypes->isNotEmpty())
<div id="addFieldModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg border border-slate-100 max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="font-semibold text-slate-900">{{ __('farm.modal_add_field') }}</div>
            <button onclick="document.getElementById('addFieldModal').classList.add('hidden')"
                    class="h-8 w-8 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition text-xl leading-none">×</button>
        </div>
        <div class="overflow-y-auto flex-1">
        <form method="POST" action="{{ route('farm.fields.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_farm') }} <span class="text-red-500">*</span></label>
                    <select name="farm_id" required
                            class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="">{{ __('farm.select_farm') }}</option>
                        @foreach($farms as $farm)
                            <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_field_name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="{{ __('farm.placeholder_field_name') }}"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_area_ha') }}</label>
                    <input type="number" step="0.01" min="0" name="area_ha" placeholder="{{ __('farm.placeholder_area') }}"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">
                        {{ __('farm.label_crop_profile') }}
                        <span class="text-slate-300 font-normal">{{ __('farm.label_crop_profile_hint') }}</span>
                    </label>
                    <select name="crop_type_id"
                            class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="">{{ __('farm.none_not_assigned') }}</option>
                        @foreach($cropTypes as $ct)
                            <option value="{{ $ct->id }}">
                                {{ $ct->crop?->name ?? $ct->name }}
                                @if($ct->crop?->scientific_name) ({{ $ct->crop->scientific_name }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_status') }}</label>
                    <select name="status"
                            class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="active">{{ __('farm.status_active') }}</option>
                        <option value="fallow">{{ __('farm.status_fallow') }}</option>
                        <option value="harvested">{{ __('farm.status_harvested') }}</option>
                        <option value="prep">{{ __('farm.status_prep') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_planting_date') }}</label>
                    <input type="date" name="planted_at"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_expected_harvest') }}</label>
                    <input type="date" name="harvest_at"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_notes') }}</label>
                <textarea name="notes" rows="2" placeholder="{{ __('farm.notes_placeholder') }}"
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 resize-none"></textarea>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('addFieldModal').classList.add('hidden')"
                        class="h-10 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition">{{ __('app.cancel') }}</button>
                <button type="submit"
                        class="h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
                    {{ __('farm.btn_save_field') }}
                </button>
            </div>
        </form>
        </div>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════
     EDIT FIELD MODAL
══════════════════════════════════════════════════════════════════ --}}
<div id="editFieldModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg border border-slate-100 max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="font-semibold text-slate-900">{{ __('farm.modal_edit_field') }}</div>
            <button onclick="document.getElementById('editFieldModal').classList.add('hidden')"
                    class="h-8 w-8 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition text-xl leading-none">×</button>
        </div>
        <div class="overflow-y-auto flex-1">
        <form id="editFieldForm" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_farm') }} <span class="text-red-500">*</span></label>
                    <select id="editFarmId" name="farm_id" required
                            class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        @foreach($farms as $farm)
                            <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_field_name') }} <span class="text-red-500">*</span></label>
                    <input type="text" id="editName" name="name" required
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_area_ha') }}</label>
                    <input type="number" step="0.01" min="0" id="editAreaHa" name="area_ha"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">
                        {{ __('farm.label_crop_profile') }}
                        <span class="text-slate-300 font-normal">{{ __('farm.label_crop_profile_hint') }}</span>
                    </label>
                    <select id="editCropTypeId" name="crop_type_id"
                            class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="">{{ __('farm.none_not_assigned') }}</option>
                        @foreach($cropTypes as $ct)
                            <option value="{{ $ct->id }}">
                                {{ $ct->crop?->name ?? $ct->name }}
                                @if($ct->crop?->scientific_name) ({{ $ct->crop->scientific_name }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_status') }}</label>
                    <select id="editStatus" name="status"
                            class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="active">{{ __('farm.status_active') }}</option>
                        <option value="fallow">{{ __('farm.status_fallow') }}</option>
                        <option value="harvested">{{ __('farm.status_harvested') }}</option>
                        <option value="prep">{{ __('farm.status_prep') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_planting_date') }}</label>
                    <input type="date" id="editPlantedAt" name="planted_at"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_expected_harvest') }}</label>
                    <input type="date" id="editHarvestAt" name="harvest_at"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_notes') }}</label>
                <textarea id="editNotes" name="notes" rows="2"
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 resize-none"></textarea>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('editFieldModal').classList.add('hidden')"
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
function openFieldEdit(id, farmId, name, areaHa, cropTypeId, status, plantedAt, harvestAt, notes) {
    document.getElementById('editFieldForm').action    = '/farm/fields/' + id;
    document.getElementById('editFarmId').value        = farmId;
    document.getElementById('editName').value          = name;
    document.getElementById('editAreaHa').value        = areaHa;
    document.getElementById('editCropTypeId').value    = cropTypeId !== null ? String(cropTypeId) : '';
    document.getElementById('editStatus').value        = status;
    document.getElementById('editPlantedAt').value     = plantedAt;
    document.getElementById('editHarvestAt').value     = harvestAt;
    document.getElementById('editNotes').value         = notes;
    document.getElementById('editFieldModal').classList.remove('hidden');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('addFieldModal')?.classList.add('hidden');
        document.getElementById('editFieldModal').classList.add('hidden');
    }
});
</script>

@endsection
