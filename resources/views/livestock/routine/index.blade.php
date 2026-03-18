@extends('layouts.app')
@section('content')

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
            <a href="{{ route('livestock.dashboard') }}" class="hover:text-emerald-700 transition">{{ __('livestock.management_title') }}</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-900 font-medium">{{ __('livestock.breadcrumb_routine') }}</span>
        </div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('livestock.routine_title') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('livestock.routine_subtitle') }}</p>
    </div>
    <button onclick="document.getElementById('addCheckModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm flex-shrink-0">
        {{ __('livestock.btn_log_check') }}
    </button>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    {{ session('success') }}
</div>
@endif

{{-- KPI Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('livestock.kpi_todays_checks') }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $checksToday }}</div>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('livestock.kpi_feeding_logs') }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $feedingLogs }}</div>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('livestock.kpi_health_alerts') }}</div>
        <div class="text-3xl font-bold" style="color:{{ $healthAlerts > 0 ? '#dc2626' : '#1e293b' }};">{{ $healthAlerts }}</div>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('livestock.kpi_vet_visits_month') }}</div>
        <div class="text-3xl font-bold text-amber-500">{{ $vetThisMonth }}</div>
    </div>
</div>

{{-- Check log table --}}
<div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-900">{{ __('livestock.daily_check_log') }}</div>

    @if($checks->isEmpty())
    <div class="p-12 text-center">
        <div class="h-14 w-14 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center mx-auto mb-4 text-3xl">📋</div>
        <p class="text-sm font-semibold text-slate-700 mb-1">{{ __('livestock.no_checks_title') }}</p>
        <p class="text-xs text-slate-400 max-w-xs mx-auto">{{ __('livestock.no_checks_desc') }}</p>
        <button onclick="document.getElementById('addCheckModal').classList.remove('hidden')"
                class="mt-4 inline-flex items-center gap-2 h-9 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
            {{ __('livestock.btn_log_first_check') }}
        </button>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs font-semibold text-slate-400 uppercase tracking-wide border-b border-slate-100">
                    <th class="px-6 py-3 text-left">{{ __('livestock.col_check_date') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('livestock.col_check_type') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('livestock.col_check_animal') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('livestock.col_check_status') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('livestock.col_check_notes') }}</th>
                    <th class="px-6 py-3 text-right">{{ __('livestock.col_check_actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($checks as $check)
                @php
                    $statusStyle = match($check->status) {
                        'normal'   => 'background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;',
                        'alert'    => 'background:#fffbeb;color:#b45309;border:1px solid #fde68a;',
                        'critical' => 'background:#fef2f2;color:#dc2626;border:1px solid #fecaca;',
                        default    => 'background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;',
                    };
                @endphp
                <tr class="hover:bg-slate-50/60 transition">
                    <td class="px-6 py-3 text-slate-600">{{ $check->checked_at->format('d M Y') }}</td>
                    <td class="px-6 py-3 font-medium text-slate-800">{{ $check->type }}</td>
                    <td class="px-6 py-3">
                        @if($check->animal)
                            <span class="text-xs px-2 py-0.5 rounded-full" style="background:#f0fdf4;color:#047857;border:1px solid #d1fae5;">
                                {{ $check->animal->tag }}
                            </span>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full capitalize" style="{{ $statusStyle }}">
                            {{ ucfirst($check->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-slate-400 max-w-xs truncate">{{ $check->notes ?? '—' }}</td>
                    <td class="px-6 py-3 text-right">
                        <form method="POST" action="{{ route('livestock.routine.destroy', $check) }}"
                              onsubmit="return confirm('{{ __('livestock.confirm_remove_check') }}')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs h-7 px-2.5 rounded-lg border border-slate-200 hover:bg-red-50 hover:border-red-200 hover:text-red-500 transition text-slate-400">
                                ✕
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- Add Check Modal --}}
<div id="addCheckModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="font-semibold text-slate-900">{{ __('livestock.modal_log_check') }}</div>
            <button onclick="document.getElementById('addCheckModal').classList.add('hidden')"
                    class="h-8 w-8 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition text-xl leading-none">×</button>
        </div>
        <form method="POST" action="{{ route('livestock.routine.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_check_type') }} <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="">{{ __('livestock.select_placeholder') }}</option>
                        <option value="Morning Feeding">{{ __('livestock.check_morning_feeding') }}</option>
                        <option value="Evening Feeding">{{ __('livestock.check_evening_feeding') }}</option>
                        <option value="Health Observation">{{ __('livestock.check_health_obs') }}</option>
                        <option value="Vaccination">{{ __('livestock.check_vaccination') }}</option>
                        <option value="Vet Visit">{{ __('livestock.check_vet_visit') }}</option>
                        <option value="Weight Check">{{ __('livestock.check_weight_check') }}</option>
                        <option value="Other">{{ __('livestock.check_other') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_check_date') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="checked_at" required
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_check_animal') }}</label>
                <select name="animal_id" class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    <option value="">{{ __('livestock.select_all_herd') }}</option>
                    @foreach($animals as $animal)
                        <option value="{{ $animal->id }}">{{ $animal->tag }} ({{ $animal->species }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_check_status') }} <span class="text-red-500">*</span></label>
                <div class="flex gap-4">
                    @foreach([__('livestock.status_normal') => 'normal', __('livestock.status_alert') => 'alert', __('livestock.status_critical') => 'critical'] as $label => $value)
                    <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                        <input type="radio" name="status" value="{{ $value }}" {{ $value === 'normal' ? 'checked' : '' }}
                               class="accent-emerald-600">
                        <span>{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_observations') }}</label>
                <textarea name="notes" rows="3" placeholder="{{ __('livestock.placeholder_observations') }}"
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 resize-none"></textarea>
            </div>
            <div class="pt-2 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addCheckModal').classList.add('hidden')"
                        class="h-10 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition">{{ __('app.cancel') }}</button>
                <button type="submit" class="h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">{{ __('livestock.btn_save_check') }}</button>
            </div>
        </form>
    </div>
</div>

@endsection
