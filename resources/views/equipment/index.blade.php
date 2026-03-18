@extends('layouts.app')
@section('content')

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Equipment</h1>
        <p class="text-sm text-slate-500 mt-1">Register tractors, machinery and tools. Track maintenance and operational status.</p>
    </div>
    <button onclick="document.getElementById('addEquipModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm flex-shrink-0">
        + Add Equipment
    </button>
</div>

@if(session('success'))
    <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Total Items</div>
        <div class="text-3xl font-bold text-slate-900">{{ $total }}</div>
    </div>
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Operational</div>
        <div class="text-3xl font-bold text-emerald-600">{{ $operational }}</div>
    </div>
    <div class="rounded-2xl border border-amber-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Under Maintenance</div>
        <div class="text-3xl font-bold text-amber-500">{{ $maintenance }}</div>
    </div>
    <div class="rounded-2xl border border-red-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Out of Service</div>
        <div class="text-3xl font-bold text-red-500">{{ $outOfService }}</div>
    </div>
</div>

<div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-900">Equipment Register</div>

    @if($equipment->isEmpty())
        <div class="p-12 text-center">
            <div class="h-14 w-14 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="text-sm font-semibold text-slate-700 mb-1">No equipment registered yet</p>
            <p class="text-xs text-slate-400 max-w-xs mx-auto">Add your first piece of equipment to start tracking maintenance and usage.</p>
            <button onclick="document.getElementById('addEquipModal').classList.remove('hidden')"
                    class="mt-4 inline-flex items-center gap-2 h-9 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
                + Add Equipment
            </button>
        </div>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Name</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Category</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Serial / Plate</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Next Service</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($equipment as $item)
                @php
                    [$badgeCls, $badgeLabel] = match($item->status) {
                        'operational'    => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'Operational'],
                        'maintenance'    => ['bg-amber-50 text-amber-700 border-amber-200',       'Maintenance'],
                        'out_of_service' => ['bg-red-50 text-red-600 border-red-200',             'Out of Service'],
                        default          => ['bg-slate-50 text-slate-500 border-slate-200',       $item->status],
                    };
                @endphp
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-5 py-3 font-medium text-slate-900">
                        {{ $item->name }}
                        @if($item->notes)
                            <div class="text-xs text-slate-400 truncate max-w-xs">{{ $item->notes }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-slate-600">{{ $item->category }}</td>
                    <td class="px-5 py-3 font-mono text-slate-500 text-xs">{{ $item->serial ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg border text-xs font-medium {{ $badgeCls }}">{{ $badgeLabel }}</span>
                    </td>
                    <td class="px-5 py-3 text-slate-500 text-xs">{{ $item->next_service_at ? $item->next_service_at->format('d M Y') : '—' }}</td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button type="button"
                                    onclick="openEquipEdit({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ $item->category }}', '{{ addslashes($item->serial ?? '') }}', '{{ $item->status }}', '{{ $item->purchased_at?->format('Y-m-d') ?? '' }}', '{{ $item->next_service_at?->format('Y-m-d') ?? '' }}', '{{ addslashes($item->notes ?? '') }}')"
                                    class="h-7 px-3 rounded-lg border border-slate-200 text-xs font-medium text-slate-500 hover:bg-slate-50 hover:text-emerald-700 hover:border-emerald-200 transition">
                                Edit
                            </button>
                            <form method="POST" action="{{ route('equipment.destroy', $item) }}" onsubmit="return confirm('Remove this equipment?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-300 hover:text-red-500 transition text-xl leading-none" title="Remove">×</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- Edit Equipment Modal --}}
<div id="editEquipModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="font-semibold text-slate-900">Edit Equipment</div>
            <button type="button" onclick="document.getElementById('editEquipModal').classList.add('hidden')"
                    class="h-8 w-8 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition text-xl leading-none">×</button>
        </div>
        <form id="editEquipForm" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Name <span class="text-red-500">*</span></label>
                    <input type="text" id="editEquipName" name="name" required
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Category</label>
                    <select id="editEquipCategory" name="category"
                            class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option>Tractor</option><option>Harvester</option><option>Irrigation</option>
                        <option>Sprayer</option><option>Hand Tool</option><option>Vehicle</option><option>Other</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Serial / Plate</label>
                    <input type="text" id="editEquipSerial" name="serial"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Status</label>
                    <select id="editEquipStatus" name="status"
                            class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="operational">Operational</option>
                        <option value="maintenance">Under Maintenance</option>
                        <option value="out_of_service">Out of Service</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Purchase Date</label>
                    <input type="date" id="editEquipPurchased" name="purchased_at"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Next Service Date</label>
                    <input type="date" id="editEquipService" name="next_service_at"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Notes</label>
                <textarea id="editEquipNotes" name="notes" rows="2"
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 resize-none"></textarea>
            </div>
            <div class="pt-2 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('editEquipModal').classList.add('hidden')"
                        class="h-10 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition">Cancel</button>
                <button type="submit"
                        class="h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEquipEdit(id, name, category, serial, status, purchasedAt, nextService, notes) {
    document.getElementById('editEquipName').value      = name;
    document.getElementById('editEquipCategory').value  = category;
    document.getElementById('editEquipSerial').value    = serial;
    document.getElementById('editEquipStatus').value    = status;
    document.getElementById('editEquipPurchased').value = purchasedAt;
    document.getElementById('editEquipService').value   = nextService;
    document.getElementById('editEquipNotes').value     = notes;
    document.getElementById('editEquipForm').action     = '/equipment/' + id;
    document.getElementById('editEquipModal').classList.remove('hidden');
}
</script>

<div id="addEquipModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="font-semibold text-slate-900">Add Equipment</div>
            <button onclick="document.getElementById('addEquipModal').classList.add('hidden')"
                    class="h-8 w-8 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition text-xl leading-none">×</button>
        </div>
        <form method="POST" action="{{ route('equipment.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" placeholder="e.g. John Deere 6130"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Category</label>
                    <select name="category" class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option>Tractor</option><option>Harvester</option><option>Irrigation</option>
                        <option>Sprayer</option><option>Hand Tool</option><option>Vehicle</option><option>Other</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Serial / Plate</label>
                    <input type="text" name="serial" placeholder="Serial or plate number"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Status</label>
                    <select name="status" class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="operational">Operational</option>
                        <option value="maintenance">Under Maintenance</option>
                        <option value="out_of_service">Out of Service</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Purchase Date</label>
                    <input type="date" name="purchased_at"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Next Service Date</label>
                    <input type="date" name="next_service_at"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Notes</label>
                <textarea name="notes" rows="2" placeholder="Model year, fuel type, attachments..."
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 resize-none"></textarea>
            </div>
            <div class="pt-2 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addEquipModal').classList.add('hidden')"
                        class="h-10 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition">Cancel</button>
                <button type="submit" class="h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">Save Equipment</button>
            </div>
        </form>
    </div>
</div>

@endsection
