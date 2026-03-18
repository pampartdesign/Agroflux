@extends('layouts.app')

@section('content')

{{-- Page header --}}
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Execution Log</h1>
        <p class="text-sm text-slate-500 mt-1">
            <span class="font-medium text-slate-700">{{ $rule->name }}</span>
            &mdash; {{ $rule->triggerLabel() }}
        </p>
    </div>
    <a href="{{ route('plus.iot.rules.index') }}"
       class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition">
        ← Back to Rules
    </a>
</div>

{{-- Rule summary bar --}}
<div class="mb-6 rounded-2xl border border-slate-200 bg-white shadow-sm px-6 py-4 flex flex-wrap gap-3 items-center">

    <span class="inline-flex items-center gap-1.5 rounded-lg {{ $rule->is_active ? 'bg-emerald-50 border-emerald-100 text-emerald-700' : 'bg-slate-100 border-slate-200 text-slate-500' }} border px-3 py-1 text-xs font-medium">
        <span class="h-1.5 w-1.5 rounded-full {{ $rule->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
        {{ $rule->is_active ? 'Active' : 'Disabled' }}
    </span>

    <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 border border-blue-100 px-3 py-1 text-xs text-blue-700">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $rule->triggerLabel() }}
    </span>

    @if($rule->weather_condition_enabled)
        <span class="inline-flex items-center gap-1.5 rounded-lg bg-sky-50 border border-sky-100 px-3 py-1 text-xs text-sky-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
            </svg>
            Skip if rain ≥ {{ $rule->weather_rain_skip_pct }}%
        </span>
    @endif

    @if($rule->sensor_condition_enabled && $rule->conditionSensor)
        <span class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 border border-amber-100 px-3 py-1 text-xs text-amber-700">
            Skip if {{ $rule->conditionSensor->name }}
            {{ \App\Models\SensorRule::operatorLabel($rule->condition_operator) }}
            {{ $rule->condition_threshold }}
        </span>
    @endif

    <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 border border-slate-200 px-3 py-1 text-xs text-slate-600">
        Action: {{ ucfirst($rule->action_type) }}
    </span>

    <div class="ml-auto flex gap-2">
        <a href="{{ route('plus.iot.rules.edit', $rule) }}"
           class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit Rule
        </a>
    </div>
</div>

{{-- Log table --}}
@if($logs->isEmpty())
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-8 py-14 text-center">
        <div class="mx-auto mb-4 h-14 w-14 rounded-2xl flex items-center justify-center" style="background:#f8fafc;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <p class="text-sm text-slate-500">No executions recorded yet. The rule will be evaluated every minute once enabled.</p>
    </div>
@else
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wide">
                    <th class="px-5 py-3 text-left">Time</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Summary</th>
                    <th class="px-5 py-3 text-right">Context</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($logs as $log)
                    @php $badge = $log->statusBadge(); @endphp
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3.5 text-slate-600 whitespace-nowrap">
                            <div class="font-medium">{{ $log->evaluated_at->format('d M Y') }}</div>
                            <div class="text-xs text-slate-400">{{ $log->evaluated_at->format('H:i:s') }}</div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center text-xs font-medium px-2.5 py-0.5 rounded-full border {{ $badge['class'] }}">
                                {{ $badge['label'] }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-600 max-w-md">
                            {{ $log->summary }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if($log->context)
                                <button type="button"
                                        onclick="toggleCtx({{ $log->id }})"
                                        class="text-xs text-slate-400 hover:text-slate-600 transition underline">
                                    details
                                </button>
                                <div id="ctx-{{ $log->id }}" class="hidden mt-2 text-left">
                                    <div class="rounded-lg bg-slate-900 text-emerald-400 text-xs font-mono p-3 max-w-xs ml-auto leading-relaxed">
                                        @foreach($log->context as $k => $v)
                                            <div><span class="text-slate-400">{{ $k }}:</span>
                                            {{ is_bool($v) ? ($v ? 'true' : 'false') : ($v ?? 'null') }}</div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <span class="text-xs text-slate-300">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="mt-6">{{ $logs->links() }}</div>
    @endif
@endif

<script>
function toggleCtx(id) {
    const el = document.getElementById('ctx-' + id);
    if (el) el.classList.toggle('hidden');
}
</script>

@endsection
