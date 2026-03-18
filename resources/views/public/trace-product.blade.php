<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('market.verified_traceability') }} — {{ $product->default_name }} — AgroFlux</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">

{{-- ── Header ──────────────────────────────────────────────────────── --}}
<header class="sticky top-0 z-30 bg-white border-b border-emerald-100 shadow-sm">
    <div class="max-w-3xl mx-auto px-6 py-4 flex items-center justify-between gap-4">
        <a href="{{ route('public.marketplace') }}" class="flex items-center gap-3 shrink-0">
            <div class="h-9 w-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">A</div>
            <div class="hidden sm:block">
                <div class="font-semibold leading-tight text-slate-900">AgroFlux</div>
                <div class="text-xs text-slate-500">{{ __('market.marketplace') }}</div>
            </div>
        </a>
        <div class="flex items-center gap-3">
            @include('public._locale_switcher')
            @include('public._customer_nav')
            <a onclick="if(history.length>1){event.preventDefault();history.back();}"
               href="{{ route('public.marketplace') }}"
               class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('market.back_to_product') }}
            </a>
            <span class="text-slate-200">|</span>
            <a href="{{ route('public.marketplace') }}"
               class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-slate-600 transition">
                {{ __('market.marketplace_label') }}
            </a>
        </div>
    </div>
</header>

<div class="max-w-3xl mx-auto px-6 py-10">

    {{-- ── Product header ── --}}
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-6 mb-8">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                          style="background:#dcfce7; color:#166534; border:1px solid #bbf7d0;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        {{ __('market.verified_traceability') }}
                    </span>
                    @if($product->category)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs"
                              style="background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;">
                            {{ $product->category->name }}
                            @if($product->subcategory)
                                &rsaquo; {{ $product->subcategory->name }}
                            @endif
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $product->default_name }}</h1>
                @if($product->default_description)
                    <p class="text-sm text-slate-500 mt-1">{{ $product->default_description }}</p>
                @endif
            </div>
            <div class="shrink-0 h-14 w-14 rounded-2xl flex items-center justify-center font-bold text-lg"
                 style="background:#dcfce7; color:#166534;">
                {{ strtoupper(substr($product->default_name, 0, 2)) }}
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-6 text-xs text-slate-500">
            <span class="flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                {{ trans_choice('market.published_batches', $batches->count(), ['count' => $batches->count()]) }}
            </span>
            <span class="flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Powered by AgroFlux
            </span>
        </div>
    </div>

    {{-- ── Timelines ── --}}
    @forelse($batches as $batch)
        @php
            $bqr    = $batchTokens[$batch->id] ?? null;
            $events = $batch->events->sortBy('occurred_at');
        @endphp

        <div class="mb-8">
            {{-- Batch header --}}
            <div class="flex items-center justify-between mb-4 gap-3">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-full flex items-center justify-center shrink-0"
                         style="background:#059669; color:#fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-semibold text-slate-900">{{ __('market.batch_label') }} {{ $batch->code }}</div>
                        <div class="text-xs text-slate-400">{{ __('market.created_label') }} {{ $batch->created_at->format('d M Y') }}</div>
                    </div>
                </div>
                @if($bqr)
                    <a href="{{ route('public.trace', $bqr->public_token) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-medium transition"
                       style="border-color:#d1fae5; background:#f0fdf4; color:#059669;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        {{ __('market.full_page') }}
                    </a>
                @endif
            </div>

            {{-- Timeline events --}}
            @if($events->count() > 0)
                <div class="relative pl-6">
                    {{-- Vertical line --}}
                    <div class="absolute left-2.5 top-2 bottom-2 w-0.5" style="background:#d1fae5;"></div>

                    <div class="space-y-4">
                        @foreach($events as $i => $e)
                            <div class="relative">
                                {{-- Dot --}}
                                <div class="absolute -left-6 top-3.5 h-3.5 w-3.5 rounded-full border-2 border-white shadow-sm"
                                     style="background:{{ $loop->last ? '#059669' : '#34d399' }};"></div>

                                <div class="rounded-xl border bg-white shadow-sm p-4 ml-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-slate-900 text-sm">{{ $e->event_type }}</div>
                                            @if($e->notes)
                                                <div class="text-sm text-slate-600 mt-1 leading-relaxed">{{ $e->notes }}</div>
                                            @endif
                                        </div>
                                        <div class="shrink-0 text-xs text-slate-400 whitespace-nowrap mt-0.5">
                                            {{ $e->occurred_at->format('d M Y') }}<br>
                                            <span class="text-slate-300">{{ $e->occurred_at->format('H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Final marker --}}
                <div class="mt-4 pl-6 flex items-center gap-2 text-xs text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ trans_choice('market.traceability_events_recorded', $events->count(), ['count' => $events->count()]) }}
                </div>
            @else
                <div class="rounded-xl border border-dashed border-slate-200 bg-white p-6 text-center text-sm text-slate-400">
                    {{ __('market.no_batch_events_product') }}
                </div>
            @endif
        </div>

        {{-- Divider between batches --}}
        @if(!$loop->last)
            <div class="border-t border-slate-100 mb-8"></div>
        @endif

    @empty
        <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center">
            <div class="h-12 w-12 rounded-full flex items-center justify-center mx-auto mb-3"
                 style="background:#f1f5f9;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div class="font-semibold text-slate-700 mb-1">{{ __('market.no_traceability_data') }}</div>
            <div class="text-sm text-slate-400">{{ __('market.no_traceability_data_desc') }}</div>
        </div>
    @endforelse

    <div class="mt-10 pt-6 border-t border-slate-100 text-center text-xs text-slate-400">
        {{ __('market.powered_by') }}
    </div>
</div>

</body>
</html>
