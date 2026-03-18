@extends('admin._layout')

@section('page_title', 'New Subscription Plan')
@section('page_subtitle', 'Define the plan name, pricing, and which modules are included.')

@section('page_actions')
    <a href="{{ route('admin.plans.index') }}"
       class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-gray-600 text-sm hover:bg-gray-50 transition">
        ← Back to Plans
    </a>
@endsection

@section('page_content')

@php
$moduleGroups = [
    'AgroFlux Core Modules' => [
        'core'         => 'Core (base system)',
        'farm'         => 'Farm Management',
        'livestock'    => 'Livestock Management',
        'water'        => 'Water Management',
        'traceability' => 'Traceability & QR',
        'inventory'    => 'Inventory',
        'equipment'    => 'Equipment',
        'iot_sim'      => 'IoT Simulator (demo — no real sensors)',
    ],
    'AgroFlux Plus Modules' => [
        'iot'  => 'IoT Real Sensors & Dashboard',
    ],
    'LogiTrace (Standalone — Delivery & Logistics)' => [
        'logi' => 'Logistics & Delivery',
    ],
    'AgroFlux Drone (Full Suite — Core + Plus + Logi + Drone)' => [
        'drone' => 'Drones & Field Mapping',
    ],
];
@endphp

<form method="POST" action="{{ route('admin.plans.store') }}" class="max-w-2xl space-y-5">
    @csrf

    {{-- Basic info --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 space-y-4">
        <div class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Plan Info</div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">
                    Plan Name <span class="text-red-500">*</span>
                </label>
                <input name="name" value="{{ old('name') }}" required
                       class="w-full h-9 px-3 rounded-lg border @error('name') border-red-400 @else border-gray-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
                       placeholder="e.g. AgroFlux Core">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">
                    Plan Key <span class="text-red-500">*</span>
                    <span class="font-normal text-gray-400">(lowercase, no spaces)</span>
                </label>
                <input name="key" value="{{ old('key') }}" required
                       class="w-full h-9 px-3 rounded-lg border @error('key') border-red-400 @else border-gray-200 @enderror bg-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-emerald-200"
                       placeholder="e.g. core">
                @error('key')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Description</label>
            <textarea name="description" rows="2"
                      class="w-full px-3 py-2 rounded-lg border @error('description') border-red-400 @else border-gray-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
                      placeholder="Short description shown to users…">{{ old('description') }}</textarea>
            @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Price (€)</label>
                <input name="price" type="number" step="0.01" min="0" value="{{ old('price') }}"
                       class="w-full h-9 px-3 rounded-lg border @error('price') border-red-400 @else border-gray-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
                       placeholder="0.00">
                @error('price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Billing Cycle</label>
                <select name="billing_cycle"
                        class="w-full h-9 px-3 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    <option value="">— None —</option>
                    <option value="monthly"  @selected(old('billing_cycle') === 'monthly')>Monthly</option>
                    <option value="yearly"   @selected(old('billing_cycle') === 'yearly')>Yearly</option>
                    <option value="custom"   @selected(old('billing_cycle') === 'custom')>Custom</option>
                </select>
                @error('billing_cycle')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex items-center gap-3">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-200">
                <span class="text-sm text-gray-700 font-medium">Plan is active</span>
            </label>
        </div>
    </div>

    {{-- Module toggles --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">Included Modules</div>
        @error('modules')<p class="mb-3 text-xs text-red-600">{{ $message }}</p>@enderror

        @foreach($moduleGroups as $groupLabel => $modules)
            <div class="mb-5">
                <div class="text-xs font-semibold text-gray-500 mb-2">{{ $groupLabel }}</div>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($modules as $key => $label)
                        <label class="flex items-center gap-2.5 cursor-pointer rounded-lg border border-gray-100 bg-gray-50 hover:bg-emerald-50 px-3 py-2.5 transition">
                            <input type="checkbox" name="modules[]" value="{{ $key }}"
                                   {{ in_array($key, old('modules', [])) ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-200">
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
                class="inline-flex items-center gap-2 h-10 px-6 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
            Create Plan
        </button>
        <a href="{{ route('admin.plans.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
    </div>
</form>

@endsection
