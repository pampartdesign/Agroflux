@extends('layouts.app')
@section('content')

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
            <a href="{{ route('livestock.dashboard') }}" class="hover:text-emerald-700 transition">{{ __('livestock.management_title') }}</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-900 font-medium">{{ __('livestock.breadcrumb_stock') }}</span>
        </div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('livestock.stock_title') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('livestock.stock_subtitle') }}</p>
    </div>
    <button onclick="document.getElementById('addAnimalModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm flex-shrink-0">
        {{ __('livestock.btn_add_animal') }}
    </button>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    {{ session('success') }}
</div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('livestock.kpi_total_animals') }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $totalAnimals }}</div>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('livestock.kpi_pregnant') }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $pregnantCount }}</div>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('livestock.kpi_vacs_due') }}</div>
        <div class="text-3xl font-bold text-amber-500">{{ $vacsDue }}</div>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ __('livestock.kpi_new_this_month') }}</div>
        <div class="text-3xl font-bold text-emerald-600">{{ $newThisMonth }}</div>
    </div>
</div>

{{-- Animal table --}}
<div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-900">{{ __('livestock.animal_register') }}</div>

    @if($animals->isEmpty())
    <div class="p-12 text-center">
        <div class="h-14 w-14 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center mx-auto mb-4 text-3xl">🐄</div>
        <p class="text-sm font-semibold text-slate-700 mb-1">{{ __('livestock.no_animals_title') }}</p>
        <p class="text-xs text-slate-400 max-w-xs mx-auto">{{ __('livestock.no_animals_desc') }}</p>
        <button onclick="document.getElementById('addAnimalModal').classList.remove('hidden')"
                class="mt-4 inline-flex items-center gap-2 h-9 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
            {{ __('livestock.btn_add_first_animal') }}
        </button>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs font-semibold text-slate-400 uppercase tracking-wide border-b border-slate-100">
                    <th class="px-6 py-3 text-left">{{ __('livestock.col_tag') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('livestock.col_species') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('livestock.col_breed') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('livestock.col_gender') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('livestock.col_dob') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('livestock.col_status') }}</th>
                    <th class="px-6 py-3 text-right">{{ __('livestock.col_actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($animals as $animal)
                @php
                    $statusStyle = match($animal->status) {
                        'active'   => 'background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;',
                        'pregnant' => 'background:#fdf4ff;color:#7e22ce;border:1px solid #e9d5ff;',
                        'sick'     => 'background:#fef2f2;color:#dc2626;border:1px solid #fecaca;',
                        'sold'     => 'background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;',
                        default    => 'background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;',
                    };
                @endphp
                <tr class="hover:bg-slate-50/60 transition">
                    <td class="px-6 py-3 font-medium text-slate-900">{{ $animal->tag }}</td>
                    <td class="px-6 py-3 text-slate-600">{{ $animal->species }}</td>
                    <td class="px-6 py-3 text-slate-500">{{ $animal->breed ?? '—' }}</td>
                    <td class="px-6 py-3 text-slate-500 capitalize">{{ $animal->gender ?? '—' }}</td>
                    <td class="px-6 py-3 text-slate-500">{{ $animal->dob ? $animal->dob->format('d M Y') : '—' }}</td>
                    <td class="px-6 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full" style="{{ $statusStyle }}">
                            {{ $animal->statusLabel() }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openAnimalEdit({{ $animal->id }}, '{{ addslashes($animal->tag) }}', '{{ $animal->species }}', '{{ addslashes($animal->breed ?? '') }}', '{{ $animal->gender ?? '' }}', '{{ $animal->dob?->format('Y-m-d') ?? '' }}', '{{ $animal->status }}', '{{ addslashes($animal->notes ?? '') }}')"
                                    class="text-xs h-7 px-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition text-slate-500">
                                {{ __('app.edit') }}
                            </button>
                            <form method="POST" action="{{ route('livestock.stock.destroy', $animal) }}"
                                  onsubmit="return confirm('{{ __('livestock.confirm_remove_animal') }}')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-xs h-7 px-2.5 rounded-lg border border-slate-200 hover:bg-red-50 hover:border-red-200 hover:text-red-500 transition text-slate-400">
                                    ✕
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
</div>

{{-- Add Animal Modal --}}
<div id="addAnimalModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg border border-slate-100 max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
            <div class="font-semibold text-slate-900">{{ __('livestock.modal_add_animal') }}</div>
            <button onclick="document.getElementById('addAnimalModal').classList.add('hidden')"
                    class="h-8 w-8 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition text-xl leading-none">×</button>
        </div>
        <form method="POST" action="{{ route('livestock.stock.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_tag') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="tag" required placeholder="{{ __('livestock.placeholder_tag') }}"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_species') }} <span class="text-red-500">*</span></label>
                    <select name="species" required class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="">{{ __('livestock.select_placeholder') }}</option>
                        <option value="Cattle">{{ __('livestock.species_cattle') }}</option>
                        <option value="Sheep">{{ __('livestock.species_sheep') }}</option>
                        <option value="Goats">{{ __('livestock.species_goats') }}</option>
                        <option value="Pigs">{{ __('livestock.species_pigs') }}</option>
                        <option value="Poultry">{{ __('livestock.species_poultry') }}</option>
                        <option value="Other">{{ __('livestock.species_other') }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_breed') }}</label>
                    <input type="text" name="breed" placeholder="{{ __('livestock.placeholder_breed') }}"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_gender') }}</label>
                    <select name="gender" class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="">{{ __('livestock.select_placeholder') }}</option>
                        <option value="female">{{ __('livestock.gender_female') }}</option>
                        <option value="male">{{ __('livestock.gender_male') }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_dob') }}</label>
                    <input type="date" name="dob"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_status') }} <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="active">{{ __('livestock.status_active') }}</option>
                        <option value="pregnant">{{ __('livestock.status_pregnant') }}</option>
                        <option value="sick">{{ __('livestock.status_sick') }}</option>
                        <option value="sold">{{ __('livestock.status_sold') }}</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_notes') }}</label>
                <textarea name="notes" rows="2" placeholder="{{ __('livestock.placeholder_notes') }}"
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 resize-none"></textarea>
            </div>
            <div class="pt-2 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addAnimalModal').classList.add('hidden')"
                        class="h-10 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition">{{ __('app.cancel') }}</button>
                <button type="submit" class="h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">{{ __('livestock.btn_save_animal') }}</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Animal Modal --}}
<div id="editAnimalModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg border border-slate-100 max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
            <div class="font-semibold text-slate-900">{{ __('livestock.modal_edit_animal') }}</div>
            <button onclick="document.getElementById('editAnimalModal').classList.add('hidden')"
                    class="h-8 w-8 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition text-xl leading-none">×</button>
        </div>
        <form id="editAnimalForm" method="POST" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_tag') }} <span class="text-red-500">*</span></label>
                    <input type="text" id="edit_tag" name="tag" required
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_species') }} <span class="text-red-500">*</span></label>
                    <select id="edit_species" name="species" required class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="">{{ __('livestock.select_placeholder') }}</option>
                        <option value="Cattle">{{ __('livestock.species_cattle') }}</option>
                        <option value="Sheep">{{ __('livestock.species_sheep') }}</option>
                        <option value="Goats">{{ __('livestock.species_goats') }}</option>
                        <option value="Pigs">{{ __('livestock.species_pigs') }}</option>
                        <option value="Poultry">{{ __('livestock.species_poultry') }}</option>
                        <option value="Other">{{ __('livestock.species_other') }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_breed') }}</label>
                    <input type="text" id="edit_breed" name="breed"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_gender') }}</label>
                    <select id="edit_gender" name="gender" class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="">{{ __('livestock.select_placeholder') }}</option>
                        <option value="female">{{ __('livestock.gender_female') }}</option>
                        <option value="male">{{ __('livestock.gender_male') }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_dob') }}</label>
                    <input type="date" id="edit_dob" name="dob"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_status') }} <span class="text-red-500">*</span></label>
                    <select id="edit_status" name="status" required class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="active">{{ __('livestock.status_active') }}</option>
                        <option value="pregnant">{{ __('livestock.status_pregnant') }}</option>
                        <option value="sick">{{ __('livestock.status_sick') }}</option>
                        <option value="sold">{{ __('livestock.status_sold') }}</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('livestock.label_notes') }}</label>
                <textarea id="edit_notes" name="notes" rows="2"
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 resize-none"></textarea>
            </div>
            <div class="pt-2 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('editAnimalModal').classList.add('hidden')"
                        class="h-10 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition">{{ __('app.cancel') }}</button>
                <button type="submit" class="h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">{{ __('livestock.btn_update_animal') }}</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAnimalEdit(id, tag, species, breed, gender, dob, status, notes) {
    const base = '{{ route('livestock.stock.update', ['animal' => '__ID__']) }}';
    document.getElementById('editAnimalForm').action = base.replace('__ID__', id);
    document.getElementById('edit_tag').value     = tag;
    document.getElementById('edit_breed').value   = breed;
    document.getElementById('edit_dob').value     = dob;
    document.getElementById('edit_notes').value   = notes;
    document.getElementById('edit_species').value = species;
    document.getElementById('edit_gender').value  = gender;
    document.getElementById('edit_status').value  = status;
    document.getElementById('editAnimalModal').classList.remove('hidden');
}
</script>

@endsection
