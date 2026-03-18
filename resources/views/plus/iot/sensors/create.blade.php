@extends('layouts.app')

@section('content')

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
            <a href="{{ route('plus.iot.dashboard') }}" class="hover:text-emerald-700 transition">IoT Dashboard</a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('plus.iot.sensors.index') }}" class="hover:text-emerald-700 transition">Sensors</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-900 font-medium">Add Sensor</span>
        </div>
        <h1 class="text-2xl font-semibold text-slate-900">Add Sensor</h1>
        <p class="text-sm text-slate-500 mt-1">Register a new sensor to your IoT monitoring setup.</p>
    </div>
    <a href="{{ route('plus.iot.sensors.index') }}"
       class="inline-flex items-center gap-2 h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm text-slate-600 flex-shrink-0">
        ← Back to Sensors
    </a>
</div>

<div class="max-w-2xl">
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-emerald-100 flex items-center gap-3">
            <div class="h-8 w-8 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
            </div>
            <div class="font-semibold text-sm text-slate-900">Sensor Details</div>
        </div>

        <form method="POST" action="{{ route('plus.iot.sensors.store') }}" class="px-6 py-5 space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Sensor type <span class="text-red-500">*</span></label>
                <select name="group_key"
                        class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    <option value="irrigation"   @selected(old('group_key') === 'irrigation')>Irrigation Sensor</option>
                    <option value="humidity"     @selected(old('group_key') === 'humidity')>Humidity Sensor</option>
                    <option value="temperature"  @selected(old('group_key') === 'temperature')>Temperature Sensor</option>
                    <option value="trough_level" @selected(old('group_key') === 'trough_level')>Water Trough Level</option>
                    <option value="rfid"         @selected(old('group_key') === 'rfid')>RFID (Hydration / Feeding)</option>
                    <option value="barn_climate" @selected(old('group_key') === 'barn_climate')>Barn Climate</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Name <span class="text-red-500">*</span></label>
                <input name="name"
                       value="{{ old('name') }}"
                       type="text"
                       class="w-full h-10 px-3 rounded-xl border @error('name') border-red-400 @else border-slate-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
                       placeholder="e.g. Field 3 Irrigation">
                @error('name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Identifier</label>
                    <input name="identifier"
                           value="{{ old('identifier') }}"
                           type="text"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-emerald-200"
                           placeholder="serial / mac / device id">
                    <p class="mt-1 text-xs text-slate-400">Hardware serial or MAC address</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Unit</label>
                    <input name="unit"
                           value="{{ old('unit') }}"
                           type="text"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
                           placeholder="%, °C, L, …">
                    <p class="mt-1 text-xs text-slate-400">Displayed next to readings</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Initial status</label>
                <select name="status"
                        class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    <option value="offline" @selected(old('status', 'offline') === 'offline')>Offline</option>
                    <option value="online"  @selected(old('status') === 'online')>Online</option>
                </select>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                <button type="submit"
                        class="inline-flex items-center gap-2 h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Sensor
                </button>
                <a href="{{ route('plus.iot.sensors.index') }}"
                   class="inline-flex items-center h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm text-slate-600">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
