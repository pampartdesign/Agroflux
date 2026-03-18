@extends('layouts.app')

@section('title', __('drone.field_maps_title'))

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('drone.field_maps_title') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('drone.field_maps_subtitle') }}</p>
        </div>
        <a href="{{ route('drone.fields.map') }}"
           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('drone.btn_draw_new_field') }}
        </a>
    </div>

    {{-- Session messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    @if($boundaries->isEmpty())
        <div class="bg-white border border-dashed border-gray-300 rounded-xl p-16 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 13l4.553 2.276A1 1 0 0021 21.382V10.618a1 1 0 00-.553-.894L15 7m0 13V7m0 0L9 7"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">{{ __('drone.no_fields_title') }}</h3>
            <p class="text-gray-400 text-sm mb-6">{{ __('drone.no_fields_desc') }}</p>
            <a href="{{ route('drone.fields.map') }}"
               class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('drone.btn_draw_first_field') }}
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($boundaries as $boundary)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition overflow-hidden">
                {{-- Colour thumbnail --}}
                <div class="h-32 bg-gradient-to-br from-green-100 to-emerald-200 relative flex items-center justify-center">
                    <svg class="w-10 h-10 text-green-400 opacity-60" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 13l4.553 2.276A1 1 0 0021 21.382V10.618a1 1 0 00-.553-.894L15 7m0 13V7m0 0L9 7"/>
                    </svg>
                    @if($boundary->area_ha)
                        <span class="absolute top-2 right-2 bg-white/80 text-green-800 text-xs font-semibold px-2 py-0.5 rounded-full">
                            {{ number_format($boundary->area_ha, 2) }} ha
                        </span>
                    @endif
                </div>

                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 text-base truncate">{{ $boundary->name }}</h3>

                    @if($boundary->notes)
                        <p class="text-gray-500 text-xs mt-0.5 line-clamp-2">{{ $boundary->notes }}</p>
                    @endif

                    <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-gray-500">
                        @if($boundary->perimeter_m)
                            <div class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h16v16H4z"/>
                                </svg>
                                {{ number_format($boundary->perimeter_m) }} {{ __('drone.perimeter_label') }}
                            </div>
                        @endif
                        @if($boundary->centroid_lat && $boundary->centroid_lng)
                            <div class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ number_format($boundary->centroid_lat, 4) }}, {{ number_format($boundary->centroid_lng, 4) }}
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 flex items-center gap-2">
                        <a href="{{ route('drone.fields.map.edit', $boundary) }}"
                           class="flex-1 text-center text-xs font-medium bg-green-50 hover:bg-green-100 text-green-700 px-3 py-2 rounded-lg transition">
                            {{ __('drone.btn_edit_on_map') }}
                        </a>
                        <a href="{{ route('drone.missions.plan', ['field_boundary_id' => $boundary->id]) }}"
                           class="flex-1 text-center text-xs font-medium bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-2 rounded-lg transition">
                            {{ __('drone.btn_plan_mission_field') }}
                        </a>
                        <form method="POST" action="{{ route('drone.fields.destroy', $boundary) }}"
                              onsubmit="return confirm('{{ __('drone.confirm_delete_field') }}')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs font-medium bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded-lg transition">
                                {{ __('app.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
