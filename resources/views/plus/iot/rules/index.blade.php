@extends('layouts.app')

@section('content')

{{-- Page header --}}
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Sensor Rules &amp; Conditions</h1>
        <p class="text-sm text-slate-500 mt-1">
            Automate actions based on scheduled times, sensor readings, weather forecasts, and custom retry logic.
        </p>
    </div>
    <a href="{{ route('plus.iot.rules.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Add Condition
    </a>
</div>

@if(session('status'))
    <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        {{ session('status') }}
    </div>
@endif

{{-- How it works banner (shown when empty) --}}
@if($rules->isEmpty())
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-8 py-14 text-center">
        <div class="mx-auto mb-4 h-16 w-16 rounded-2xl flex items-center justify-center" style="background:#f0fdf4;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
            </svg>
        </div>
        <h2 class="text-base font-semibold text-slate-800 mb-2">No rules yet</h2>
        <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">
            Rules let you automate sensor and pump behaviour. For example: <em>"Start irrigation at 14:00 — but skip if rain probability ≥ 40 %. Wait 2 hours; if no rain arrives, start anyway."</em>
        </p>
        <a href="{{ route('plus.iot.rules.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Add your first condition
        </a>
    </div>
@else

{{-- Rule cards --}}
<div class="space-y-4">
    @foreach($rules as $rule)
        @php $badge = $rule->statusBadge(); @endphp
        <div class="rounded-2xl border {{ $rule->is_active ? 'border-slate-200' : 'border-slate-100 opacity-60' }} bg-white shadow-sm hover:shadow-md transition-shadow">
            <div class="px-6 py-5">

                {{-- Top row: name + status badge + toggle + actions --}}
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        {{-- Active indicator --}}
                        <div class="mt-0.5 h-2.5 w-2.5 rounded-full flex-shrink-0 {{ $rule->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}"></div>
                        <div class="min-w-0">
                            <div class="text-base font-semibold text-slate-900 truncate">{{ $rule->name }}</div>
                            @if($rule->description)
                                <div class="text-xs text-slate-500 mt-0.5 truncate">{{ $rule->description }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- Last-status badge --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-xs font-medium px-2.5 py-0.5 rounded-full border {{ $badge['class'] }}">
                            {{ $badge['label'] }}
                        </span>
                        @if($rule->last_triggered_at)
                            <span class="text-xs text-slate-400">{{ $rule->last_triggered_at->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>

                {{-- Rule detail pills --}}
                <div class="mt-4 flex flex-wrap gap-2">

                    {{-- Trigger --}}
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 border border-blue-100 px-3 py-1 text-xs text-blue-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            @if($rule->trigger_type === 'time')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            @endif
                        </svg>
                        <strong>Trigger:</strong> {{ $rule->triggerLabel() }}
                    </span>

                    {{-- Weather condition --}}
                    @if($rule->weather_condition_enabled)
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-sky-50 border border-sky-100 px-3 py-1 text-xs text-sky-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                            </svg>
                            Skip if rain ≥ {{ $rule->weather_rain_skip_pct }}%
                        </span>
                    @endif

                    {{-- Sensor condition --}}
                    @if($rule->sensor_condition_enabled && $rule->conditionSensor)
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 border border-amber-100 px-3 py-1 text-xs text-amber-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Skip if {{ $rule->conditionSensor->name }}
                            {{ \App\Models\SensorRule::operatorLabel($rule->condition_operator) }}
                            {{ $rule->condition_threshold }} {{ $rule->conditionSensor->unit }}
                        </span>
                    @endif

                    {{-- Action --}}
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-violet-50 border border-violet-100 px-3 py-1 text-xs text-violet-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Action: {{ ucfirst($rule->action_type) }}
                    </span>

                    {{-- Retry --}}
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 border border-slate-200 px-3 py-1 text-xs text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Retry:
                        @if($rule->retry_type === 'wait_window')
                            Wait {{ $rule->retry_wait_minutes }} min
                        @elseif($rule->retry_type === 'next_scheduled')
                            Next scheduled run
                        @else
                            Wait {{ $rule->retry_wait_minutes }} min, then next run
                        @endif
                    </span>

                </div>

                {{-- Recent execution log (last 3 entries) --}}
                @php $ruleLogs = $recentLogs[$rule->id] ?? collect(); @endphp
                @if($ruleLogs->isNotEmpty())
                    <div class="mt-4 rounded-xl border border-slate-100 bg-slate-50 divide-y divide-slate-100 overflow-hidden">
                        <div class="px-4 py-2 flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Recent Executions</span>
                            <a href="{{ route('plus.iot.rules.logs', $rule) }}"
                               class="text-xs text-slate-400 hover:text-slate-700 transition">View all →</a>
                        </div>
                        @foreach($ruleLogs as $log)
                            @php $lb = $log->statusBadge(); @endphp
                            <div class="px-4 py-2 flex items-start gap-3">
                                <span class="mt-0.5 text-xs font-medium px-2 py-0.5 rounded-full border {{ $lb['class'] }} whitespace-nowrap flex-shrink-0">
                                    {{ $lb['label'] }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs text-slate-600 truncate">{{ $log->summary }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $log->evaluated_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Bottom row: toggle + logs + edit + delete --}}
                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                    {{-- Toggle active --}}
                    <form method="POST" action="{{ route('plus.iot.rules.toggle', $rule) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-lg border px-3 py-1.5 text-xs font-medium transition
                                       {{ $rule->is_active
                                           ? 'border-slate-200 text-slate-600 hover:border-red-200 hover:text-red-600 hover:bg-red-50'
                                           : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }}">
                            @if($rule->is_active)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Disable
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Enable
                            @endif
                        </button>
                    </form>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('plus.iot.rules.logs', $rule) }}"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Logs
                        </a>
                        <a href="{{ route('plus.iot.rules.edit', $rule) }}"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </a>
                        <form method="POST" action="{{ route('plus.iot.rules.destroy', $rule) }}"
                              onsubmit="return confirm('Delete rule \'{{ addslashes($rule->name) }}\'? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-red-500 hover:bg-red-50 hover:border-red-200 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    @endforeach
</div>

{{-- Pagination --}}
@if($rules->hasPages())
    <div class="mt-6">{{ $rules->links() }}</div>
@endif

@endif

@endsection
