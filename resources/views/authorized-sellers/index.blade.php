@extends('layouts.app')
@section('content')

{{-- ── Header ──────────────────────────────────────────────────────── --}}
<div class="flex items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Authorized Sellers</h1>
        <p class="text-sm text-slate-500 mt-1">Verified companies and their product offerings.</p>
    </div>
</div>

{{-- ── Filter bar ───────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('authorized-sellers.index') }}"
      class="flex flex-wrap items-center gap-3 mb-6">

    {{-- Category filter --}}
    @if($categories->isNotEmpty())
    <select name="category"
            onchange="this.form.submit()"
            class="h-10 pl-3 pr-8 rounded-xl border border-slate-200 bg-white text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-300">
        <option value="">All Categories</option>
        @foreach($categories as $cat)
            <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
        @endforeach
    </select>
    @endif

    {{-- Company search --}}
    <div class="flex-1 min-w-48 max-w-xs relative">
        <input type="text"
               name="search"
               value="{{ request('search') }}"
               placeholder="Search company…"
               class="w-full h-10 pl-9 pr-4 rounded-xl border border-slate-200 bg-white text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400"
             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
        </svg>
    </div>

    @if(request('search') || request('category'))
    <a href="{{ route('authorized-sellers.index') }}"
       class="h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-sm text-slate-600 flex items-center gap-1 transition">
        ✕ Clear
    </a>
    @endif
</form>

{{-- ── Results count --}}
@if($sellers->total() > 0)
<p class="text-xs text-slate-400 mb-4">
    Showing {{ $sellers->firstItem() }}–{{ $sellers->lastItem() }} of {{ $sellers->total() }} companies
</p>
@endif

{{-- ── Seller cards ─────────────────────────────────────────────────── --}}
@if($sellers->isEmpty())
<div class="text-center py-20 text-slate-400">
    <div class="text-4xl mb-3">🏢</div>
    <p class="text-sm">No authorized sellers found.</p>
</div>
@else
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
    @foreach($sellers as $seller)
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
        <div class="flex gap-0">

            {{-- Col 1: Featured image --}}
            <div class="w-44 flex-shrink-0 bg-slate-50 flex items-center justify-center overflow-hidden border-r border-slate-100">
                @if($seller->featuredImageUrl())
                    <img src="{{ $seller->featuredImageUrl() }}"
                         alt="{{ $seller->company_name }}"
                         class="w-full h-full object-cover"
                         style="min-height:180px;">
                @else
                    <div class="flex flex-col items-center justify-center p-4 text-slate-300" style="min-height:180px;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="text-xs">No image</span>
                    </div>
                @endif
            </div>

            {{-- Col 2: Company info + products --}}
            <div class="flex-1 p-5 min-w-0">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <h2 class="font-semibold text-slate-900 text-base leading-snug">{{ $seller->company_name }}</h2>
                    @if($seller->category)
                    <span class="flex-shrink-0 text-xs px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-700">
                        {{ $seller->category }}
                    </span>
                    @endif
                </div>

                @if($seller->short_description)
                <p class="text-sm text-slate-500 mb-3 leading-relaxed line-clamp-3">{{ $seller->short_description }}</p>
                @endif

                {{-- Products grid (2 columns, up to 20) --}}
                @if($seller->products->isNotEmpty())
                <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 mb-3">
                    @foreach($seller->products->take(20) as $product)
                    <div class="flex items-center gap-1.5 text-xs text-slate-600 truncate">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 flex-shrink-0"></span>
                        <span class="truncate">{{ $product->name }}</span>
                    </div>
                    @endforeach
                </div>
                @if($seller->products->count() > 20)
                <p class="text-xs text-slate-400 mb-3">+{{ $seller->products->count() - 20 }} more products</p>
                @endif
                @endif

                {{-- Contact info --}}
                @if($seller->address || $seller->phone || $seller->email)
                <div class="space-y-1 mb-3">
                    @if($seller->address)
                    <div class="flex items-center gap-1.5 text-xs text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>{{ $seller->address }}</span>
                    </div>
                    @endif
                    @if($seller->phone)
                    <div class="flex items-center gap-1.5 text-xs text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <a href="tel:{{ $seller->phone }}" class="hover:text-emerald-700 transition">{{ $seller->phone }}</a>
                    </div>
                    @endif
                    @if($seller->email)
                    <div class="flex items-center gap-1.5 text-xs text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <a href="mailto:{{ $seller->email }}" class="hover:text-emerald-700 transition">{{ $seller->email }}</a>
                    </div>
                    @endif
                </div>
                @endif

                @if($seller->website_url)
                <a href="{{ $seller->website_url }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 hover:underline">
                    Visit website
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6M15 3h6v6M10 14L21 3"/>
                    </svg>
                </a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Pagination ───────────────────────────────────────────────────── --}}
@if($sellers->hasPages())
<div class="mt-8 flex justify-center">
    <div class="flex items-center gap-1 flex-wrap">
        {{-- Previous --}}
        @if($sellers->onFirstPage())
            <span class="h-9 w-9 flex items-center justify-center rounded-xl border border-slate-200 text-slate-300 text-sm cursor-not-allowed">‹</span>
        @else
            <a href="{{ $sellers->previousPageUrl() }}"
               class="h-9 w-9 flex items-center justify-center rounded-xl border border-slate-200 hover:bg-emerald-50 text-sm text-slate-600 transition">‹</a>
        @endif

        {{-- Pages --}}
        @foreach($sellers->getUrlRange(1, $sellers->lastPage()) as $page => $url)
            @if($page == $sellers->currentPage())
                <span class="h-9 w-9 flex items-center justify-center rounded-xl text-sm font-semibold text-white"
                      style="background:#059669;">{{ $page }}</span>
            @else
                <a href="{{ $url }}"
                   class="h-9 w-9 flex items-center justify-center rounded-xl border border-slate-200 hover:bg-emerald-50 text-sm text-slate-600 transition">{{ $page }}</a>
            @endif
        @endforeach

        {{-- Next --}}
        @if($sellers->hasMorePages())
            <a href="{{ $sellers->nextPageUrl() }}"
               class="h-9 w-9 flex items-center justify-center rounded-xl border border-slate-200 hover:bg-emerald-50 text-sm text-slate-600 transition">›</a>
        @else
            <span class="h-9 w-9 flex items-center justify-center rounded-xl border border-slate-200 text-slate-300 text-sm cursor-not-allowed">›</span>
        @endif
    </div>
</div>
@endif

@endif

@endsection
