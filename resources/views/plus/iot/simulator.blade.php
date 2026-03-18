@extends('layouts.app')

@section('content')

{{-- Page header --}}
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
            <a href="{{ route('plus.iot.dashboard') }}" class="hover:text-emerald-700 transition">IoT Dashboard</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-900 font-medium">IoT Simulator</span>
        </div>
        <h1 class="text-2xl font-semibold text-slate-900">IoT Simulator</h1>
        <p class="text-sm text-slate-500 mt-1">Log a sensor reading manually or simulate a ping to verify connectivity.</p>
    </div>
    <a href="{{ route('plus.iot.dashboard') }}"
       class="inline-flex items-center gap-2 h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm text-slate-600 flex-shrink-0">
        ← Back to Dashboard
    </a>
</div>

@if(session('success'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        ✓ {{ session('success') }}
    </div>
@endif

<div class="max-w-2xl space-y-5">

    {{-- ── Manual data entry ── --}}
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-emerald-100 flex items-center gap-3">
            <div class="h-8 w-8 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <div class="font-semibold text-sm text-slate-900">Log a Sensor Reading</div>
                <div class="text-xs text-slate-400 mt-0.5">Used when a sensor cannot push automatically, or as a fallback</div>
            </div>
        </div>

        <form method="POST" action="{{ route('plus.iot.manual.store') }}" class="px-6 py-5 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Sensor <span class="text-red-500">*</span></label>
                <select name="sensor_id"
                        class="w-full h-10 px-3 rounded-xl border @error('sensor_id') border-red-400 @else border-slate-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    <option value="">— Select a sensor —</option>
                    @foreach($sensors as $s)
                        <option value="{{ $s->id }}" @selected(old('sensor_id') == $s->id)>
                            {{ $s->name }} ({{ $s->group_key }})
                        </option>
                    @endforeach
                </select>
                @error('sensor_id')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Value <span class="text-red-500">*</span></label>
                    <input name="value"
                           value="{{ old('value') }}"
                           type="text"
                           class="w-full h-10 px-3 rounded-xl border @error('value') border-red-400 @else border-slate-200 @enderror bg-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-emerald-200"
                           placeholder="e.g. 72.4">
                    @error('value')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Recorded at <span class="text-red-500">*</span></label>
                    <input name="recorded_at"
                           value="{{ old('recorded_at') }}"
                           type="datetime-local"
                           class="w-full h-10 px-3 rounded-xl border @error('recorded_at') border-red-400 @else border-slate-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    @error('recorded_at')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100">
                <button type="submit"
                        class="inline-flex items-center gap-2 h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Reading
                </button>
            </div>
        </form>
    </div>

    {{-- ── Connectivity simulator / ping ── --}}
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
            <div class="h-8 w-8 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
            </div>
            <div>
                <div class="font-semibold text-sm text-slate-900">Connectivity Simulator</div>
                <div class="text-xs text-slate-400 mt-0.5">Sends a simulated ping — marks sensor online and injects a random test reading</div>
            </div>
        </div>

        <form method="POST" action="{{ route('plus.iot.ping') }}" class="px-6 py-5 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Sensor to ping</label>
                <select name="sensor_id"
                        class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <option value="">— Select a sensor —</option>
                    @foreach($sensors as $s)
                        <option value="{{ $s->id }}">
                            {{ $s->name }} ({{ $s->group_key }}) — {{ $s->status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center gap-3">
                <button type="submit"
                        class="inline-flex items-center gap-2 h-10 px-5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 text-sm font-medium hover:bg-slate-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Send Ping
                </button>
                <span class="text-xs text-slate-400">Injects a random reading and marks sensor as online</span>
            </div>
        </form>
    </div>

</div>

@endsection
