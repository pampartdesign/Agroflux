@extends('layouts.app')

@section('content')

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
            <a href="{{ route('plus.iot.dashboard') }}" class="hover:text-emerald-700 transition">IoT Dashboard</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-900 font-medium">IoT Configuration</span>
        </div>
        <h1 class="text-2xl font-semibold text-slate-900">Sensors</h1>
        <p class="text-sm text-slate-500 mt-1">Manage your registered sensors grouped by type.</p>
    </div>
    <a href="{{ route('plus.iot.sensors.create') }}"
       class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm flex-shrink-0">
        + Add Sensor
    </a>
</div>

@if(session('status'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        ✓ {{ session('status') }}
    </div>
@endif

@if($sensors->isEmpty())
    <div class="rounded-2xl border border-slate-100 bg-white p-12 text-center">
        <div class="h-12 w-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center mx-auto mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-slate-700">No sensors yet</p>
        <p class="text-xs text-slate-400 mt-1">Add your first sensor to start collecting readings.</p>
        <a href="{{ route('plus.iot.sensors.create') }}"
           class="mt-4 inline-flex items-center gap-2 h-9 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
            + Add Sensor
        </a>
    </div>
@else
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-emerald-100 bg-emerald-50/50">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Group</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Name</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Unit</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Identifier</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($sensors as $s)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-slate-100 text-xs font-medium text-slate-600">
                                {{ $s->group_key }}
                            </span>
                        </td>
                        <td class="px-5 py-3 font-medium text-slate-900">{{ $s->name }}</td>
                        <td class="px-5 py-3">
                            @if($s->status === 'online')
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Online
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span> Offline
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-slate-500 font-mono text-xs">{{ $s->unit ?? '—' }}</td>
                        <td class="px-5 py-3 text-slate-400 font-mono text-xs truncate max-w-[160px]">{{ $s->identifier ?? '—' }}</td>
                        <td class="px-5 py-3 text-right">
                            <button type="button"
                                    onclick="openSensorEdit({{ $s->id }}, '{{ addslashes($s->name) }}', '{{ $s->status }}')"
                                    class="h-7 px-3 rounded-lg border border-slate-200 text-xs font-medium text-slate-500 hover:bg-slate-50 hover:text-emerald-700 hover:border-emerald-200 transition">
                                Edit
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $sensors->links() }}</div>
@endif

{{-- Edit Sensor Modal --}}
<div id="editSensorModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="font-semibold text-slate-900">Edit Sensor</div>
            <button type="button" onclick="document.getElementById('editSensorModal').classList.add('hidden')"
                    class="h-8 w-8 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition text-xl leading-none">×</button>
        </div>
        <form id="editSensorForm" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Sensor Name <span class="text-red-500">*</span></label>
                <input type="text" id="editSensorName" name="name" required
                       class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Status</label>
                <select id="editSensorStatus" name="status"
                        class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    <option value="online">Online</option>
                    <option value="offline">Offline</option>
                </select>
            </div>
            <div class="pt-2 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('editSensorModal').classList.add('hidden')"
                        class="h-10 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition">Cancel</button>
                <button type="submit"
                        class="h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openSensorEdit(id, name, status) {
    document.getElementById('editSensorName').value   = name;
    document.getElementById('editSensorStatus').value = status;
    document.getElementById('editSensorForm').action  = '/plus/iot/sensors/' + id;
    document.getElementById('editSensorModal').classList.remove('hidden');
}
</script>

{{-- ── Sensor Rules & Conditions ──────────────────────────────────────────── --}}
<div class="mt-10">

    {{-- Section header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Sensor Rules & Conditions</h2>
            <p class="text-sm text-slate-500 mt-0.5">
                Automate actions based on sensor readings or schedules — with weather &amp; sensor override conditions.
            </p>
        </div>
        <a href="{{ route('plus.iot.rules.create') }}"
           class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Add Condition
        </a>
    </div>

    @if($rules->isEmpty())
        {{-- Empty state --}}
        <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-8 py-12 text-center">
            <div class="mx-auto mb-3 h-12 w-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-700">No rules yet</p>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                Create your first rule to automate actions — for example, hold irrigation when rain is forecast, or alert when a sensor crosses a threshold.
            </p>
            <a href="{{ route('plus.iot.rules.create') }}"
               class="mt-5 inline-flex items-center gap-2 h-9 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
                + Add Condition
            </a>
        </div>

    @else
        {{-- Rules table --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-x-auto">
            <table class="w-full text-sm min-w-[780px]">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wide">
                        <th class="px-3 py-3 w-5"></th>
                        <th class="px-3 py-3 text-left">Name</th>
                        <th class="px-3 py-3 text-left">Trigger</th>
                        <th class="px-3 py-3 text-left">Conditions</th>
                        <th class="px-3 py-3 text-left">Action</th>
                        <th class="px-3 py-3 text-left">Last Run</th>
                        <th class="px-3 py-3 w-24"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($rules as $rule)
                        @php
                            $lastLog      = $lastLogs[$rule->id] ?? null;
                            $badge        = $lastLog ? $lastLog->statusBadge() : null;
                            $actionColors = [
                                'log'    => 'bg-slate-100 text-slate-600 border-slate-200',
                                'notify' => 'bg-violet-50 text-violet-700 border-violet-100',
                                'both'   => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">

                            {{-- Active status dot --}}
                            <td class="px-3 py-3 text-center">
                                <span title="{{ $rule->is_active ? 'Active' : 'Disabled' }}"
                                      class="inline-block h-2 w-2 rounded-full {{ $rule->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                            </td>

                            {{-- Name (description as tooltip only) --}}
                            <td class="px-3 py-3 max-w-[180px]">
                                <div class="font-medium text-slate-900 leading-snug truncate"
                                     title="{{ $rule->description ?? $rule->name }}">{{ $rule->name }}</div>
                            </td>

                            {{-- Trigger --}}
                            <td class="px-3 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 rounded-lg bg-blue-50 border border-blue-100 px-2 py-0.5 text-xs text-blue-700 font-medium">
                                    @if($rule->trigger_type === 'time')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                        </svg>
                                    @endif
                                    {{ $rule->triggerLabel() }}
                                </span>
                            </td>

                            {{-- Conditions --}}
                            <td class="px-3 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @if($rule->weather_condition_enabled)
                                        <span class="inline-flex items-center gap-0.5 rounded-lg bg-sky-50 border border-sky-100 px-2 py-0.5 text-xs text-sky-700 whitespace-nowrap">
                                            🌧 ≥{{ $rule->weather_rain_skip_pct }}%
                                        </span>
                                    @endif
                                    @if($rule->sensor_condition_enabled && $rule->conditionSensor)
                                        <span class="inline-flex items-center rounded-lg bg-amber-50 border border-amber-100 px-2 py-0.5 text-xs text-amber-700 max-w-[140px] truncate"
                                              title="{{ $rule->conditionSensor->name }} {{ \App\Models\SensorRule::operatorLabel($rule->condition_operator) }} {{ $rule->condition_threshold }}">
                                            {{ $rule->conditionSensor->name }} {{ \App\Models\SensorRule::operatorLabel($rule->condition_operator) }} {{ $rule->condition_threshold }}
                                        </span>
                                    @endif
                                    @if(!$rule->weather_condition_enabled && !($rule->sensor_condition_enabled && $rule->conditionSensor))
                                        <span class="text-xs text-slate-300">—</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Action --}}
                            <td class="px-3 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-lg border px-2 py-0.5 text-xs font-medium {{ $actionColors[$rule->action_type] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                    {{ ucfirst($rule->action_type) }}
                                </span>
                            </td>

                            {{-- Last run --}}
                            <td class="px-3 py-3 whitespace-nowrap">
                                @if($badge)
                                    <span class="inline-flex items-center text-xs font-medium px-2 py-0.5 rounded-full border {{ $badge['class'] }}">
                                        {{ $badge['label'] }}
                                    </span>
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $lastLog->evaluated_at->diffForHumans() }}</div>
                                @else
                                    <span class="text-xs text-slate-300">—</span>
                                @endif
                            </td>

                            {{-- Row actions (compact text links) --}}
                            <td class="px-3 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('plus.iot.rules.logs', $rule) }}"
                                   class="text-xs text-slate-400 hover:text-slate-700 transition">Logs</a>
                                <span class="text-slate-200 mx-1">·</span>
                                <a href="{{ route('plus.iot.rules.edit', $rule) }}"
                                   class="text-xs text-slate-400 hover:text-emerald-700 transition">Edit</a>
                                <span class="text-slate-200 mx-1">·</span>
                                <form method="POST" action="{{ route('plus.iot.rules.destroy', $rule) }}"
                                      class="inline"
                                      onsubmit="return confirm('Delete rule \'{{ addslashes($rule->name) }}\'?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition">Del</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Quick link to full rules management page --}}
        <div class="mt-3 text-right">
            <a href="{{ route('plus.iot.rules.index') }}"
               class="text-xs text-slate-400 hover:text-emerald-700 transition underline underline-offset-2">
                Manage all rules →
            </a>
        </div>
    @endif

</div>

@endsection
