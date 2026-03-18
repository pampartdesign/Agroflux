@extends('layouts.app')
@section('content')

<div class="flex items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('drone.config_title') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('drone.config_subtitle') }}</p>
    </div>
    <button onclick="document.getElementById('addDroneModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 h-10 px-4 rounded-xl text-sm font-medium text-white shadow-sm transition"
            style="background:#059669;">
        {{ __('drone.btn_register_drone') }}
    </button>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    {{ session('success') }}
</div>
@endif

@if($drones->isEmpty())
<div class="rounded-2xl border border-slate-100 bg-white shadow-sm py-16 text-center text-slate-400">
    <div class="text-4xl mb-3">🚁</div>
    <p class="text-sm font-medium text-slate-600 mb-1">{{ __('drone.no_drones_title') }}</p>
    <p class="text-xs">{{ __('drone.no_drones_desc') }}</p>
</div>
@else
<div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-100 bg-slate-50/60">
                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('drone.col_drone') }}</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">{{ __('drone.col_model_serial') }}</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('drone.col_status') }}</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden lg:table-cell">{{ __('drone.col_default_alt') }}</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden lg:table-cell">{{ __('drone.col_speed') }}</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden lg:table-cell">{{ __('drone.col_overlap') }}</th>
                <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('drone.col_actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($drones as $drone)
            @php
                $bgMap  = ['active'=>'#f0fdf4','maintenance'=>'#fffbeb','retired'=>'#f8fafc'];
                $txtMap = ['active'=>'#166534','maintenance'=>'#92400e','retired'=>'#475569'];
            @endphp
            <tr class="border-b border-slate-50 hover:bg-slate-50/60 transition">
                <td class="px-5 py-3">
                    <p class="font-medium text-slate-900">{{ $drone->name }}</p>
                    @if($drone->notes)
                    <p class="text-xs text-slate-400 truncate max-w-xs">{{ Str::limit($drone->notes, 50) }}</p>
                    @endif
                </td>
                <td class="px-4 py-3 text-slate-500 hidden md:table-cell">
                    {{ $drone->model ?? '—' }}<br>
                    <span class="text-xs text-slate-400">{{ $drone->serial_number ?? '—' }}</span>
                </td>
                <td class="px-4 py-3">
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                          style="background:{{ $bgMap[$drone->status] ?? '#f8fafc' }};color:{{ $txtMap[$drone->status] ?? '#475569' }};">
                        {{ $drone->statusLabel() }}
                    </span>
                </td>
                <td class="px-4 py-3 text-slate-600 hidden lg:table-cell">{{ $drone->default_altitude_m }}m</td>
                <td class="px-4 py-3 text-slate-600 hidden lg:table-cell">{{ $drone->default_speed_ms }}m/s</td>
                <td class="px-4 py-3 text-slate-600 hidden lg:table-cell">{{ $drone->default_overlap_pct }}%</td>
                <td class="px-5 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick='openEditDrone({{ json_encode($drone) }})'
                                class="text-xs h-8 px-3 rounded-lg border border-slate-200 hover:bg-slate-50 transition text-slate-600">
                            {{ __('app.edit') }}
                        </button>
                        <form method="POST" action="{{ route('drone.drones.destroy', $drone) }}"
                              onsubmit="return confirm('{{ __('drone.confirm_delete_drone') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs h-8 px-3 rounded-lg border border-red-100 hover:bg-red-50 transition text-red-500">
                                {{ __('app.delete') }}
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Add Drone Modal --}}
<div id="addDroneModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-900">{{ __('drone.modal_register_drone') }}</h3>
            <button onclick="document.getElementById('addDroneModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600 transition text-xl leading-none">✕</button>
        </div>
        <form method="POST" action="{{ route('drone.drones.store') }}" class="p-6 space-y-4">
            @csrf
            @include('drone.drones._form', ['drone' => null])
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="h-10 px-6 rounded-xl text-white text-sm font-medium shadow-sm transition" style="background:#059669;">{{ __('drone.btn_register_submit') }}</button>
                <button type="button" onclick="document.getElementById('addDroneModal').classList.add('hidden')"
                        class="h-10 px-5 rounded-xl border border-slate-200 bg-white text-sm text-slate-600 hover:bg-slate-50 transition">{{ __('app.cancel') }}</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Drone Modal --}}
<div id="editDroneModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-900">{{ __('drone.modal_edit_drone') }}</h3>
            <button onclick="document.getElementById('editDroneModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600 transition text-xl leading-none">✕</button>
        </div>
        <form id="editDroneForm" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            @include('drone.drones._form', ['drone' => null])
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="h-10 px-6 rounded-xl text-white text-sm font-medium shadow-sm transition" style="background:#059669;">{{ __('drone.btn_save_changes') }}</button>
                <button type="button" onclick="document.getElementById('editDroneModal').classList.add('hidden')"
                        class="h-10 px-5 rounded-xl border border-slate-200 bg-white text-sm text-slate-600 hover:bg-slate-50 transition">{{ __('app.cancel') }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEditDrone(drone) {
    const f = document.getElementById('editDroneForm');
    f.action = '/drone/drones/' + drone.id;
    const fields = ['name','model','serial_number','status','default_altitude_m','default_speed_ms','default_overlap_pct','default_spacing_m','default_buffer_m','notes'];
    fields.forEach(k => {
        const el = f.querySelector('[name="'+k+'"]');
        if (el) el.value = drone[k] ?? '';
    });
    document.getElementById('editDroneModal').classList.remove('hidden');
}
</script>
@endpush

@endsection
