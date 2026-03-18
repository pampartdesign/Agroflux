<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $listing->product->default_name }} — AgroFlux {{ __('market.marketplace') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">

{{-- ── Public header ──────────────────────────────────────────────── --}}
<header class="sticky top-0 z-30 bg-white border-b border-emerald-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between gap-4">
        <a href="{{ route('public.marketplace') }}" class="flex items-center gap-3 shrink-0">
            <div class="h-9 w-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">A</div>
            <div class="hidden sm:block">
                <div class="font-semibold leading-tight text-slate-900">AgroFlux</div>
                <div class="text-xs text-slate-500">{{ __('market.marketplace') }}</div>
            </div>
        </a>

        {{-- Mini search bar --}}
        <form method="GET" action="{{ route('public.marketplace') }}" class="flex-1 max-w-md mx-4 hidden md:block">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input name="search"
                       placeholder="{{ __('market.search_marketplace_placeholder') }}"
                       class="w-full h-9 pl-9 pr-4 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 transition">
            </div>
        </form>

        <div class="flex items-center gap-3 shrink-0">
            @include('public._locale_switcher')
            @php $cartCount = count(session('cart', [])); @endphp
            <a href="{{ route('cart.show') }}"
               class="relative inline-flex items-center gap-2 h-9 px-4 rounded-xl border border-emerald-200 bg-white hover:bg-emerald-50 transition text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.2 6H19M9 21a1 1 0 100-2 1 1 0 000 2zm10 0a1 1 0 100-2 1 1 0 000 2z"/>
                </svg>
                <span class="hidden sm:inline">{{ __('market.cart') }}</span>
                @if($cartCount > 0)
                    <span class="absolute -top-1.5 -right-1.5 h-5 w-5 rounded-full bg-emerald-600 text-white text-xs flex items-center justify-center font-bold">
                        {{ $cartCount }}
                    </span>
                @endif
            </a>
            @include('public._customer_nav')
        </div>
    </div>
</header>

<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-6">
        <a href="{{ route('public.marketplace') }}" class="hover:text-emerald-700 transition">{{ __('market.marketplace') }}</a>
        @if($listing->product->category)
            <span class="text-slate-300">/</span>
            <a href="{{ route('public.marketplace', ['category_id' => $listing->product->category_id]) }}"
               class="hover:text-emerald-700 transition">{{ $listing->product->category->name }}</a>
        @endif
        @if($listing->product->subcategory)
            <span class="text-slate-300">/</span>
            <a href="{{ route('public.marketplace', ['category_id' => $listing->product->subcategory_id]) }}"
               class="hover:text-emerald-700 transition">{{ $listing->product->subcategory->name }}</a>
        @endif
        <span class="text-slate-300">/</span>
        <span class="text-slate-900 font-medium truncate">{{ $listing->product->default_name }}</span>
    </nav>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- 3-column layout: LEFT Filters | CENTER Product | RIGHT Cart --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div style="display:grid; grid-template-columns:minmax(0,2fr) minmax(0,3fr) minmax(0,2fr); gap:1.5rem;"
         class="block lg:grid">

        {{-- ══ LEFT: Marketplace filter sidebar ══ --}}
        <div class="mb-6 lg:mb-0">

            {{-- Sticky filter panel --}}
            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5 sticky top-24">

                <div class="font-semibold text-slate-900 mb-1 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    {{ __('market.browse_products') }}
                </div>
                <p class="text-xs text-slate-400 mb-4">{{ __('market.filter_find_more') }}</p>

                <form method="GET" action="{{ route('public.marketplace') }}" class="space-y-4">

                    {{-- Text search --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">{{ __('market.filter_search') }}</label>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input name="search"
                                   placeholder="{{ __('market.product_name_placeholder') }}"
                                   class="w-full h-9 pl-8 pr-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        </div>
                    </div>

                    {{-- Category (hierarchical) --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">{{ __('market.filter_category') }}</label>
                        <select name="category_id"
                                class="w-full h-9 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            <option value="">{{ __('market.filter_all_categories') }}</option>
                            @foreach($categories as $cat)
                                @if($cat->children->count() > 0)
                                    <optgroup label="{{ $cat->name }}">
                                        <option value="{{ $cat->id }}"
                                            @selected($listing->product->category_id == $cat->id)>
                                            {{ $cat->name }} — All
                                        </option>
                                        @foreach($cat->children->sortBy('name') as $child)
                                            <option value="{{ $child->id }}"
                                                @selected($listing->product->subcategory_id == $child->id)>
                                                &nbsp;&nbsp;└ {{ $child->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @else
                                    <option value="{{ $cat->id }}"
                                        @selected($listing->product->category_id == $cat->id)>
                                        {{ $cat->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    {{-- Availability --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">{{ __('market.filter_availability') }}</label>
                        <select name="type"
                                class="w-full h-9 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            <option value="">{{ __('market.filter_all_types') }}</option>
                            <option value="instock">{{ __('market.filter_instock_only') }}</option>
                            <option value="preorder">{{ __('market.filter_preorder_only') }}</option>
                        </select>
                    </div>

                    {{-- Region --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">{{ __('market.filter_region') }}</label>
                        <select name="region_id"
                                class="w-full h-9 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            <option value="">{{ __('market.filter_all_regions') }}</option>
                            @foreach($regions as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- City --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">{{ __('market.filter_city') }}</label>
                        <input name="city"
                               placeholder="{{ __('market.filter_city_placeholder') }}"
                               class="w-full h-9 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    </div>

                    <button type="submit"
                            class="w-full h-9 rounded-xl text-white text-sm font-medium hover:opacity-90 transition"
                            style="background:#059669;">
                        {{ __('market.search_marketplace_btn') }}
                    </button>

                    <a href="{{ route('public.marketplace') }}"
                       class="block text-center text-xs text-slate-400 hover:text-slate-600 transition">
                        {{ __('market.all_listings') }}
                    </a>
                </form>

                {{-- Quick category chips --}}
                @if($categories->count() > 0)
                    <div class="mt-5 pt-4 border-t border-slate-100">
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">{{ __('market.quick_browse') }}</div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($categories->take(8) as $cat)
                                <a href="{{ route('public.marketplace', ['category_id' => $cat->id]) }}"
                                   class="inline-flex items-center px-2.5 py-1 rounded-full text-xs transition
                                       {{ $listing->product->category_id == $cat->id
                                           ? 'bg-emerald-600 text-white'
                                           : 'bg-slate-100 text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' }}">
                                    {{ $cat->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>{{-- /sticky filter panel --}}

            {{-- ── AD BANNER — under filter ── --}}
            <div class="mt-4 rounded-2xl overflow-hidden border border-slate-200 shadow-sm" style="background:#f8fafc;">
                <div class="flex items-center justify-between px-3 py-1.5 border-b border-slate-100">
                    <span class="text-xs text-slate-300 font-medium uppercase tracking-widest">Ad</span>
                    <span class="text-xs text-slate-200">300×250</span>
                </div>
                <div class="flex items-center justify-center p-4" style="min-height:200px;">
                    <div class="w-full h-full rounded-xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center gap-2 p-6" style="min-height:160px;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                        </svg>
                        <span class="text-xs text-slate-300 font-medium">Advertisement Space</span>
                        <span class="text-xs text-slate-200">300 × 250</span>
                    </div>
                </div>
            </div>

        </div>{{-- /LEFT --}}

        {{-- ══ CENTER: Product details ══ --}}
        <div class="mb-6 lg:mb-0" style="display:flex; flex-direction:column; gap:1.25rem;">

            {{-- Product image --}}
            @php $hasImg = !empty($listing->product->image_path); @endphp
            <div class="rounded-2xl overflow-hidden border border-slate-200 bg-white shadow-sm">
                @if($hasImg)
                    <img src="{{ asset('storage/' . $listing->product->image_path) }}"
                         alt="{{ $listing->product->default_name }}"
                         class="w-full max-h-72 object-cover">
                @else
                    <div class="w-full flex items-center justify-center" style="height:160px; background:linear-gradient(135deg,#f0fdf4,#dcfce7);">
                        <div class="flex flex-col items-center gap-2">
                            <div class="h-16 w-16 rounded-2xl bg-emerald-100 border-2 border-emerald-200 flex items-center justify-center">
                                <span class="text-2xl font-bold text-emerald-600">{{ strtoupper(substr($listing->product->default_name ?? '?', 0, 2)) }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="px-5 py-4">
                    {{-- Badges --}}
                    <div class="flex items-center gap-2 flex-wrap mb-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            {{ $listing->type === 'instock' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-amber-100 text-amber-700 border border-amber-200' }}">
                            {{ $listing->type === 'instock' ? __('market.badge_instock_detail') : __('market.badge_preorder') }}
                        </span>
                        @if($listing->product->category)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-slate-100 border border-slate-200 text-xs text-slate-600">
                                {{ $listing->product->category->name }}
                            </span>
                        @endif
                        @if($listing->product->subcategory)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-xs text-emerald-700">
                                {{ $listing->product->subcategory->name }}
                            </span>
                        @endif
                        @if($qr)
                            <a href="{{ route('public.trace.product', $qr->public_token) }}"
                               class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-sky-50 border border-sky-200 text-xs text-sky-700 hover:bg-sky-100 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                {{ __('market.view_traceability') }}
                            </a>
                        @endif
                    </div>

                    <h1 class="text-xl font-bold text-slate-900 mb-1">{{ $listing->product->default_name }}</h1>

                    @if($listing->product->default_description)
                        <p class="text-sm text-slate-600 leading-relaxed">{{ $listing->product->default_description }}</p>
                    @endif
                </div>
            </div>

            {{-- ── Traceability section ── --}}
            @if($qr)
                <div class="rounded-2xl border border-sky-100 bg-white shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-sky-50 flex items-center gap-2" style="background:linear-gradient(to right,#f0f9ff,#e0f2fe);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="#0284c7" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span class="text-sm font-semibold" style="color:#075985;">{{ __('market.product_traceability') }}</span>
                        <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#dbeafe; color:#1d4ed8; border:1px solid #bfdbfe;">{{ __('market.traceability_verified_badge') }}</span>
                    </div>
                    <div class="p-5">
                        <p class="text-sm text-slate-600 mb-4 leading-relaxed">
                            {{ __('market.traceability_desc') }}
                        </p>
                        <div class="flex items-center gap-3 flex-wrap">
                            <button type="button"
                                    onclick="openTraceModal('{{ route('public.trace.product', $qr->public_token) }}')"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition"
                                    style="background:#0284c7;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                {{ __('market.view_full_traceability') }}
                            </button>
                            <span class="flex items-center gap-1.5 text-xs text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                {{ __('market.qr_powered') }}
                            </span>
                        </div>
                    </div>
                </div>
            @else
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span class="text-sm font-semibold text-slate-500">{{ __('market.product_traceability') }}</span>
                    </div>
                    <p class="text-xs text-slate-400">{{ __('market.no_traceability') }}</p>
                </div>
            @endif

            {{-- ── Details grid ── --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-3">{{ __('market.product_details') }}</div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-3">
                        <div class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">{{ __('market.detail_price') }}</div>
                        <div class="text-xl font-bold text-emerald-700">€{{ number_format($listing->price, 2) }}</div>
                        <div class="text-xs text-slate-400">per {{ $listing->product->unit ?? 'unit' }}</div>
                    </div>

                    @if($listing->type === 'instock')
                        <div class="rounded-xl bg-slate-50 border border-slate-200 p-3">
                            <div class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">{{ __('market.detail_available_qty') }}</div>
                            <div class="text-xl font-bold text-slate-900">{{ $listing->available_qty ?? '—' }}</div>
                            <div class="text-xs text-slate-400">{{ $listing->product->unit ?? 'units' }}</div>
                        </div>
                    @else
                        <div class="rounded-xl bg-slate-50 border border-slate-200 p-3">
                            <div class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">{{ __('market.detail_harvest') }}</div>
                            <div class="text-base font-bold text-slate-900">
                                {{ optional($listing->expected_harvest_at)->format('d M Y') ?? '—' }}
                            </div>
                        </div>
                    @endif

                    @if($listing->type === 'preorder')
                        <div class="rounded-xl bg-amber-50 border border-amber-200 p-3">
                            <div class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">{{ __('market.detail_deposit') }}</div>
                            <div class="text-xl font-bold text-amber-600">{{ $listing->upfront_percent }}%</div>
                            <div class="text-xs text-slate-400">{{ __('market.detail_upfront') }}</div>
                        </div>
                    @endif

                    @if($listing->product->sku)
                        <div class="rounded-xl bg-slate-50 border border-slate-200 p-3">
                            <div class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">{{ __('market.detail_sku') }}</div>
                            <div class="text-sm font-mono text-slate-700 font-semibold">{{ $listing->product->sku }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── Seller info ── --}}
            @if($seller)
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4">
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-3">{{ __('market.sold_by') }}</div>
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full flex items-center justify-center font-bold shrink-0"
                             style="background:#d1fae5; color:#065f46;">
                            {{ strtoupper(substr($seller->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-semibold text-slate-900">{{ $seller->name }}</div>
                            <div class="flex items-center gap-1 text-xs text-emerald-600 mt-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ __('market.verified_producer') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── Payment note ── --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4">
                <div class="flex items-start gap-3">
                    <div class="h-8 w-8 rounded-xl flex items-center justify-center shrink-0 mt-0.5" style="background:#f1f5f9;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="5" width="20" height="14" rx="2"/>
                            <path d="M2 10h20"/>
                        </svg>
                    </div>
                    <div class="text-sm text-slate-600">
                        {{ __('market.payment_wire_note') }}
                        @if($listing->type === 'preorder')
                            {{ __('market.deposit_note', ['pct' => $listing->upfront_percent]) }}
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── AD BANNER — under traceability ── --}}
            <div class="rounded-2xl overflow-hidden border border-slate-200 shadow-sm" style="background:#f8fafc;">
                <div class="flex items-center justify-between px-3 py-1.5 border-b border-slate-100">
                    <span class="text-xs text-slate-300 font-medium uppercase tracking-widest">Ad</span>
                    <span class="text-xs text-slate-200">728×90</span>
                </div>
                <div class="flex items-center justify-center p-4" style="min-height:110px;">
                    <div class="w-full rounded-xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center gap-1.5 p-4" style="min-height:80px;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                        </svg>
                        <span class="text-xs text-slate-300 font-medium">Advertisement Space</span>
                        <span class="text-xs text-slate-200">728 × 90</span>
                    </div>
                </div>
            </div>

        </div>{{-- /CENTER --}}

        {{-- ══ RIGHT: Add to cart panel ══ --}}
        <div>
            <div class="rounded-2xl border border-emerald-200 bg-white shadow-sm p-5 sticky top-24">

                <div class="mb-4 pb-4 border-b border-slate-100">
                    <div class="text-2xl font-bold text-emerald-700">€{{ number_format($listing->price, 2) }}</div>
                    <div class="text-xs text-slate-400 mt-0.5">per {{ $listing->product->unit ?? 'unit' }}</div>
                </div>

                @if($listing->type === 'instock')
                    <div class="flex items-center gap-2 mb-4 text-sm">
                        <span class="h-2 w-2 rounded-full" style="background:#22c55e;"></span>
                        <span class="text-emerald-700 font-medium">{{ __('market.in_stock') }}</span>
                        @if($listing->available_qty)
                            <span class="text-slate-400 text-xs">{{ $listing->available_qty }} {{ $listing->product->unit ?? 'units' }}</span>
                        @endif
                    </div>
                @else
                    <div class="flex items-center gap-2 mb-4 text-sm">
                        <span class="h-2 w-2 rounded-full" style="background:#f59e0b;"></span>
                        <span class="text-amber-600 font-medium">{{ __('market.pre_order') }}</span>
                        @if($listing->expected_harvest_at)
                            <span class="text-slate-400 text-xs">{{ optional($listing->expected_harvest_at)->format('d M Y') }}</span>
                        @endif
                    </div>
                @endif

                @if($isOwnListing)
                    {{-- Own product notice --}}
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 mb-4 text-center">
                        <div class="text-2xl mb-2">🏪</div>
                        <div class="text-sm font-semibold text-slate-700 mb-1">{{ __('market.own_listing_title') }}</div>
                        <div class="text-xs text-slate-500">{{ __('market.own_listing_desc') }}</div>
                        <a href="{{ route('dashboard') }}"
                           class="mt-3 inline-flex items-center h-8 px-4 rounded-lg border border-slate-200 hover:bg-white transition text-xs font-medium text-slate-600">
                            {{ __('market.go_to_dashboard') }}
                        </a>
                    </div>
                @elseif($differentSeller)
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 mb-4">
                        <div class="font-semibold text-amber-800 text-sm mb-1">{{ __('market.cart_conflict_title') }}</div>
                        <p class="text-amber-700 text-xs mb-3">{{ __('market.cart_conflict_desc', ['seller' => $cartSellerName]) }}</p>
                        <form method="POST" action="{{ route('cart.add', $listing) }}?clear=1">
                            @csrf
                            <input type="hidden" name="qty" value="1">
                            <button type="submit"
                                    class="w-full h-9 rounded-xl text-white text-sm font-medium hover:opacity-90 transition"
                                    style="background:#d97706;">
                                {{ __('market.clear_add_item') }}
                            </button>
                        </form>
                        <a href="{{ route('cart.show') }}" class="block text-center mt-2 text-xs text-amber-600">
                            {{ __('market.keep_current_cart') }}
                        </a>
                    </div>
                @else
                    <form method="POST" action="{{ route('cart.add', $listing) }}" class="space-y-3 mb-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
                                {{ __('market.qty_label', ['unit' => $listing->product->unit ?? 'units']) }}
                            </label>
                            <input type="number" name="qty" min="1" value="{{ $inCart ? $cartQty : 1 }}"
                                   class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-base font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-300 text-center">
                        </div>

                        @if(session('cart_added') == $listing->id)
                            <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-3 py-2 text-sm text-emerald-700 font-medium text-center">
                                {{ __('market.added_to_cart') }}
                            </div>
                        @endif

                        <button type="submit"
                                class="w-full h-10 rounded-xl text-white text-sm font-semibold hover:opacity-90 transition"
                                style="background:#059669;">
                            {{ __('market.order_now') }}
                        </button>
                    </form>

                    @if($inCart)
                        <a href="{{ route('cart.show') }}"
                           class="w-full mb-3 h-10 rounded-xl border-2 text-sm font-semibold transition flex items-center justify-center gap-2"
                           style="border-color:#059669; color:#059669; background:transparent;"
                           onmouseover="this.style.background='#f0fdf4'" onmouseout="this.style.background='transparent'">
                            {{ __('market.checkout_btn') }}
                        </a>
                    @endif

                    {{-- Regular Delivery --}}
                    <button type="button"
                            onclick="openDeliveryModal()"
                            class="w-full h-10 rounded-xl border border-slate-300 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition flex items-center justify-center gap-2 mb-1">
                        {{ __('market.regular_delivery') }}
                    </button>
                @endif

                {{-- Trust badges --}}
                <div class="pt-3 border-t border-slate-100 space-y-2">
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        {{ __('market.trust_verified') }}
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                        </svg>
                        {{ __('market.trust_farm_delivery') }}
                    </div>
                    @if($qr)
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        {{ __('market.trust_full_traceability') }}
                    </div>
                    @endif
                </div>

            </div>
        </div>{{-- /RIGHT --}}

    </div>{{-- /3-col grid --}}
</div>

<footer class="mt-16 border-t border-slate-200 py-8">
    <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
        <div>{{ __('market.footer_tagline') }}</div>
        <div class="flex items-center gap-4">
            <a href="{{ route('login') }}" class="hover:text-slate-600 transition">{{ __('market.sell_on_agroflux') }}</a>
            <span>·</span>
            <span>© {{ date('Y') }} AgroFlux</span>
        </div>
    </div>
</footer>

{{-- ── Traceability modal ──────────────────────────────────────── --}}
<div id="traceModal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background:rgba(0,0,0,0.6);"
     onclick="if(event.target===this)closeTraceModal()">

    <div class="bg-white rounded-2xl w-full flex flex-col shadow-2xl overflow-hidden"
         style="max-width:860px; max-height:90vh;">

        {{-- Modal header --}}
        <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 shrink-0"
             style="background:linear-gradient(to right,#f0f9ff,#e0f2fe);">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="#0284c7" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-sm font-semibold" style="color:#075985;">{{ __('market.product_traceability') }}</span>
            </div>
            <button onclick="closeTraceModal()"
                    class="h-8 w-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition text-lg font-bold leading-none">
                &times;
            </button>
        </div>

        {{-- Iframe --}}
        <iframe id="traceFrame"
                src=""
                class="flex-1 border-0 w-full"
                style="min-height:520px;">
        </iframe>
    </div>
</div>

<script>
function openTraceModal(url) {
    document.getElementById('traceFrame').src = url;
    document.getElementById('traceModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeTraceModal() {
    document.getElementById('traceModal').classList.add('hidden');
    document.getElementById('traceFrame').src = '';
    document.body.style.overflow = '';
}
function openDeliveryModal() {
    document.getElementById('deliveryModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeDeliveryModal() {
    document.getElementById('deliveryModal').classList.add('hidden');
    document.body.style.overflow = '';
}
function selectFreq(val, clickedLabel) {
    // Reset all cards
    document.querySelectorAll('.freq-card').forEach(function(card) {
        card.style.borderColor = '#e2e8f0';
        card.style.background  = '#fff';
        var dot = card.querySelector('.freq-dot');
        dot.style.borderColor  = '#cbd5e1';
        dot.style.background   = 'transparent';
        dot.innerHTML          = '';
        card.querySelector('input[type=radio]').checked = false;
    });
    // Activate clicked
    clickedLabel.style.borderColor = '#059669';
    clickedLabel.style.background  = '#f0fdf4';
    var dot = clickedLabel.querySelector('.freq-dot');
    dot.style.borderColor = '#059669';
    dot.style.background  = '#059669';
    dot.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
    clickedLabel.querySelector('input[type=radio]').checked = true;
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeTraceModal(); closeDeliveryModal(); }
});
// Auto-open delivery modal if there are errors from the delivery request form
@if($errors->any() && old('listing_id') == $listing->id)
    document.addEventListener('DOMContentLoaded', function() { openDeliveryModal(); });
@endif
@if(session('delivery_sent') == $listing->id)
    document.addEventListener('DOMContentLoaded', function() { openDeliveryModal(); });
@endif
</script>

{{-- ── Regular Delivery Subscription Modal ─────────────────────────── --}}
<div id="deliveryModal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background:rgba(0,0,0,0.55);"
     onclick="if(event.target===this)closeDeliveryModal()">

    <div class="bg-white rounded-2xl w-full flex flex-col shadow-2xl overflow-hidden"
         style="max-width:560px; max-height:94vh;">

        {{-- Modal header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-emerald-100 shrink-0"
             style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl flex items-center justify-center shrink-0"
                     style="background:#059669;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-slate-900 text-sm leading-tight">{{ __('market.delivery_subscribe_title') }}</div>
                    <div class="text-xs text-emerald-700 font-medium leading-tight">{{ $listing->product->default_name }} · €{{ number_format($listing->price, 2) }}/{{ $listing->product->unit ?? 'unit' }}</div>
                </div>
            </div>
            <button onclick="closeDeliveryModal()"
                    class="h-8 w-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-white transition text-xl font-bold leading-none">
                &times;
            </button>
        </div>

        {{-- Modal body --}}
        <div class="overflow-y-auto flex-1 p-5">

            @if(session('delivery_sent') == $listing->id)
                {{-- Success state --}}
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6 text-center">
                    <div class="text-4xl mb-3">🎉</div>
                    <div class="font-bold text-emerald-800 text-base mb-1">{{ __('market.delivery_success_title') }}</div>
                    <p class="text-sm text-emerald-700 mb-4 leading-relaxed">
                        {{ __('market.delivery_success_desc') }}
                    </p>
                    <button onclick="closeDeliveryModal()"
                            class="inline-flex items-center h-9 px-6 rounded-xl text-white text-sm font-semibold hover:opacity-90 transition"
                            style="background:#059669;">
                        {{ __('market.delivery_done') }}
                    </button>
                </div>
            @else

                <p class="text-sm text-slate-500 mb-5 leading-relaxed">
                    {{ __('market.delivery_desc') }}
                </p>

                <form method="POST" action="{{ route('delivery.request.store') }}" class="space-y-5" id="deliveryForm">
                    @csrf
                    <input type="hidden" name="listing_id" value="{{ $listing->id }}">

                    {{-- ── Frequency picker ── --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2.5">
                            {{ __('market.delivery_frequency_label') }} <span class="text-red-400">*</span>
                        </label>
                        @error('frequency') <p class="text-red-500 text-xs mb-2">{{ $message }}</p> @enderror
                        <div class="grid grid-cols-2 gap-2">
                            @php
                                $frequencies = [
                                    'daily'    => ['label' => __('market.delivery_freq_daily'),    'sub' => __('market.delivery_freq_daily_sub'),    'emoji' => '📅'],
                                    'weekly'   => ['label' => __('market.delivery_freq_weekly'),   'sub' => __('market.delivery_freq_weekly_sub'),   'emoji' => '📆'],
                                    'biweekly' => ['label' => __('market.delivery_freq_biweekly'), 'sub' => __('market.delivery_freq_biweekly_sub'), 'emoji' => '🗓️'],
                                    'monthly'  => ['label' => __('market.delivery_freq_monthly'),  'sub' => __('market.delivery_freq_monthly_sub'),  'emoji' => '📋'],
                                ];
                                $selectedFreq = old('frequency', 'weekly');
                            @endphp
                            @foreach($frequencies as $val => $freq)
                                <label class="freq-card cursor-pointer rounded-xl border-2 p-3 flex items-center gap-3 transition select-none"
                                       style="{{ $selectedFreq === $val ? 'border-color:#059669; background:#f0fdf4;' : 'border-color:#e2e8f0; background:#fff;' }}"
                                       onclick="selectFreq('{{ $val }}', this)">
                                    <input type="radio" name="frequency" value="{{ $val }}"
                                           {{ $selectedFreq === $val ? 'checked' : '' }}
                                           class="sr-only">
                                    <span class="text-xl shrink-0">{{ $freq['emoji'] }}</span>
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900 leading-tight">{{ $freq['label'] }}</div>
                                        <div class="text-xs text-slate-400 leading-tight">{{ $freq['sub'] }}</div>
                                    </div>
                                    <div class="ml-auto h-5 w-5 rounded-full border-2 shrink-0 flex items-center justify-center freq-dot"
                                         style="{{ $selectedFreq === $val ? 'border-color:#059669; background:#059669;' : 'border-color:#cbd5e1; background:transparent;' }}">
                                        @if($selectedFreq === $val)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- ── Qty + Start date side by side ── --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
                                {{ __('market.delivery_qty_per') }} <span class="text-red-400">*</span>
                            </label>
                            <div class="flex items-center gap-1">
                                <input type="number" name="qty" min="1" required
                                       value="{{ old('qty', 1) }}"
                                       class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-300 transition text-center">
                                <span class="text-xs text-slate-400 shrink-0">{{ $listing->product->unit ?? 'units' }}</span>
                            </div>
                            @error('qty') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
                                {{ __('market.delivery_start_from') }}
                            </label>
                            <input type="date" name="start_date"
                                   value="{{ old('start_date') }}"
                                   min="{{ now()->toDateString() }}"
                                   class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 transition">
                            @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- ── Divider ── --}}
                    <div class="flex items-center gap-3">
                        <div class="flex-1 border-t border-slate-100"></div>
                        <span class="text-xs text-slate-400 uppercase tracking-wide font-semibold">{{ __('market.delivery_your_details') }}</span>
                        <div class="flex-1 border-t border-slate-100"></div>
                    </div>

                    {{-- Name --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
                            {{ __('market.delivery_full_name') }} <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="name" required
                               value="{{ old('name', trim((auth()->user()?->name ?? '') . ' ' . (auth()->user()?->surname ?? ''))) }}"
                               placeholder="{{ __('market.delivery_name_placeholder') }}"
                               class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 transition">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Phone + Email side by side --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
                                {{ __('market.delivery_phone') }} <span class="text-red-400">*</span>
                            </label>
                            <input type="tel" name="phone" required
                                   value="{{ old('phone', auth()->user()?->phone) }}"
                                   placeholder="+30 6900 000000"
                                   class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 transition">
                            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">{{ __('market.label_email') }}</label>
                            <input type="email" name="email"
                                   value="{{ old('email', auth()->user()?->email) }}"
                                   placeholder="your@email.com"
                                   class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 transition">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Delivery Address --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
                            {{ __('market.delivery_address') }} <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="address" required
                               value="{{ old('address', auth()->user()?->address) }}"
                               placeholder="{{ __('market.delivery_address_placeholder') }}"
                               class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 transition">
                        @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">{{ __('market.delivery_notes') }}</label>
                        <textarea name="notes" rows="2"
                                  placeholder="{{ __('market.delivery_notes_placeholder') }}"
                                  class="w-full rounded-xl border border-slate-200 px-3 py-2.5 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 transition resize-none">{{ old('notes') }}</textarea>
                        @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-3 pt-1">
                        <button type="submit"
                                class="flex-1 h-11 rounded-xl text-white text-sm font-bold hover:opacity-90 transition flex items-center justify-center gap-2"
                                style="background:#059669;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            {{ __('market.delivery_subscribe_btn') }}
                        </button>
                        <button type="button" onclick="closeDeliveryModal()"
                                class="h-11 px-4 rounded-xl border border-slate-200 bg-white text-slate-600 text-sm font-medium hover:bg-slate-50 transition">
                            {{ __('market.delivery_cancel') }}
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

</body>
</html>
