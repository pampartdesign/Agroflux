{{--
    Shared product form partial.
    Variables expected from parent:
      $product        – Product model (null on create)
      $categories     – Collection of top-level CatalogCategory (with children eager-loaded)
      $categoriesJson – JSON map of parent_id => [{id, name}] for Alpine cascade
--}}

@php
    $units = [
        'kg'     => 'kg — Kilogram',
        'g'      => 'g — Gram',
        'tonne'  => 'tonne — Metric Tonne',
        'litre'  => 'L — Litre',
        'ml'     => 'ml — Millilitre',
        'piece'  => 'piece — Unit / Piece',
        'box'    => 'box — Box',
        'crate'  => 'crate — Crate',
        'bag'    => 'bag — Bag',
        'head'   => 'head — Head (Livestock)',
        'dozen'  => 'dozen — Dozen',
        'bundle' => 'bundle — Bundle',
    ];

    $oldCat    = old('category_id',    $product?->category_id);
    $oldSubcat = old('subcategory_id', $product?->subcategory_id);
    $oldUnit   = old('unit',           $product?->unit ?? 'kg');
    $oldStatus = old('stock_status',   $product?->stock_status ?? 'in_stock');
@endphp

{{-- Alpine root: cascading subcategory + image preview --}}
<div x-data="{
        subcats:    {{ $categoriesJson }},
        catId:      {{ $oldCat ?? 'null' }},
        subcatId:   {{ $oldSubcat ?? 'null' }},
        imagePreview: {{ $product?->image_path ? '\''.asset('storage/'.$product->image_path).'\'' : 'null' }},
        get subList() { return this.catId && this.subcats[this.catId] ? this.subcats[this.catId] : []; },
        onCatChange() { if (!this.subList.find(s => s.id == this.subcatId)) this.subcatId = null; },
        onImageChange(e) {
            const f = e.target.files[0];
            if (f) this.imagePreview = URL.createObjectURL(f);
        }
     }"
     class="space-y-5">

    {{-- ── Product identity ── --}}
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-emerald-100 flex items-center gap-3">
            <div class="h-8 w-8 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M20 7l-8-4-8 4 8 4 8-4z"/><path d="M4 7v10l8 4 8-4V7"/><path d="M12 11v10"/>
                </svg>
            </div>
            <div class="font-semibold text-sm text-slate-900">{{ __('core.section_product_identity') }}</div>
        </div>

        <div class="px-6 py-5 space-y-4">

            {{-- Name --}}
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">
                    {{ __('core.label_product_name') }} <span class="text-red-500">*</span>
                </label>
                <input name="default_name"
                       value="{{ old('default_name', $product?->default_name) }}"
                       type="text" required
                       class="w-full h-10 px-3 rounded-xl border @error('default_name') border-red-400 @else border-slate-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
                       placeholder="{{ __('core.placeholder_product_name') }}">
                @error('default_name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('core.label_description') }}</label>
                <textarea name="default_description" rows="3"
                          class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 resize-none"
                          placeholder="{{ __('core.placeholder_description') }}">{{ old('default_description', $product?->default_description) }}</textarea>
            </div>

            {{-- SKU --}}
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('core.label_sku') }}</label>
                <input name="sku"
                       value="{{ old('sku', $product?->sku) }}"
                       type="text"
                       class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-emerald-200"
                       placeholder="{{ __('core.placeholder_sku') }}">
                <p class="mt-1 text-xs text-slate-400">{{ __('core.sku_hint') }}</p>
            </div>
        </div>
    </div>

    {{-- ── Classification ── --}}
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-emerald-100 flex items-center gap-3">
            <div class="h-8 w-8 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
            <div class="font-semibold text-sm text-slate-900">{{ __('core.section_classification') }}</div>
        </div>

        <div class="px-6 py-5 space-y-4">

            {{-- Category --}}
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('core.label_category') }}</label>
                <select name="category_id"
                        x-model="catId"
                        x-on:change="onCatChange()"
                        class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    <option value="">{{ __('core.no_category_option') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Sub-category (cascades from category via Alpine) --}}
            <div x-show="subList.length > 0" x-transition>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('core.label_subcategory') }}</label>
                <select name="subcategory_id"
                        x-model="subcatId"
                        class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    <option value="">{{ __('core.no_subcategory_option') }}</option>
                    <template x-for="sub in subList" :key="sub.id">
                        <option :value="sub.id" x-text="sub.name" :selected="sub.id == subcatId"></option>
                    </template>
                </select>
                <p class="mt-1 text-xs text-slate-400">{{ __('core.subcategory_hint') }}</p>
            </div>
        </div>
    </div>

    {{-- ── Inventory & Pricing ── --}}
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-emerald-100 flex items-center gap-3">
            <div class="h-8 w-8 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                </svg>
            </div>
            <div class="font-semibold text-sm text-slate-900">{{ __('core.section_inventory_pricing') }}</div>
        </div>

        <div class="px-6 py-5 space-y-4">

            {{-- Stock status --}}
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-2">{{ __('core.label_stock_status') }} <span class="text-red-500">*</span></label>
                <div class="flex gap-3">
                    <label class="flex-1 flex items-center gap-3 px-4 py-3 rounded-xl border cursor-pointer transition
                                  {{ $oldStatus === 'in_stock' ? 'border-emerald-400 bg-emerald-50' : 'border-slate-200 bg-white hover:bg-slate-50' }}">
                        <input type="radio" name="stock_status" value="in_stock"
                               {{ $oldStatus === 'in_stock' ? 'checked' : '' }}
                               class="accent-emerald-600">
                        <div>
                            <div class="text-sm font-medium text-slate-900">{{ __('core.status_in_stock') }}</div>
                            <div class="text-xs text-slate-400">{{ __('core.status_in_stock_desc') }}</div>
                        </div>
                    </label>
                    <label class="flex-1 flex items-center gap-3 px-4 py-3 rounded-xl border cursor-pointer transition
                                  {{ $oldStatus === 'pre_order' ? 'border-amber-400 bg-amber-50' : 'border-slate-200 bg-white hover:bg-slate-50' }}">
                        <input type="radio" name="stock_status" value="pre_order"
                               {{ $oldStatus === 'pre_order' ? 'checked' : '' }}
                               class="accent-amber-500">
                        <div>
                            <div class="text-sm font-medium text-slate-900">{{ __('core.status_pre_order') }}</div>
                            <div class="text-xs text-slate-400">{{ __('core.status_pre_order_desc') }}</div>
                        </div>
                    </label>
                </div>
                @error('stock_status')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Inventory + Unit of measure --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">
                        {{ __('core.label_inventory_qty') }} <span class="text-red-500">*</span>
                    </label>
                    <input name="inventory"
                           value="{{ old('inventory', $product?->inventory) }}"
                           type="number" min="0" step="0.001"
                           class="w-full h-10 px-3 rounded-xl border @error('inventory') border-red-400 @else border-slate-200 @enderror bg-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-emerald-200"
                           placeholder="{{ __('core.placeholder_inventory') }}">
                    @error('inventory')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">
                        {{ __('core.label_unit_of_measure') }} <span class="text-red-500">*</span>
                    </label>
                    <select name="unit"
                            class="w-full h-10 px-3 rounded-xl border @error('unit') border-red-400 @else border-slate-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <option value="">{{ __('core.select_unit') }}</option>
                        @foreach($units as $val => $label)
                            <option value="{{ $val }}" @selected($oldUnit === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('unit')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <p class="text-xs text-slate-400 -mt-2">
                {{ __('core.inventory_unit_hint', ['qty' => '500 kg']) }}
            </p>

            {{-- Unit price --}}
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('core.label_unit_price') }}</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400 font-medium">€</span>
                    <input name="unit_price"
                           value="{{ old('unit_price', $product?->unit_price) }}"
                           type="number" min="0" step="0.01"
                           class="w-full h-10 pl-7 pr-3 rounded-xl border @error('unit_price') border-red-400 @else border-slate-200 @enderror bg-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-emerald-200"
                           placeholder="0.00">
                </div>
                <p class="mt-1 text-xs text-slate-400">{{ __('core.unit_price_hint') }}</p>
                @error('unit_price')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- ── Product image ── --}}
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-emerald-100 flex items-center gap-3">
            <div class="h-8 w-8 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="font-semibold text-sm text-slate-900">{{ __('core.section_product_image') }}</div>
        </div>

        <div class="px-6 py-5">
            <div class="flex items-start gap-5">
                {{-- Preview --}}
                <div class="flex-shrink-0">
                    <div class="h-24 w-24 rounded-2xl border border-slate-200 bg-slate-50 overflow-hidden flex items-center justify-center">
                        <template x-if="imagePreview">
                            <img :src="imagePreview" class="h-full w-full object-cover">
                        </template>
                        <template x-if="!imagePreview">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </template>
                    </div>
                </div>

                {{-- Upload --}}
                <div class="flex-1">
                    <label class="block text-xs font-medium text-slate-500 mb-2">{{ __('core.label_upload_photo') }}</label>
                    <label class="flex items-center gap-3 px-4 py-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-300 transition cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span class="text-sm text-slate-500">{{ __('core.click_to_choose_image') }}</span>
                        <input type="file" name="image" accept="image/*" class="hidden"
                               x-on:change="onImageChange($event)">
                    </label>
                    <p class="mt-1.5 text-xs text-slate-400">{{ __('core.image_hint') }}</p>
                    @error('image')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

</div>{{-- end Alpine root --}}
