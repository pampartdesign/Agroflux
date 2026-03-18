<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('market.marketplace') }} — AgroFlux</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">

{{-- ── Public header ──────────────────────────────────────────────── --}}
<header class="sticky top-0 z-30 bg-white border-b border-emerald-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between gap-4">

        {{-- Logo --}}
        <a href="{{ route('public.marketplace') }}" class="flex items-center gap-3 shrink-0">
            <div class="h-9 w-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">A</div>
            <div class="hidden sm:block">
                <div class="font-semibold leading-tight text-slate-900">AgroFlux</div>
                <div class="text-xs text-slate-500">{{ __('market.marketplace') }}</div>
            </div>
        </a>

        {{-- Search bar (header) --}}
        <form method="GET" action="{{ route('public.marketplace') }}" class="flex-1 max-w-xl mx-4">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                {{-- Preserve other filter params --}}
                @foreach(request()->except('search','page') as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <input name="search"
                       value="{{ request('search') }}"
                       placeholder="{{ __('market.search_placeholder') }}"
                       class="w-full h-10 pl-9 pr-4 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400 transition">
            </div>
        </form>

        {{-- Right actions --}}
        <div class="flex items-center gap-3 shrink-0">
            @include('public._locale_switcher')
            @include('public._customer_nav')
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
            @auth
                <a href="{{ route('dashboard') }}"
                   class="hidden sm:inline-flex items-center h-9 px-4 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition text-sm font-medium">
                    {{ __('market.dashboard') }}
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="hidden sm:inline-flex items-center h-9 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm">
                    {{ __('market.sign_in') }}
                </a>
            @endauth
        </div>
    </div>
</header>

{{-- ── Hero strip ─────────────────────────────────────────────────── --}}
@if(!request()->hasAny(['search','category_id','region_id','city','type']))
<div style="background:linear-gradient(135deg,#064e3b 0%,#065f46 50%,#047857 100%);" class="text-white">
    <div class="max-w-7xl mx-auto px-6 py-10 flex flex-col sm:flex-row items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-bold mb-2">{{ __('market.hero_title') }}</h1>
            <p class="text-emerald-200 max-w-lg">{{ __('market.hero_desc') }}</p>
            <div class="flex items-center gap-6 mt-4 text-sm text-emerald-100">
                <span class="flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('market.hero_active_listings', ['count' => $totalListings]) }}
                </span>
                <span class="flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    {{ __('market.hero_full_traceability') }}
                </span>
                <span class="flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ __('market.hero_greek_producers') }}
                </span>
            </div>
        </div>
        <div class="hidden lg:flex items-center gap-3">
            <div class="text-center bg-white/10 rounded-2xl p-4 min-w-[90px]">
                <div class="text-2xl font-bold">{{ $totalListings }}</div>
                <div class="text-xs text-emerald-200 mt-0.5">{{ __('market.stat_listings') }}</div>
            </div>
            <div class="text-center bg-white/10 rounded-2xl p-4 min-w-[90px]">
                <div class="text-2xl font-bold">{{ $categories->count() }}</div>
                <div class="text-xs text-emerald-200 mt-0.5">{{ __('market.stat_categories') }}</div>
            </div>
            <div class="text-center bg-white/10 rounded-2xl p-4 min-w-[90px]">
                <div class="text-2xl font-bold">{{ $regions->count() }}</div>
                <div class="text-xs text-emerald-200 mt-0.5">{{ __('market.stat_regions') }}</div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- Active search/filter summary banner --}}
    @if(request()->hasAny(['search','category_id','region_id','city','type']))
    <div class="mb-5 flex items-center justify-between gap-3 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3">
        <div class="flex items-center gap-2 flex-wrap text-sm text-emerald-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            <span class="font-medium">{{ __('market.filtered_results') }}</span>
            @if(request('search'))
                <span class="px-2 py-0.5 rounded-full bg-emerald-100 border border-emerald-200 text-xs">"{{ request('search') }}"</span>
            @endif
            @if(request('type'))
                <span class="px-2 py-0.5 rounded-full bg-emerald-100 border border-emerald-200 text-xs">{{ request('type') === 'instock' ? __('market.filter_instock_only') : __('market.filter_preorder_only') }}</span>
            @endif
        </div>
        <a href="{{ route('public.marketplace') }}"
           class="shrink-0 text-xs text-emerald-600 hover:text-emerald-800 font-medium transition">
            ✕ {{ __('market.clear_filters') }}
        </a>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- ── Sidebar filters ────────────────────────────────── --}}
        <aside class="lg:col-span-1">
            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-5 sticky top-24">
                <div class="font-semibold text-slate-900 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    {{ __('market.filters') }}
                </div>
                <form method="GET" class="space-y-4">

                    {{-- Category (hierarchical) --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">{{ __('market.filter_category') }}</label>
                        <select name="category_id"
                                class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            <option value="">{{ __('market.filter_all_categories') }}</option>
                            @foreach($categories as $cat)
                                @if($cat->children->count() > 0)
                                    <optgroup label="{{ $cat->name }}">
                                        <option value="{{ $cat->id }}" @selected((int)request('category_id') === $cat->id)>{{ $cat->name }} — All</option>
                                        @foreach($cat->children->sortBy('name') as $child)
                                            <option value="{{ $child->id }}" @selected((int)request('category_id') === $child->id)>
                                                &nbsp;&nbsp;└ {{ $child->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @else
                                    <option value="{{ $cat->id }}" @selected((int)request('category_id') === $cat->id)>{{ $cat->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    {{-- Type --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">{{ __('market.filter_availability') }}</label>
                        <select name="type"
                                class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            <option value="">{{ __('market.filter_all_types') }}</option>
                            <option value="instock"  @selected(request('type') === 'instock')>{{ __('market.filter_instock_only') }}</option>
                            <option value="preorder" @selected(request('type') === 'preorder')>{{ __('market.filter_preorder_only') }}</option>
                        </select>
                    </div>

                    {{-- Region --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">{{ __('market.filter_region') }}</label>
                        <select name="region_id"
                                class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            <option value="">{{ __('market.filter_all_regions') }}</option>
                            @foreach($regions as $r)
                                <option value="{{ $r->id }}" @selected((int)request('region_id') === $r->id)>{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- City --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">{{ __('market.filter_city') }}</label>
                        <input name="city" value="{{ request('city') }}"
                               class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
                               placeholder="{{ __('market.filter_city_placeholder') }}">
                    </div>

                    <button type="submit"
                            class="w-full h-10 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
                        {{ __('market.apply_filters') }}
                    </button>

                    @if(request()->hasAny(['category_id','region_id','city','type','search']))
                        <a href="{{ route('public.marketplace') }}"
                           class="block text-center text-xs text-slate-400 hover:text-slate-600 transition">
                            {{ __('market.clear_all_filters') }}
                        </a>
                    @endif
                </form>
            </div>
        </aside>

        {{-- ── Listings grid ────────────────────────────────────── --}}
        <main class="lg:col-span-3">
            @if($listings->isEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-16 text-center">
                    <div class="h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <div class="text-slate-700 font-semibold mb-1">{{ __('market.no_listings_found') }}</div>
                    <div class="text-slate-400 text-sm mb-4">{{ __('market.no_listings_desc') }}</div>
                    <a href="{{ route('public.marketplace') }}"
                       class="inline-flex items-center h-9 px-4 rounded-xl bg-emerald-600 text-white text-sm hover:bg-emerald-700 transition">
                        {{ __('market.clear_filters') }}
                    </a>
                </div>
            @else
                <div class="mb-4 flex items-center justify-between">
                    <div class="text-xs text-slate-500">
                        Showing <span class="font-medium text-slate-700">{{ $listings->firstItem() }}–{{ $listings->lastItem() }}</span>
                        of <span class="font-medium text-slate-700">{{ $listings->total() }}</span> listings
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($listings as $l)
                        @php
                            $seller = $tenants[$l->tenant_id] ?? null;
                            $qr     = $productQrs[$l->product_id] ?? null;
                            $hasImg = !empty($l->product?->image_path);
                            $initials = strtoupper(substr($l->product?->default_name ?? '?', 0, 2));
                        @endphp
                        <a href="{{ route('public.marketplace.show', $l) }}"
                           class="group flex flex-col rounded-2xl border border-slate-200 bg-white hover:border-emerald-300 hover:shadow-lg transition-all duration-200 overflow-hidden">

                            {{-- ── Product image / placeholder ── --}}
                            <div class="relative h-44 overflow-hidden"
                                 style="background:#f0fdf4;">
                                @if($hasImg)
                                    <img src="{{ asset('storage/' . $l->product?->image_path) }}"
                                         alt="{{ $l->product?->default_name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <div class="h-14 w-14 rounded-2xl bg-emerald-100 border border-emerald-200 flex items-center justify-center">
                                                <span class="text-emerald-700 font-bold text-lg">{{ $initials }}</span>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                    </div>
                                @endif

                                {{-- Overlaid badges --}}
                                <div class="absolute top-3 left-3 flex items-center gap-1.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold shadow-sm
                                        {{ $l->type === 'instock' ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white' }}">
                                        {{ $l->type === 'instock' ? __('market.badge_instock') : __('market.badge_preorder') }}
                                    </span>
                                </div>

                                @if($qr)
                                    <div class="absolute top-3 right-3">
                                        <span onclick="event.preventDefault(); event.stopPropagation(); window.open('{{ route('public.trace.product', $qr->public_token) }}', '_blank');"
                                              class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/90 border border-slate-200 text-xs text-slate-600 hover:bg-white shadow-sm cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path d="M12 4v1m0 14v1M4 12h1m14 0h1m-2.1-7.1-.7.7M6.8 17.2l-.7.7m0-11.4.7.7m9.9 9.9.7.7"/>
                                            </svg>
                                            {{ __('market.qr_trace') }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- ── Card body ── --}}
                            <div class="p-4 flex-1 flex flex-col">
                                {{-- Category breadcrumb --}}
                                <div class="flex items-center gap-1 text-xs text-slate-400 mb-1.5 flex-wrap">
                                    @if($l->product?->category)
                                        <span>{{ $l->product->category->name }}</span>
                                    @endif
                                    @if($l->product?->subcategory)
                                        <span class="text-slate-300">›</span>
                                        <span class="text-emerald-600">{{ $l->product->subcategory->name }}</span>
                                    @endif
                                </div>

                                {{-- Product name --}}
                                <div class="font-semibold text-slate-900 group-hover:text-emerald-700 transition text-base leading-snug mb-1">
                                    {{ $l->product?->default_name ?? 'Unknown Product' }}
                                </div>

                                {{-- Seller --}}
                                @if($seller)
                                    <div class="text-xs text-slate-400 mb-3">
                                        <span class="inline-flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            {{ $seller->name }}
                                        </span>
                                    </div>
                                @endif

                                <div class="mt-auto flex items-end justify-between gap-2 pt-2 border-t border-slate-100">
                                    <div>
                                        <div class="text-xl font-bold text-emerald-700">€{{ number_format($l->price, 2) }}</div>
                                        <div class="text-xs text-slate-400">per {{ $l->product?->unit ?? 'unit' }}</div>
                                    </div>
                                    <div class="text-right text-xs text-slate-500">
                                        @if($l->type === 'instock')
                                            <span class="{{ ($l->available_qty ?? 0) > 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                                                Qty: {{ $l->available_qty ?? '—' }}
                                            </span>
                                        @else
                                            <span>{{ optional($l->expected_harvest_at)->format('M Y') ?? __('market.soon') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- CTA --}}
                            <div class="px-4 pb-4">
                                <div class="w-full h-9 rounded-xl text-white text-sm font-medium flex items-center justify-center gap-1.5 transition group-hover:opacity-90"
                                     style="background:#059669;">
                                    {{ __('market.view_add_to_cart') }}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">{{ $listings->links() }}</div>
            @endif
        </main>
    </div>
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

</body>
</html>
