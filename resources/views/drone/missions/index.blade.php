@extends('layouts.app')

@section('title', __('drone.missions_title'))

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('drone.missions_title') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('drone.missions_subtitle') }}</p>
        </div>
        <a href="{{ route('drone.missions.plan') }}"
           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('drone.btn_new_mission') }}
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    @if($missions->isEmpty())
        <div class="bg-white border border-dashed border-gray-300 rounded-xl p-16 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">{{ __('drone.no_missions_title') }}</h3>
            <p class="text-gray-400 text-sm mb-6">{{ __('drone.no_missions_desc') }}</p>
            <a href="{{ route('drone.missions.plan') }}"
               class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('drone.btn_create_first_mission') }}
            </a>
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700">{{ __('drone.col_mission') }}</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">{{ __('drone.col_field') }}</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">{{ __('drone.col_drone') }}</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">{{ __('drone.col_type') }}</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">{{ __('drone.col_mission_status') }}</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">{{ __('drone.col_planned') }}</th>
                        <th class="text-right px-5 py-3 font-semibold text-gray-700">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($missions as $mission)
                    @php
                        $color = match($mission->statusColor()) {
                            'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                            'blue'    => 'bg-blue-50 text-blue-700 border-blue-100',
                            'amber'   => 'bg-amber-50 text-amber-700 border-amber-100',
                            'red'     => 'bg-red-50 text-red-700 border-red-100',
                            default   => 'bg-gray-50 text-gray-600 border-gray-100',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3.5">
                            <div class="font-medium text-gray-900">{{ $mission->name }}</div>
                            @if($mission->altitude_m || $mission->speed_ms)
                                <div class="text-xs text-gray-400 mt-0.5">
                                    @if($mission->altitude_m){{ $mission->altitude_m }}m @endif
                                    @if($mission->speed_ms)· {{ $mission->speed_ms }}m/s @endif
                                    @if($mission->overlap_pct)· {{ $mission->overlap_pct }}% overlap @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-gray-700">{{ $mission->boundary->name ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-gray-700">{{ $mission->drone->name ?? '—' }}</td>
                        <td class="px-4 py-3.5">
                            <span class="text-xs bg-blue-50 text-blue-700 border border-blue-100 px-2 py-0.5 rounded-full font-medium">
                                {{ $mission->missionTypeLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium border {{ $color }}">
                                {{ ucfirst(str_replace('_', ' ', $mission->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-gray-500 text-xs">
                            {{ $mission->planned_at ? $mission->planned_at->format('d M Y H:i') : '—' }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('drone.missions.plan.edit', $mission) }}"
                                   class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2.5 py-1.5 rounded-lg transition font-medium">
                                    {{ __('drone.btn_plan') }}
                                </a>
                                <a href="{{ route('drone.missions.export', [$mission->id, 'geojson']) }}"
                                   class="text-xs bg-gray-50 hover:bg-gray-100 text-gray-700 px-2.5 py-1.5 rounded-lg transition font-medium">
                                    GeoJSON
                                </a>
                                <a href="{{ route('drone.missions.export', [$mission->id, 'kml']) }}"
                                   class="text-xs bg-gray-50 hover:bg-gray-100 text-gray-700 px-2.5 py-1.5 rounded-lg transition font-medium">
                                    KML
                                </a>

                                {{-- Status & Delete dropdown --}}
                                <div class="relative" x-data="{ open: false, top: 0, left: 0 }">
                                    <button @click="open = !open; $nextTick(() => { let r = $el.getBoundingClientRect(); top = r.bottom + 4; left = r.right - 176; })"
                                            class="text-xs bg-gray-50 hover:bg-gray-100 text-gray-700 px-2.5 py-1.5 rounded-lg transition font-medium">
                                        ···
                                    </button>
                                    <div x-show="open" @click.away="open = false"
                                         :style="`position:fixed; top:${top}px; left:${left}px; z-index:9999`"
                                         class="w-44 bg-white border border-gray-200 rounded-lg shadow-lg py-1 text-xs">
                                        @foreach(['planned' => __('drone.mark_planned'), 'in_progress' => __('drone.mark_in_progress'),
                                                  'completed' => __('drone.mark_completed'), 'aborted' => __('drone.mark_aborted')] as $s => $label)
                                        <form method="POST" action="{{ route('drone.missions.status', $mission) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $s }}">
                                            <button type="submit"
                                                    class="w-full text-left px-4 py-1.5 hover:bg-gray-50 text-gray-700">
                                                {{ $label }}
                                            </button>
                                        </form>
                                        @endforeach
                                        <div class="border-t border-gray-100 mt-1 pt-1">
                                            <form method="POST" action="{{ route('drone.missions.destroy', $mission) }}"
                                                  onsubmit="return confirm('{{ __('drone.confirm_delete_mission') }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="w-full text-left px-4 py-1.5 hover:bg-red-50 text-red-600">
                                                    {{ __('drone.btn_delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
