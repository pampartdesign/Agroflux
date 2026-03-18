@extends('layouts.app')

@section('content')

{{-- Header --}}
<div class="flex items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('core.products_title') }}</h1>
        <p class="text-sm text-slate-500 mt-1">
            {{ __('core.products_subtitle') }}
        </p>
    </div>
    <a href="{{ route('core.products.create') }}"
       class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-sm text-sm font-medium flex-shrink-0">
        <span class="text-lg leading-none">+</span>
        {{ __('core.new_product') }}
    </a>
</div>

{{-- Search + filter bar --}}
<form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
    <div class="relative flex-1 min-w-48">
        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
        </svg>
        <input name="q" value="{{ request('q') }}"
               class="w-full h-10 pl-9 pr-4 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
               placeholder="{{ __('core.search_by_name_sku') }}">
    </div>

    <select name="category_id"
            class="h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
        <option value="">{{ __('core.all_categories') }}</option>
        @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected((int)request('category_id') === $c->id)>{{ $c->name }}</option>
        @endforeach
    </select>

    <button type="submit"
            class="h-10 px-4 rounded-xl bg-slate-800 text-white text-sm hover:bg-slate-700 transition">
        {{ __('app.search') }}
    </button>

    @if(request('q') || request('category_id'))
        <a href="{{ route('core.products.index') }}"
           class="h-10 px-4 rounded-xl border border-slate-200 bg-white text-sm text-slate-500 hover:bg-slate-50 transition inline-flex items-center">
            {{ __('app.reset') }}
        </a>
    @endif
</form>

@if($products->isEmpty())
    {{-- Empty state --}}
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-10">
        <div class="max-w-md">
            <div class="h-12 w-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M20 7l-8-4-8 4 8 4 8-4z"/>
                    <path d="M4 7v10l8 4 8-4V7"/>
                    <path d="M12 11v10"/>
                </svg>
            </div>
            @if(request('q') || request('category_id'))
                <h2 class="text-lg font-semibold text-slate-900">{{ __('core.no_products_search_title') }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ __('core.no_products_search_hint') }}</p>
                <a href="{{ route('core.products.index') }}"
                   class="mt-4 inline-flex items-center h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm">
                    {{ __('core.clear_filters') }}
                </a>
            @else
                <h2 class="text-lg font-semibold text-slate-900">{{ __('core.no_products_title') }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ __('core.no_products_desc') }}</p>
                <a href="{{ route('core.products.create') }}"
                   class="mt-4 inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition text-sm font-medium">
                    <span class="text-lg leading-none">+</span> {{ __('core.add_first_product') }}
                </a>
            @endif
        </div>
    </div>
@else
    {{-- Result count --}}
    <div class="text-xs text-slate-400 mb-4">
        {{ $products->total() }} product{{ $products->total() !== 1 ? 's' : '' }}
        @if(request('q') || request('category_id'))
            {{ __('core.matching_filter') }}
        @endif
    </div>

    {{-- Card grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($products as $product)
            @php $qr = $productQrs[$product->id] ?? null; @endphp
            <div class="group rounded-2xl border border-slate-200 bg-white hover:border-emerald-200 hover:shadow-md transition overflow-hidden flex flex-col">

                {{-- Card header --}}
                <div class="px-5 pt-5 pb-3 flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="h-10 w-10 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M20 7l-8-4-8 4 8 4 8-4z"/>
                                <path d="M4 7v10l8 4 8-4V7"/>
                                <path d="M12 11v10"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <div class="font-semibold text-slate-900 truncate leading-tight">
                                {{ $product->default_name }}
                            </div>
                            @if($product->category)
                                <div class="text-xs text-slate-400 mt-0.5">{{ $product->category->name }}</div>
                            @else
                                <div class="text-xs text-slate-300 mt-0.5 italic">{{ __('core.no_category') }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- Edit + Delete buttons --}}
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <a href="{{ route('core.products.edit', $product) }}"
                           class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white hover:bg-emerald-50 hover:border-emerald-200 text-slate-400 hover:text-emerald-700 transition"
                           title="{{ __('core.edit_product_title') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('core.products.destroy', $product) }}"
                              onsubmit="return confirm('{{ __('core.confirm_delete_product', ['name' => addslashes($product->default_name)]) }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white hover:bg-red-50 hover:border-red-200 text-slate-400 hover:text-red-600 transition"
                                    title="{{ __('core.delete_product_title') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Description --}}
                @if($product->default_description)
                    <div class="px-5 pb-3 text-xs text-slate-500 line-clamp-2 leading-relaxed">
                        {{ $product->default_description }}
                    </div>
                @endif

                {{-- Meta chips --}}
                <div class="px-5 pb-4 flex flex-wrap items-center gap-2">
                    @if($product->sku)
                        <span class="inline-flex items-center px-2 py-1 rounded-full bg-slate-50 border border-slate-200 text-xs text-slate-500 font-mono">
                            SKU: {{ $product->sku }}
                        </span>
                    @endif
                    @if($product->unit)
                        <span class="inline-flex items-center px-2 py-1 rounded-full bg-slate-50 border border-slate-200 text-xs text-slate-500">
                            {{ $product->unit }}
                        </span>
                    @endif
                </div>

                {{-- Divider --}}
                <div class="border-t border-slate-100 mx-5"></div>

                {{-- Stats row --}}
                <div class="px-5 py-3 flex items-center gap-4 text-xs text-slate-500 flex-wrap">
                    <a href="{{ route('core.listings.index') }}"
                       class="inline-flex items-center gap-1.5 hover:text-emerald-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                            <rect x="9" y="3" width="6" height="4" rx="1"/>
                        </svg>
                        <span class="font-semibold text-slate-700">{{ $product->listings_count }}</span>
                        listing{{ $product->listings_count !== 1 ? 's' : '' }}
                    </a>

                    <span class="text-slate-200">|</span>

                    <a href="{{ route('core.traceability.index') }}"
                       class="inline-flex items-center gap-1.5 hover:text-emerald-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5"/>
                            <path d="M2 12l10 5 10-5"/>
                        </svg>
                        <span class="font-semibold text-slate-700">{{ $product->batches_count }}</span>
                        batch{{ $product->batches_count !== 1 ? 'es' : '' }}
                    </a>

                    @if($qr)
                        <span class="text-slate-200">|</span>
                        <a href="{{ route('public.trace.product', $qr->public_token) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 text-emerald-600 hover:text-emerald-800 transition font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="5" height="5"/>
                                <rect x="16" y="3" width="5" height="5"/>
                                <rect x="3" y="16" width="5" height="5"/>
                                <path d="M21 16h-3v3m3 2v-2m-6 2h3m0-5v2m-3 3h2"/>
                            </svg>
                            {{ __('core.qr_trace') }}
                        </a>
                    @endif
                </div>

                {{-- Create listing CTA --}}
                <div class="mt-auto px-5 pb-5">
                    <a href="{{ route('core.listings.create') }}"
                       class="block w-full h-8 rounded-xl border border-emerald-200 text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition text-xs font-medium text-center leading-8">
                        {{ __('core.create_listing_cta') }}
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
        <div class="mt-6">{{ $products->links() }}</div>
    @endif
@endif

@endsection
