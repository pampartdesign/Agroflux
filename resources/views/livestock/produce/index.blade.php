@extends('layouts.app')
@section('content')

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
            <a href="{{ route('livestock.dashboard') }}" class="hover:text-emerald-700 transition">{{ __('livestock.management_title') }}</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-900 font-medium">{{ __('livestock.breadcrumb_produce') }}</span>
        </div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('livestock.produce_title') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('livestock.produce_subtitle') }}</p>
    </div>
    <button onclick="document.getElementById('addProduceModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm flex-shrink-0">
        {{ __('livestock.btn_log_produce') }}
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
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('livestock.kpi_milk_today') }}</div>
        <div class="text-2xl font-bold text-slate-900">{{ $milkToday > 0 ? number_format($milkToday, 1).' L' : '—' }}</div>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('livestock.kpi_eggs_today') }}</div>
        <div class="text-2xl font-bold text-slate-900">{{ $eggsToday > 0 ? number_format($eggsToday, 0).' units' : '—' }}</div>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('livestock.kpi_this_week') }}</div>
        <div class="text-2xl font-bold text-slate-900">{{ $weekCount }} {{ $weekCount === 1 ? __('livestock.week_entry') : __('livestock.week_entries') }}</div>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('livestock.kpi_avg_daily') }}</div>
        <div class="text-2xl font-bold text-slate-400">{{ $avgDaily ? number_format($avgDaily, 1) : '—' }}</div>
    </div>
</div>

{{-- Produce log table --}}
<div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-900">{{ __('livestock.produce_log') }}</div>

    @if($logs->isEmpty())
    <div class="p-12 text-center">
        <div class="h-14 w-14 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center mx-auto mb-4 text-3xl">🥛</div>
        <p class="text-sm font-semibold text-slate-700 mb-1">{{ __('livestock.no_produce_title') }}</p>
        <p class="text-xs text-slate-400 max-w-xs mx-auto">{{ __('livestock.no_produce_desc') }}</p>
        <button onclick="document.getElementById('addProduceModal').classList.remove('hidden')"
                class="mt-4 inline-flex items-center gap-2 h-9 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
            {{ __('livestock.btn_log_first_entry') }}
        </button>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs font-semibold text-slate-400 uppercase tracking-wide border-b border-slate-100">
                    <th class="px-6 py-3 text-left">{{ __('livestock.col_date') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('livestock.col_type') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('livestock.col_quantity') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('livestock.col_animal') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('livestock.col_notes') }}</th>
                    <th class="px-6 py-3 text-right">{{ __('livestock.col_actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($logs as $log)
                <tr class="hover:bg-slate-50/60 transition">
                    <td class="px-6 py-3 text-slate-600">{{ $log->logged_at->format('d M Y') }}</td>
                    <td class="px-6 py-3 font-medium text-slate-800">{{ $log->type }}</td>
                    <td class="px-6 py-3 text-slate-600">{{ number_format($log->quantity, 2) }} {{ $log->unit }}</td>
                    <td class="px-6 py-3 text-slate-500">
                        @if($log->animal)
                            <span class="text-xs px-2 py-0.5 rounded-full" style="background:#f0fdf4;color:#047857;border:1px solid #d1fae5;">
                                {{ $log->animal->tag }}
                            </span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-6 py-3 text-slate-400 max-w-xs truncate">{{ $log->notes ?? '—' }}</td>
                    <td class="px-6 py-3 text-right">
                        <form method="POST" action="{{ route('livestock.produce.destroy', $log) }}"
                              onsubmit="return confirm('{{ __('livestock.confirm_remove_entry') }}')">
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

{{-- Add Produce Modal --}}
<div id="addProduceModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="font-semibold text-slate-900">{{ __('livestock.modal_log_produce') }}</div>
            <button onclick="document.getElementById('addProduceModal').classList.add('hidden')"
                    class="h-8 w-8 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition text-xl leading-none">×</button>
        </div>
        <form method="POST" action="{{ route('livestock.produce.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_produce_type') }} <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="">{{ __('livestock.select_placeholder') }}</option>
                        <option value="Milk">{{ __('livestock.type_milk') }}</option>
                        <option value="Eggs">{{ __('livestock.type_eggs') }}</option>
                        <option value="Meat">{{ __('livestock.type_meat') }}</option>
                        <option value="Wool">{{ __('livestock.type_wool') }}</option>
                        <option value="Honey">{{ __('livestock.type_honey') }}</option>
                        <option value="Other">{{ __('livestock.type_other') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_date') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="logged_at" required
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_quantity') }} <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="quantity" required placeholder="{{ __('livestock.placeholder_quantity') }}"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_unit') }} <span class="text-red-500">*</span></label>
                    <select name="unit" required class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="Litres">{{ __('livestock.unit_litres') }}</option>
                        <option value="kg">{{ __('livestock.unit_kg') }}</option>
                        <option value="Units">{{ __('livestock.unit_units') }}</option>
                        <option value="Dozen">{{ __('livestock.unit_dozen') }}</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_animal') }}</label>
                <select name="animal_id" class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    <option value="">{{ __('livestock.select_all_herd') }}</option>
                    @foreach($animals as $animal)
                        <option value="{{ $animal->id }}">{{ $animal->tag }} ({{ $animal->species }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_notes') }}</label>
                <textarea name="notes" rows="2" placeholder="{{ __('livestock.placeholder_notes_produce') }}"
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 resize-none"></textarea>
            </div>
            <div class="pt-2 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addProduceModal').classList.add('hidden')"
                        class="h-10 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition">{{ __('app.cancel') }}</button>
                <button type="submit" class="h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">{{ __('livestock.btn_save_entry') }}</button>
            </div>
        </form>
    </div>
</div>

@endsection
