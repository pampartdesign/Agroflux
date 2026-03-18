{{--
    Shared form partial for create & edit.
    Variables expected: $seller (existing model or null), $action (route URL), $method ('POST' or 'PUT')
--}}

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($method === 'PUT') @method('PUT') @endif

    {{-- Company info --}}
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-6 space-y-4">
        <h2 class="font-semibold text-slate-900">Company Information</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Company Name <span class="text-red-500">*</span></label>
                <input type="text" name="company_name" value="{{ old('company_name', $seller?->company_name) }}" required
                       class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
                @error('company_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Category</label>
                <input type="text" name="category" value="{{ old('category', $seller?->category) }}"
                       placeholder="e.g. Seeds, Fertilizers, Equipment"
                       class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
                @error('category') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Short Description</label>
            <textarea name="short_description" rows="3"
                      class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 resize-none">{{ old('short_description', $seller?->short_description) }}</textarea>
            @error('short_description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Address</label>
            <input type="text" name="address" value="{{ old('address', $seller?->address) }}"
                   placeholder="e.g. 123 Main Street, City, Country"
                   class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
            @error('address') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $seller?->phone) }}"
                       placeholder="+1 555 000 0000"
                       class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
                @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $seller?->email) }}"
                       placeholder="info@company.com"
                       class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
                @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Website URL</label>
                <input type="url" name="website_url" value="{{ old('website_url', $seller?->website_url) }}"
                       placeholder="https://example.com"
                       class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
                @error('website_url') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">
                    Publish Order
                    <span class="text-slate-400 font-normal">(0 = default, lower = first)</span>
                </label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $seller?->sort_order ?? 0) }}" min="0"
                       class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
                @error('sort_order') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center gap-3">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                       {{ old('is_active', $seller?->is_active ?? true) ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 transition-colors after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:w-5 after:h-5 after:bg-white after:rounded-full after:transition-all peer-checked:after:translate-x-5"></div>
            </label>
            <span class="text-sm text-slate-700">Published (visible to users)</span>
        </div>
    </div>

    {{-- Featured image --}}
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-6">
        <h2 class="font-semibold text-slate-900 mb-4">Featured Image</h2>
        @if($seller?->featuredImageUrl())
        <div class="mb-4">
            <img src="{{ $seller->featuredImageUrl() }}" alt="Current image"
                 class="h-32 w-auto rounded-xl border border-slate-200 object-cover">
            <p class="text-xs text-slate-400 mt-1">Current image — upload a new one to replace it.</p>
        </div>
        @endif
        <input type="file" name="featured_image" accept="image/*"
               class="text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-emerald-50 file:text-emerald-700 file:text-sm file:font-medium hover:file:bg-emerald-100 transition">
        <p class="text-xs text-slate-400 mt-1">Max 2 MB. JPG, PNG, WEBP.</p>
        @error('featured_image') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Products list --}}
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="font-semibold text-slate-900">Products</h2>
                <p class="text-xs text-slate-400 mt-0.5">Up to 20 products are displayed. Add one product per line.</p>
            </div>
            <button type="button" id="addProductBtn"
                    class="text-xs px-3 py-1.5 rounded-xl border border-emerald-200 text-emerald-700 hover:bg-emerald-50 transition">
                + Add Row
            </button>
        </div>

        <div id="productList" class="space-y-2">
            @php $existingProducts = old('products', $seller?->products->pluck('name')->toArray() ?? []); @endphp
            @forelse($existingProducts as $i => $pname)
            <div class="product-row flex items-center gap-2">
                <input type="text" name="products[]" value="{{ $pname }}"
                       placeholder="Product name"
                       class="flex-1 h-9 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
                <button type="button" onclick="this.closest('.product-row').remove()"
                        class="h-9 w-9 flex items-center justify-center rounded-xl border border-red-100 text-red-400 hover:bg-red-50 transition flex-shrink-0">
                    ✕
                </button>
            </div>
            @empty
            <div class="product-row flex items-center gap-2">
                <input type="text" name="products[]" value=""
                       placeholder="Product name"
                       class="flex-1 h-9 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
                <button type="button" onclick="this.closest('.product-row').remove()"
                        class="h-9 w-9 flex items-center justify-center rounded-xl border border-red-100 text-red-400 hover:bg-red-50 transition flex-shrink-0">
                    ✕
                </button>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3">
        <button type="submit"
                class="h-10 px-6 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
            {{ $method === 'PUT' ? 'Save Changes' : 'Add Seller' }}
        </button>
        <a href="{{ route('admin.authorized-sellers.index') }}"
           class="h-10 px-5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-sm text-slate-600 flex items-center transition">
            Cancel
        </a>
    </div>
</form>

<script>
document.getElementById('addProductBtn')?.addEventListener('click', function () {
    const list = document.getElementById('productList');
    const row = document.createElement('div');
    row.className = 'product-row flex items-center gap-2';
    row.innerHTML = `
        <input type="text" name="products[]" placeholder="Product name"
               class="flex-1 h-9 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
        <button type="button" onclick="this.closest('.product-row').remove()"
                class="h-9 w-9 flex items-center justify-center rounded-xl border border-red-100 text-red-400 hover:bg-red-50 transition flex-shrink-0">
            ✕
        </button>`;
    list.appendChild(row);
    row.querySelector('input')?.focus();
});
</script>
