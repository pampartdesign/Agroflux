{{-- resources/views/core/farms/_form.blade.php --}}
@php
  $farm = $farm ?? null;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    <div class="lg:col-span-1">
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('farm.label_farm_name') }}</label>
        <input name="name" type="text"
               value="{{ old('name', $farm->name ?? '') }}"
               class="w-full h-11 rounded-xl border border-slate-200 px-4 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-200"
               placeholder="{{ __('farm.placeholder_farm_name') }}">
        @error('name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="lg:col-span-1">
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('farm.label_city_location') }}</label>
        <input name="city" type="text"
               value="{{ old('city', $farm->city ?? '') }}"
               class="w-full h-11 rounded-xl border border-slate-200 px-4 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-200"
               placeholder="{{ __('farm.placeholder_city') }}">
        @error('city') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="lg:col-span-1">
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('farm.label_area_hectares') }}</label>
        <input name="area_ha" type="number" step="0.01"
               value="{{ old('area_ha', $farm->area_ha ?? '') }}"
               class="w-full h-11 rounded-xl border border-slate-200 px-4 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-200"
               placeholder="{{ __('farm.placeholder_area') }}">
        @error('area_ha') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="lg:col-span-1">
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('app.notes') }}</label>
        <input name="notes" type="text"
               value="{{ old('notes', $farm->notes ?? '') }}"
               class="w-full h-11 rounded-xl border border-slate-200 px-4 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-200"
               placeholder="{{ __('farm.placeholder_optional') }}">
        @error('notes') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>
</div>
