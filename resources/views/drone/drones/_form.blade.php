{{-- Drone form partial — used in add/edit modals --}}
<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Name --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('drone.label_drone_name') }} <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $drone->name ?? '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                   placeholder="{{ __('drone.placeholder_drone_name') }}" required>
        </div>

        {{-- Model --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('drone.label_model') }}</label>
            <input type="text" name="model" value="{{ old('model', $drone->model ?? '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                   placeholder="{{ __('drone.placeholder_model') }}">
        </div>

        {{-- Serial Number --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('drone.label_serial_number') }}</label>
            <input type="text" name="serial_number" value="{{ old('serial_number', $drone->serial_number ?? '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                   placeholder="{{ __('drone.placeholder_serial') }}">
        </div>

        {{-- Status --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('drone.label_drone_status') }}</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                @foreach(['active' => __('drone.drone_status_active'), 'maintenance' => __('drone.drone_status_maintenance'), 'retired' => __('drone.drone_status_retired')] as $val => $label)
                    <option value="{{ $val }}" {{ old('status', $drone->status ?? 'active') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Default Flight Parameters --}}
    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
        <h4 class="text-sm font-semibold text-gray-700 mb-3">{{ __('drone.label_default_params') }}</h4>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">

            {{-- Altitude --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('drone.label_altitude_m') }}</label>
                <input type="number" name="default_altitude_m" min="10" max="500" step="1"
                       value="{{ old('default_altitude_m', $drone->default_altitude_m ?? 50) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       required>
            </div>

            {{-- Speed --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('drone.label_speed_ms') }}</label>
                <input type="number" name="default_speed_ms" min="0.5" max="30" step="0.5"
                       value="{{ old('default_speed_ms', $drone->default_speed_ms ?? 8) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       required>
            </div>

            {{-- Overlap --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('drone.label_overlap_pct') }}</label>
                <input type="number" name="default_overlap_pct" min="0" max="95" step="5"
                       value="{{ old('default_overlap_pct', $drone->default_overlap_pct ?? 70) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       required>
            </div>

            {{-- Strip Spacing --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('drone.label_spacing_m') }}</label>
                <input type="number" name="default_spacing_m" min="1" max="100" step="1"
                       value="{{ old('default_spacing_m', $drone->default_spacing_m ?? 20) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       required>
            </div>

            {{-- Buffer --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('drone.label_buffer_m') }}</label>
                <input type="number" name="default_buffer_m" min="0" max="50" step="1"
                       value="{{ old('default_buffer_m', $drone->default_buffer_m ?? 5) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       required>
            </div>
        </div>
    </div>

    {{-- Notes --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('drone.label_notes') }}</label>
        <textarea name="notes" rows="2"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  placeholder="{{ __('drone.placeholder_notes') }}">{{ old('notes', $drone->notes ?? '') }}</textarea>
    </div>
</div>
