@extends('layouts.app')

@section('content')

<div class="max-w-2xl">

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
            <a href="{{ route('core.listings.index') }}" class="hover:text-emerald-700 transition">Listings</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-900 font-medium">New Listing</span>
        </div>
        <h1 class="text-2xl font-semibold text-slate-900">Create Listing</h1>
        <p class="text-sm text-slate-500 mt-1">Publish a product to the AgroFlux Marketplace.</p>
    </div>
</div>

{{-- Payment info notice --}}
@if(!$hasPaymentInfo)
<div class="mb-5 flex items-start gap-3 rounded-xl px-4 py-3" style="background:#fefce8;border:1px solid #fde047;">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 mt-0.5" style="color:#ca8a04;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
    </svg>
    <div class="flex-1">
        <span class="text-sm font-semibold" style="color:#854d0e;">Payment information missing</span>
        <p class="text-xs mt-0.5" style="color:#78350f;">
            Buyers won't see bank or IRIS payment details on their order confirmation until you add them.
            <a href="{{ route('profile.edit') }}" class="underline font-medium" style="color:#92400e;">Add payment info in your profile →</a>
        </p>
    </div>
</div>
@endif

@if($errors->any())
<div class="mb-4 px-4 py-3 rounded-xl text-sm" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;">
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<div class="rounded-2xl border border-emerald-100 bg-white shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-emerald-100">
        <div class="font-semibold text-sm text-slate-900">Listing Details</div>
    </div>

    <form method="POST" action="{{ route('core.listings.store') }}" class="px-6 py-5 space-y-5">
        @csrf

        {{-- Product --}}
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">
                Product <span class="text-red-500">*</span>
            </label>
            <select name="product_id" required
                    class="w-full h-10 px-3 rounded-xl border @error('product_id') border-red-400 @else border-slate-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                <option value="">— Select a product —</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->default_name }} @if($p->unit)({{ $p->unit }})@endif
                    </option>
                @endforeach
            </select>
            @error('product_id')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Type + Price --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">
                    Listing Type <span class="text-red-500">*</span>
                </label>
                <select name="type" id="listingType" required onchange="toggleTypeFields()"
                        class="w-full h-10 px-3 rounded-xl border @error('type') border-red-400 @else border-slate-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    <option value="instock" {{ old('type','instock') === 'instock' ? 'selected' : '' }}>In Stock — sell what you have now</option>
                    <option value="preorder" {{ old('type') === 'preorder' ? 'selected' : '' }}>Pre-order — future harvest</option>
                </select>
                @error('type')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">
                    Price per unit (€) <span class="text-red-500">*</span>
                </label>
                <input name="price" type="number" step="0.01" min="0" required
                       value="{{ old('price', '0.00') }}"
                       class="w-full h-10 px-3 rounded-xl border @error('price') border-red-400 @else border-slate-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                @error('price')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- In-stock fields --}}
        <div id="instockFields">
            <label class="block text-xs font-medium text-slate-500 mb-1.5">Available Quantity</label>
            <input name="available_qty" type="number" step="0.01" min="0"
                   value="{{ old('available_qty') }}"
                   placeholder="e.g. 500"
                   class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
            @error('available_qty')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Pre-order fields --}}
        <div id="preorderFields" style="display:none;">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Expected Harvest Date</label>
                    <input name="expected_harvest_at" type="date"
                           value="{{ old('expected_harvest_at') }}"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    @error('expected_harvest_at')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Upfront % required</label>
                    <input name="upfront_percent" type="number" step="0.01" min="0" max="99.99"
                           value="{{ old('upfront_percent', '25') }}"
                           class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    @error('upfront_percent')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            <button type="submit"
                    class="inline-flex items-center gap-2 h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Publish Listing
            </button>
            <a href="{{ route('core.listings.index') }}"
               class="h-10 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition inline-flex items-center">
                Cancel
            </a>
        </div>
    </form>
</div>

</div>

<script>
function toggleTypeFields() {
    const type = document.getElementById('listingType').value;
    document.getElementById('instockFields').style.display  = type === 'instock'  ? '' : 'none';
    document.getElementById('preorderFields').style.display = type === 'preorder' ? '' : 'none';
}
toggleTypeFields();
</script>

@endsection
