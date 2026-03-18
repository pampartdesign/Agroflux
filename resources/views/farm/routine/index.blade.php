@extends('layouts.app')
@section('content')

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
            <a href="{{ route('farm.dashboard') }}" class="hover:text-emerald-700 transition">{{ __('farm.breadcrumb_farm_management') }}</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-900 font-medium">{{ __('app.nav_routine_monitor') }}</span>
        </div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('farm.routine_title') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('farm.routine_subtitle') }}</p>
    </div>
    <button onclick="document.getElementById('addRoutineModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm flex-shrink-0">
        {{ __('farm.btn_log_task') }}
    </button>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    {{ session('success') }}
</div>
@endif

{{-- Category cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    @foreach([
        [__('farm.cat_irrigation'),'💧'],
        [__('farm.cat_fertilisation'),'🌿'],
        [__('farm.cat_pest_control'),'🐛'],
        [__('farm.cat_soil_check'),'🪱']
    ] as [$cat,$emoji])
    @php $count = $categoryCounts[$cat] ?? ($categoryCounts[array_keys($categoryCounts)[array_search($cat, array_values($categoryCounts))] ?? ''] ?? 0); @endphp
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-2xl mb-2">{{ $emoji }}</div>
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">{{ $cat }}</div>
        @php
            $catKey = match($cat) {
                __('farm.cat_irrigation') => 'Irrigation',
                __('farm.cat_fertilisation') => 'Fertilisation',
                __('farm.cat_pest_control') => 'Pest Control',
                __('farm.cat_soil_check') => 'Soil Check',
                default => $cat
            };
            $catCount = $categoryCounts[$catKey] ?? 0;
        @endphp
        <div class="text-lg font-bold text-slate-900">
            {{ $catCount > 0 ? $catCount.' '.__('farm.pending_label') : '—' }}
        </div>
    </div>
    @endforeach
</div>

{{-- Task list --}}
<div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-900">{{ __('farm.scheduled_tasks') }}</div>

    @if($tasks->isEmpty())
    <div class="p-12 text-center">
        <div class="h-14 w-14 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <p class="text-sm font-semibold text-slate-700 mb-1">{{ __('farm.no_tasks_yet') }}</p>
        <p class="text-xs text-slate-400 max-w-xs mx-auto">{{ __('farm.no_tasks_hint') }}</p>
        <button onclick="document.getElementById('addRoutineModal').classList.remove('hidden')"
                class="mt-4 inline-flex items-center gap-2 h-9 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
            {{ __('farm.btn_add_first_task') }}
        </button>
    </div>
    @else
    <div class="divide-y divide-slate-50">
        @foreach($tasks as $task)
        @php
            $isPending = $task->status === 'pending';
            $isDone    = $task->status === 'done';
            $isOverdue = $isPending && $task->scheduled_at->isPast();
        @endphp
        <div class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50/60 transition">
            {{-- Status dot --}}
            <div class="flex-shrink-0 h-2.5 w-2.5 rounded-full"
                 style="background:{{ $isDone ? '#059669' : ($isOverdue ? '#dc2626' : '#f59e0b') }};"></div>

            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm font-medium text-slate-800">{{ $task->type }}</span>
                    @if($task->field)
                        <span class="text-xs px-2 py-0.5 rounded-full" style="background:#f0fdf4;color:#047857;border:1px solid #d1fae5;">
                            {{ $task->field->name }}
                        </span>
                    @endif
                    <span class="text-xs px-2 py-0.5 rounded-full
                        @if($isDone) " style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;"
                        @elseif($isOverdue) " style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;"
                        @else " style="background:#fffbeb;color:#b45309;border:1px solid #fde68a;"
                        @endif>
                        {{ $isDone ? __('farm.status_done') : ($isOverdue ? __('farm.status_overdue') : __('farm.status_pending')) }}
                    </span>
                </div>
                <div class="text-xs text-slate-400 mt-0.5">
                    {{ $task->scheduled_at->format('d M Y') }}
                    @if($task->notes) · {{ Str::limit($task->notes, 60) }} @endif
                </div>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0">
                @if($isPending)
                <form method="POST" action="{{ route('farm.routine.done', $task) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="text-xs h-8 px-3 rounded-lg border hover:bg-emerald-50 transition"
                            style="border-color:#d1fae5;color:#047857;">
                        {{ __('farm.btn_mark_done') }}
                    </button>
                </form>
                @endif
                <form method="POST" action="{{ route('farm.routine.destroy', $task) }}"
                      onsubmit="return confirm('{{ __('farm.confirm_remove_task') }}')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="text-xs h-8 px-3 rounded-lg border border-slate-200 hover:bg-red-50 hover:border-red-200 transition text-slate-400 hover:text-red-500">
                        ✕
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Add Task Modal --}}
<div id="addRoutineModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="font-semibold text-slate-900">{{ __('farm.modal_log_task') }}</div>
            <button onclick="document.getElementById('addRoutineModal').classList.add('hidden')"
                    class="h-8 w-8 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition text-xl leading-none">×</button>
        </div>
        <form method="POST" action="{{ route('farm.routine.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_task_type') }} <span class="text-red-500">*</span></label>
                <select name="type" required class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    <option value="">{{ __('farm.select_task_type') }}</option>
                    <option value="Irrigation">{{ __('farm.cat_irrigation') }}</option>
                    <option value="Fertilisation">{{ __('farm.cat_fertilisation') }}</option>
                    <option value="Pest Control">{{ __('farm.cat_pest_control') }}</option>
                    <option value="Soil Check">{{ __('farm.cat_soil_check') }}</option>
                    <option value="Pruning">{{ __('farm.cat_pruning') }}</option>
                    <option value="Other">{{ __('farm.cat_other') }}</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_field') }}</label>
                    <select name="field_id" class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="">{{ __('farm.all_fields') }}</option>
                        @foreach($fields as $field)
                            <option value="{{ $field->id }}">
                                {{ $field->name }}@if($field->farm) ({{ $field->farm->name }})@endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_date') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="scheduled_at" required
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('farm.label_notes') }}</label>
                <textarea name="notes" rows="2" placeholder="{{ __('farm.notes_task_placeholder') }}"
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 resize-none"></textarea>
            </div>
            <div class="pt-2 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addRoutineModal').classList.add('hidden')"
                        class="h-10 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition">{{ __('app.cancel') }}</button>
                <button type="submit" class="h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">{{ __('farm.btn_save_task') }}</button>
            </div>
        </form>
    </div>
</div>

@endsection
