@extends('layouts.app')
@section('content')

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Inventory</h1>
        <p class="text-sm text-slate-500 mt-1">Manage stock levels for seeds, feed, pesticides, fertilisers and other supplies.</p>
    </div>
    <button onclick="document.getElementById('addItemModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm flex-shrink-0">
        + Add Item
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
    <div class="rounded-2xl border border-amber-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Low Stock</div>
        <div class="text-3xl font-bold text-amber-500">{{ $lowStock }}</div>
    </div>
    <div class="rounded-2xl border border-red-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Out of Stock</div>
        <div class="text-3xl font-bold text-red-500">{{ $outOfStock }}</div>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Categories</div>
        <div class="text-3xl font-bold text-slate-900">{{ $categories }}</div>
    </div>
</div>

<div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div class="font-semibold text-slate-900">Stock List</div>
        <form method="GET" action="{{ route('inventory.index') }}">
            <select name="category" onchange="this.form.submit()"
                    class="h-9 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none">
                <option value="">All Categories</option>
                @foreach(['Seeds','Feed','Fertilisers','Pesticides','Herbicides','Fuel','Other'] as $cat)
                    <option value="{{ $cat }}" {{ ($category ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if($items->isEmpty())
        <div class="p-12 text-center">
            <div class="h-14 w-14 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <p class="text-sm font-semibold text-slate-700 mb-1">No inventory items yet</p>
            <p class="text-xs text-slate-400 max-w-xs mx-auto">Add your first inventory item to start tracking stock levels and usage.</p>
            <button onclick="document.getElementById('addItemModal').classList.remove('hidden')"
                    class="mt-4 inline-flex items-center gap-2 h-9 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
                + Add First Item
            </button>
        </div>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Name</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Category</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Qty</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Min Alert</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Supplier</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Expires</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($items as $item)
                @php
                    $qtyClass = $item->isOutOfStock() ? 'text-red-600 font-semibold'
                              : ($item->isLowStock()  ? 'text-amber-600 font-semibold'
                              : 'text-slate-900');
                @endphp
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-5 py-3 font-medium text-slate-900">
                        {{ $item->name }}
                        @if($item->notes)
                            <div class="text-xs text-slate-400 truncate max-w-xs">{{ $item->notes }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-slate-100 text-xs font-medium text-slate-500">{{ $item->category }}</span>
                    </td>
                    <td class="px-5 py-3 font-mono {{ $qtyClass }}">
                        {{ number_format($item->quantity, 2) }} <span class="text-slate-400 text-xs font-normal">{{ $item->unit }}</span>
                    </td>
                    <td class="px-5 py-3 text-slate-400 text-xs">{{ $item->min_qty !== null ? number_format($item->min_qty, 2).' '.$item->unit : '—' }}</td>
                    <td class="px-5 py-3 text-slate-500 text-xs">{{ $item->supplier ?? '—' }}</td>
                    <td class="px-5 py-3 text-slate-500 text-xs">{{ $item->expires_at ? $item->expires_at->format('d M Y') : '—' }}</td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button type="button"
                                    onclick="openInvEdit({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ $item->category }}', '{{ $item->quantity }}', '{{ $item->unit }}', '{{ $item->min_qty ?? '' }}', '{{ addslashes($item->supplier ?? '') }}', '{{ $item->expires_at?->format('Y-m-d') ?? '' }}', '{{ addslashes($item->notes ?? '') }}')"
                                    class="h-7 px-3 rounded-lg border border-slate-200 text-xs font-medium text-slate-500 hover:bg-slate-50 hover:text-emerald-700 hover:border-emerald-200 transition">
                                Edit
                            </button>
                            <form method="POST" action="{{ route('inventory.destroy', $item) }}" onsubmit="return confirm('Remove this item?')">
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

{{-- Edit Inventory Modal --}}
<div id="editItemModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="font-semibold text-slate-900">Edit Inventory Item</div>
            <button type="button" onclick="document.getElementById('editItemModal').classList.add('hidden')"
                    class="h-8 w-8 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition text-xl leading-none">×</button>
        </div>
        <form id="editItemForm" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Item Name <span class="text-red-500">*</span></label>
                    <input type="text" id="editInvName" name="name" required
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Category</label>
                    <select id="editInvCategory" name="category"
                            class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option>Seeds</option><option>Feed</option><option>Fertilisers</option>
                        <option>Pesticides</option><option>Herbicides</option><option>Fuel</option><option>Other</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" id="editInvQty" name="quantity" required
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Unit</label>
                    <select id="editInvUnit" name="unit"
                            class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option>kg</option><option>L</option><option>Bags</option><option>Boxes</option><option>Units</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Min. Alert</label>
                    <input type="number" step="0.01" id="editInvMinQty" name="min_qty"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Supplier</label>
                    <input type="text" id="editInvSupplier" name="supplier"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Expiry Date</label>
                    <input type="date" id="editInvExpires" name="expires_at"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Notes</label>
                <textarea id="editInvNotes" name="notes" rows="2"
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 resize-none"></textarea>
            </div>
            <div class="pt-2 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('editItemModal').classList.add('hidden')"
                        class="h-10 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition">Cancel</button>
                <button type="submit"
                        class="h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openInvEdit(id, name, category, qty, unit, minQty, supplier, expiresAt, notes) {
    document.getElementById('editInvName').value      = name;
    document.getElementById('editInvCategory').value  = category;
    document.getElementById('editInvQty').value       = qty;
    document.getElementById('editInvUnit').value      = unit;
    document.getElementById('editInvMinQty').value    = minQty;
    document.getElementById('editInvSupplier').value  = supplier;
    document.getElementById('editInvExpires').value   = expiresAt;
    document.getElementById('editInvNotes').value     = notes;
    document.getElementById('editItemForm').action    = '/inventory/' + id;
    document.getElementById('editItemModal').classList.remove('hidden');
}
</script>

<div id="addItemModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="font-semibold text-slate-900">Add Inventory Item</div>
            <button onclick="document.getElementById('addItemModal').classList.add('hidden')"
                    class="h-8 w-8 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition text-xl leading-none">×</button>
        </div>
        <form method="POST" action="{{ route('inventory.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Item Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" placeholder="e.g. NPK Fertiliser 20-20-20"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Category</label>
                    <select name="category" class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option>Seeds</option><option>Feed</option><option>Fertilisers</option>
                        <option>Pesticides</option><option>Herbicides</option><option>Fuel</option><option>Other</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="quantity" placeholder="e.g. 50"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Unit</label>
                    <select name="unit" class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option>kg</option><option>L</option><option>Bags</option><option>Boxes</option><option>Units</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Min. Alert</label>
                    <input type="number" step="0.01" name="min_qty" placeholder="e.g. 10"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Supplier</label>
                    <input type="text" name="supplier" placeholder="Supplier name"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Expiry Date</label>
                    <input type="date" name="expires_at"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Notes</label>
                <textarea name="notes" rows="2" placeholder="Storage location, batch number, notes..."
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 resize-none"></textarea>
            </div>
            <div class="pt-2 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addItemModal').classList.add('hidden')"
                        class="h-10 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition">Cancel</button>
                <button type="submit" class="h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">Save Item</button>
            </div>
        </form>
    </div>
</div>

@endsection
