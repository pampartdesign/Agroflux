{{--
    Shared rule form partial.
    $rule    → SensorRule|null   (null on create)
    $sensors → Collection<Sensor>
    $action  → form action URL
    $method  → PUT | POST
--}}

<form method="POST" action="{{ $action }}" id="rule-form" class="space-y-6">
    @csrf
    @if($method === 'PUT') @method('PUT') @endif

    {{-- ── 1. Basic Info ─────────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
            <div class="h-6 w-6 rounded-full bg-slate-800 text-white text-xs flex items-center justify-center font-bold">1</div>
            <span class="text-sm font-semibold text-slate-800">Rule Details</span>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1.5">Rule name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $rule?->name) }}" required
                       placeholder="e.g. Morning Irrigation Guard"
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-3 md:pt-6">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" id="is_active"
                           {{ old('is_active', $rule?->is_active ?? true) ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-emerald-500 rounded-full peer peer-checked:bg-emerald-500 transition-colors"></div>
                    <div class="absolute left-0.5 top-0.5 bg-white w-5 h-5 rounded-full transition-transform peer-checked:translate-x-5 shadow"></div>
                </label>
                <span class="text-sm text-slate-700 font-medium">Active</span>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-slate-700 mb-1.5">Description <span class="text-slate-400">(optional)</span></label>
                <textarea name="description" rows="2" placeholder="What does this rule do?"
                          class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none">{{ old('description', $rule?->description) }}</textarea>
            </div>
        </div>
    </div>

    {{-- ── 2. Trigger ────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
            <div class="h-6 w-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">2</div>
            <span class="text-sm font-semibold text-slate-800">Trigger</span>
            <span class="text-xs text-slate-400">— when should the rule fire?</span>
        </div>
        <div class="px-6 py-5 space-y-5">
            <div class="flex gap-4">
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="trigger_type" value="time"
                           {{ old('trigger_type', $rule?->trigger_type ?? 'time') === 'time' ? 'checked' : '' }}
                           class="sr-only peer" onchange="toggleTrigger()">
                    <div class="rounded-xl border-2 border-slate-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 p-4 transition">
                        <div class="flex items-center gap-2 mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-semibold text-slate-800">Scheduled Time</span>
                        </div>
                        <p class="text-xs text-slate-500">Fire the rule every day at a fixed clock time.</p>
                    </div>
                </label>
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="trigger_type" value="sensor_threshold"
                           {{ old('trigger_type', $rule?->trigger_type) === 'sensor_threshold' ? 'checked' : '' }}
                           class="sr-only peer" onchange="toggleTrigger()">
                    <div class="rounded-xl border-2 border-slate-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 p-4 transition">
                        <div class="flex items-center gap-2 mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span class="text-sm font-semibold text-slate-800">Sensor Threshold</span>
                        </div>
                        <p class="text-xs text-slate-500">Fire when a sensor reading crosses a set value.</p>
                    </div>
                </label>
            </div>

            {{-- Time fields --}}
            <div id="trigger-time-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Trigger time (HH:MM)</label>
                    <input type="time" name="trigger_time" value="{{ old('trigger_time', $rule?->trigger_time) }}"
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('trigger_time')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Sensor threshold fields --}}
            <div id="trigger-sensor-fields" class="grid grid-cols-1 md:grid-cols-3 gap-4 hidden">
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Trigger sensor</label>
                    <select name="trigger_sensor_id"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">— select sensor —</option>
                        @foreach($sensors as $s)
                            <option value="{{ $s->id }}" {{ old('trigger_sensor_id', $rule?->trigger_sensor_id) == $s->id ? 'selected' : '' }}>
                                {{ $s->name }} ({{ $s->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Operator</label>
                    <select name="trigger_operator"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        @foreach(['lt' => 'Less than (<)', 'lte' => 'Less or equal (≤)', 'gt' => 'Greater than (>)', 'gte' => 'Greater or equal (≥)', 'eq' => 'Equal to (=)'] as $v => $l)
                            <option value="{{ $v }}" {{ old('trigger_operator', $rule?->trigger_operator) === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Threshold value</label>
                    <input type="number" step="0.01" name="trigger_threshold"
                           value="{{ old('trigger_threshold', $rule?->trigger_threshold) }}"
                           placeholder="e.g. 30"
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>
    </div>

    {{-- ── 3. Override Conditions ─────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
            <div class="h-6 w-6 rounded-full bg-sky-600 text-white text-xs flex items-center justify-center font-bold">3</div>
            <span class="text-sm font-semibold text-slate-800">Override Conditions</span>
            <span class="text-xs text-slate-400">— these can <em>hold</em> the trigger</span>
        </div>
        <div class="px-6 py-5 space-y-6">

            {{-- Weather condition --}}
            <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                <div class="flex items-center gap-3">
                    <input type="hidden" name="weather_condition_enabled" value="0">
                    <input type="checkbox" id="weather_condition_enabled" name="weather_condition_enabled" value="1"
                           {{ old('weather_condition_enabled', $rule?->weather_condition_enabled) ? 'checked' : '' }}
                           onchange="toggleWeather()"
                           class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="weather_condition_enabled" class="flex items-center gap-2 cursor-pointer select-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                        </svg>
                        <span class="text-sm font-semibold text-slate-800">Weather forecast condition</span>
                    </label>
                </div>
                <p class="mt-1 ml-7 text-xs text-slate-500">Skip this rule if tomorrow.io forecasts rain above a set probability at trigger time.</p>
                <div id="weather-fields" class="mt-4 ml-7 {{ old('weather_condition_enabled', $rule?->weather_condition_enabled) ? '' : 'hidden' }}">
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">
                        Skip trigger if rain probability ≥ <span id="rain-pct-label">{{ old('weather_rain_skip_pct', $rule?->weather_rain_skip_pct ?? 40) }}</span>%
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="range" name="weather_rain_skip_pct" min="1" max="100" step="1"
                               value="{{ old('weather_rain_skip_pct', $rule?->weather_rain_skip_pct ?? 40) }}"
                               oninput="document.getElementById('rain-pct-label').textContent = this.value"
                               class="flex-1 accent-sky-500">
                        <span class="text-sm font-semibold text-sky-600 w-10 text-right">{{ old('weather_rain_skip_pct', $rule?->weather_rain_skip_pct ?? 40) }}%</span>
                    </div>
                </div>
            </div>

            {{-- Sensor condition --}}
            <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                <div class="flex items-center gap-3">
                    <input type="hidden" name="sensor_condition_enabled" value="0">
                    <input type="checkbox" id="sensor_condition_enabled" name="sensor_condition_enabled" value="1"
                           {{ old('sensor_condition_enabled', $rule?->sensor_condition_enabled) ? 'checked' : '' }}
                           onchange="toggleSensorCondition()"
                           class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="sensor_condition_enabled" class="flex items-center gap-2 cursor-pointer select-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span class="text-sm font-semibold text-slate-800">Live sensor reading condition</span>
                    </label>
                </div>
                <p class="mt-1 ml-7 text-xs text-slate-500">Skip this rule if a specific sensor's latest reading matches the condition at trigger time.</p>
                <div id="sensor-condition-fields" class="mt-4 ml-7 grid grid-cols-1 md:grid-cols-3 gap-4 {{ old('sensor_condition_enabled', $rule?->sensor_condition_enabled) ? '' : 'hidden' }}">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">Sensor to check</label>
                        <select name="condition_sensor_id"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                            <option value="">— select sensor —</option>
                            @foreach($sensors as $s)
                                <option value="{{ $s->id }}" {{ old('condition_sensor_id', $rule?->condition_sensor_id) == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }} ({{ $s->unit }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">Operator (skip if…)</label>
                        <select name="condition_operator"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                            @foreach(['lt' => 'Less than (<)', 'lte' => 'Less or equal (≤)', 'gt' => 'Greater than (>)', 'gte' => 'Greater or equal (≥)', 'eq' => 'Equal to (=)'] as $v => $l)
                                <option value="{{ $v }}" {{ old('condition_operator', $rule?->condition_operator) === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">Threshold value</label>
                        <input type="number" step="0.01" name="condition_threshold"
                               value="{{ old('condition_threshold', $rule?->condition_threshold) }}"
                               placeholder="e.g. 5 (mm rain)"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── 4. Action ─────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
            <div class="h-6 w-6 rounded-full bg-violet-600 text-white text-xs flex items-center justify-center font-bold">4</div>
            <span class="text-sm font-semibold text-slate-800">Action</span>
            <span class="text-xs text-slate-400">— what happens when the rule fires?</span>
        </div>
        <div class="px-6 py-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach(['log' => ['Log only', 'Record the event in the activity log.'], 'notify' => ['Notify only', 'Send an in-app notification.'], 'both' => ['Log + Notify', 'Record the event AND send a notification.']] as $v => [$label, $desc])
                    <label class="cursor-pointer">
                        <input type="radio" name="action_type" value="{{ $v }}"
                               {{ old('action_type', $rule?->action_type ?? 'both') === $v ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="rounded-xl border-2 border-slate-200 peer-checked:border-violet-500 peer-checked:bg-violet-50 p-4 transition">
                            <div class="text-sm font-semibold text-slate-800 mb-1">{{ $label }}</div>
                            <p class="text-xs text-slate-500">{{ $desc }}</p>
                        </div>
                    </label>
                @endforeach
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1.5">Action notes <span class="text-slate-400">(optional)</span></label>
                <textarea name="action_notes" rows="2" placeholder="e.g. 'Check pump status manually if rule fires.'"
                          class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 resize-none">{{ old('action_notes', $rule?->action_notes) }}</textarea>
            </div>
        </div>
    </div>

    {{-- ── 5. Retry Logic ─────────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
            <div class="h-6 w-6 rounded-full bg-slate-700 text-white text-xs flex items-center justify-center font-bold">5</div>
            <span class="text-sm font-semibold text-slate-800">Retry Logic</span>
            <span class="text-xs text-slate-400">— what to do if the trigger was held back by a condition?</span>
        </div>
        <div class="px-6 py-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach([
                    'wait_window'    => ['Wait window', 'Wait X minutes. If the condition clears, execute the action.'],
                    'next_scheduled' => ['Next scheduled run', 'Skip today entirely. Try again at the next scheduled trigger.'],
                    'both'           => ['Wait, then next run', 'Try the wait window first. If still blocked, fall back to the next scheduled run.'],
                ] as $v => [$label, $desc])
                    <label class="cursor-pointer">
                        <input type="radio" name="retry_type" value="{{ $v }}"
                               {{ old('retry_type', $rule?->retry_type ?? 'next_scheduled') === $v ? 'checked' : '' }}
                               class="sr-only peer" onchange="toggleRetry()">
                        <div class="rounded-xl border-2 border-slate-200 peer-checked:border-slate-700 peer-checked:bg-slate-50 p-4 transition">
                            <div class="text-sm font-semibold text-slate-800 mb-1">{{ $label }}</div>
                            <p class="text-xs text-slate-500">{{ $desc }}</p>
                        </div>
                    </label>
                @endforeach
            </div>

            {{-- Wait minutes (shown when wait_window or both selected) --}}
            <div id="retry-wait-fields" class="{{ in_array(old('retry_type', $rule?->retry_type ?? 'next_scheduled'), ['wait_window', 'both']) ? '' : 'hidden' }}">
                <label class="block text-xs font-medium text-slate-700 mb-1.5">
                    Wait up to how many minutes before retrying?
                </label>
                <div class="flex items-center gap-3 max-w-xs">
                    <input type="number" name="retry_wait_minutes" min="1" max="1440"
                           value="{{ old('retry_wait_minutes', $rule?->retry_wait_minutes ?? 120) }}"
                           class="flex-1 rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <span class="text-sm text-slate-500">minutes</span>
                </div>
                @error('retry_wait_minutes')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- ── Submit ─────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('plus.iot.rules.index') }}"
           class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm text-slate-600 hover:bg-slate-50 transition">
            ← Cancel
        </a>
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            {{ $method === 'PUT' ? 'Save Changes' : 'Create Rule' }}
        </button>
    </div>

</form>

<script>
function toggleTrigger() {
    const type = document.querySelector('input[name="trigger_type"]:checked')?.value;
    document.getElementById('trigger-time-fields').classList.toggle('hidden', type !== 'time');
    document.getElementById('trigger-sensor-fields').classList.toggle('hidden', type !== 'sensor_threshold');
}
function toggleWeather() {
    const checked = document.getElementById('weather_condition_enabled').checked;
    document.getElementById('weather-fields').classList.toggle('hidden', !checked);
}
function toggleSensorCondition() {
    const checked = document.getElementById('sensor_condition_enabled').checked;
    document.getElementById('sensor-condition-fields').classList.toggle('hidden', !checked);
}
function toggleRetry() {
    const type = document.querySelector('input[name="retry_type"]:checked')?.value;
    document.getElementById('retry-wait-fields').classList.toggle('hidden', !['wait_window','both'].includes(type));
}
// Sync the range label on drag
document.addEventListener('DOMContentLoaded', function () {
    const range = document.querySelector('input[name="weather_rain_skip_pct"]');
    if (range) {
        range.addEventListener('input', function () {
            document.getElementById('rain-pct-label').textContent = this.value;
            this.nextElementSibling && (this.nextElementSibling.textContent = this.value + '%');
        });
    }
    toggleTrigger();
});
</script>
