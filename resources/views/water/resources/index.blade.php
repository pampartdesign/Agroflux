@extends('layouts.app')
@section('content')

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
            <a href="{{ route('water.dashboard') }}" class="hover:text-emerald-700 transition">{{ __('water.breadcrumb_water') }}</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-900 font-medium">{{ __('water.breadcrumb_resources') }}</span>
        </div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('water.resources_title') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('water.resources_subtitle') }}</p>
    </div>
    <button onclick="document.getElementById('addResourceModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm flex-shrink-0">
        {{ __('water.btn_add_resource') }}
    </button>
</div>

@if(session('success'))
    <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="rounded-2xl border border-sky-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('water.kpi_total_sources') }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $total }}</div>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('water.kpi_wells') }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $wells }}</div>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('water.kpi_reservoirs') }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $reservoirs }}</div>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('water.kpi_irrigation_systems') }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $irrigation }}</div>
    </div>
</div>

<div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-900">{{ __('water.registered_sources') }}</div>

    @if($resources->isEmpty())
        <div class="p-12 text-center">
            <div class="h-14 w-14 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3C12 3 5 10 5 15a7 7 0 0014 0c0-5-7-12-7-12z"/>
                </svg>
            </div>
            <p class="text-sm font-semibold text-slate-700 mb-1">{{ __('water.no_sources_title') }}</p>
            <p class="text-xs text-slate-400 max-w-xs mx-auto">{{ __('water.no_sources_desc') }}</p>
            <button onclick="document.getElementById('addResourceModal').classList.remove('hidden')"
                    class="mt-4 inline-flex items-center gap-2 h-9 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
                {{ __('water.btn_add_first_source') }}
            </button>
        </div>
    @else
        @php
        $typeKey = fn(string $type): string => 'water.type_' . str_replace(' ', '_', strtolower($type));
        @endphp
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('water.col_name') }}</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('water.col_type') }}</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('water.col_capacity') }}</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('water.col_level') }}</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($resources as $res)
                @php
                    // Inline CSS — avoids Tailwind JIT not compiling dynamic class names from @php blocks
                    $levelStyle = match(true) {
                        $res->level_pct === null => 'background:#f1f5f9; color:#94a3b8;',
                        $res->level_pct < 20     => 'background:#fee2e2; color:#dc2626;',
                        $res->level_pct < 50     => 'background:#fef3c7; color:#d97706;',
                        default                  => 'background:#e0f2fe; color:#0369a1;',
                    };
                    $barColor = match(true) {
                        $res->level_pct === null => '#cbd5e1',
                        $res->level_pct < 20     => '#f87171',
                        $res->level_pct < 50     => '#fbbf24',
                        default                  => '#38bdf8',
                    };
                @endphp
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-5 py-3 font-medium text-slate-900">
                        {{ $res->name }}
                        @if($res->notes)
                            <div class="text-xs text-slate-400 truncate max-w-xs">{{ $res->notes }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-sky-50 border border-sky-200 text-xs font-medium text-sky-600">{{ __($typeKey($res->type), [], null) ?: $res->type }}</span>
                    </td>
                    <td class="px-5 py-3 font-mono text-slate-700">{{ $res->capacity_m3 !== null ? number_format($res->capacity_m3, 1) : '—' }}</td>
                    <td class="px-5 py-3">
                        @if($res->level_pct !== null)
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 rounded-full overflow-hidden" style="max-width:80px; background:#e2e8f0;">
                                    <div class="h-full rounded-full" style="width:{{ $res->level_pct }}%; background:{{ $barColor }};"></div>
                                </div>
                                <span class="text-xs font-medium px-1.5 py-0.5 rounded-lg" style="{{ $levelStyle }}">{{ $res->level_pct }}%</span>
                            </div>
                        @else
                            <span class="text-slate-400 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button type="button"
                                    onclick="openResEdit(
                                        {{ $res->id }},
                                        '{{ addslashes($res->name) }}',
                                        '{{ $res->type }}',
                                        '{{ $res->capacity_m3 ?? '' }}',
                                        '{{ $res->level_pct ?? '' }}',
                                        '{{ addslashes($res->notes ?? '') }}'
                                    )"
                                    class="text-xs text-slate-400 hover:text-emerald-600 transition font-medium px-2 py-1 rounded-lg hover:bg-emerald-50">
                                {{ __('water.btn_edit') }}
                            </button>
                            <form method="POST" action="{{ route('water.resources.destroy', $res) }}" onsubmit="return confirm('{{ __('water.confirm_remove_source') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-300 hover:text-red-500 transition text-xl leading-none" title="{{ __('water.btn_edit') }}">×</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- ── Add Resource Modal ───────────────────────────────────────────────────── --}}
<div id="addResourceModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="font-semibold text-slate-900">{{ __('water.modal_add_source') }}</div>
            <button onclick="document.getElementById('addResourceModal').classList.add('hidden')"
                    class="h-8 w-8 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition text-xl leading-none">×</button>
        </div>
        <form method="POST" action="{{ route('water.resources.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('water.label_name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" placeholder="{{ __('water.placeholder_name') }}"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('water.label_type') }} <span class="text-red-500">*</span></label>
                    <select name="type" class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="Well">{{ __('water.type_well') }}</option>
                        <option value="Reservoir">{{ __('water.type_reservoir') }}</option>
                        <option value="Irrigation System">{{ __('water.type_irrigation_system') }}</option>
                        <option value="Borehole">{{ __('water.type_borehole') }}</option>
                        <option value="Stream">{{ __('water.type_stream') }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('water.label_capacity_m3') }}</label>
                    <input type="number" step="0.1" name="capacity_m3" placeholder="{{ __('water.placeholder_capacity') }}"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('water.label_level_pct') }}</label>
                    <input type="number" min="0" max="100" name="level_pct" placeholder="{{ __('water.placeholder_level') }}"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('water.label_notes') }}</label>
                <textarea name="notes" rows="2" placeholder="{{ __('water.placeholder_notes') }}"
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 resize-none"></textarea>
            </div>
            <div class="pt-2 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addResourceModal').classList.add('hidden')"
                        class="h-10 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition">{{ __('water.btn_cancel') }}</button>
                <button type="submit" class="h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">{{ __('water.btn_save_source') }}</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Edit Resource Modal ──────────────────────────────────────────────────── --}}
<div id="editResourceModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="font-semibold text-slate-900">{{ __('water.modal_edit_source') }}</div>
            <button onclick="document.getElementById('editResourceModal').classList.add('hidden')"
                    class="h-8 w-8 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition text-xl leading-none">×</button>
        </div>
        <form id="editResForm" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('water.label_name') }} <span class="text-red-500">*</span></label>
                    <input id="editResName" type="text" name="name" placeholder="{{ __('water.placeholder_name') }}"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('water.label_type') }} <span class="text-red-500">*</span></label>
                    <select id="editResType" name="type" class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="Well">{{ __('water.type_well') }}</option>
                        <option value="Reservoir">{{ __('water.type_reservoir') }}</option>
                        <option value="Irrigation System">{{ __('water.type_irrigation_system') }}</option>
                        <option value="Borehole">{{ __('water.type_borehole') }}</option>
                        <option value="Stream">{{ __('water.type_stream') }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('water.label_capacity_m3') }}</label>
                    <input id="editResCapacity" type="number" step="0.1" name="capacity_m3" placeholder="{{ __('water.placeholder_capacity') }}"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('water.label_level_pct') }}</label>
                    <input id="editResLevel" type="number" min="0" max="100" name="level_pct" placeholder="{{ __('water.placeholder_level') }}"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('water.label_notes') }}</label>
                <textarea id="editResNotes" name="notes" rows="2" placeholder="{{ __('water.placeholder_notes') }}"
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 resize-none"></textarea>
            </div>
            <div class="pt-2 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('editResourceModal').classList.add('hidden')"
                        class="h-10 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition">{{ __('water.btn_cancel') }}</button>
                <button type="submit" class="h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">{{ __('water.btn_save_changes') }}</button>
            </div>
        </form>
    </div>
</div>

<script>
function openResEdit(id, name, type, capacity, level, notes) {
    document.getElementById('editResName').value     = name;
    document.getElementById('editResCapacity').value = capacity;
    document.getElementById('editResLevel').value    = level;
    document.getElementById('editResNotes').value    = notes;

    // Match the select option by value
    const typeSelect = document.getElementById('editResType');
    for (let i = 0; i < typeSelect.options.length; i++) {
        if (typeSelect.options[i].value === type) {
            typeSelect.selectedIndex = i;
            break;
        }
    }

    document.getElementById('editResForm').action = '/water/resources/' + id;
    document.getElementById('editResourceModal').classList.remove('hidden');
}
</script>

@endsection
