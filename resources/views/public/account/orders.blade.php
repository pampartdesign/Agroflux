<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('market.orders_title') }} — AgroFlux</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">

<header class="sticky top-0 z-30 bg-white border-b border-emerald-100 shadow-sm">
    <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between gap-4">
        <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">A</div>
            <div>
                <div class="font-semibold leading-tight text-slate-900">AgroFlux</div>
                <div class="text-xs text-slate-500">{{ __('market.marketplace') }}</div>
            </div>
        </a>
        <div class="flex items-center gap-2">
            @include('public._locale_switcher')
            @include('public._customer_nav')
        </div>
    </div>
</header>

<div class="max-w-4xl mx-auto px-6 py-8">

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('customer.dashboard') }}" class="text-slate-400 hover:text-slate-600 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('market.orders_title') }}</h1>
    </div>

    @if($orders->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center">
            <div class="h-14 w-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div class="font-medium text-slate-700 mb-1">{{ __('market.order_history_empty') }}</div>
            <a href="{{ route('public.marketplace') }}"
               class="inline-flex items-center h-10 px-6 mt-4 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition text-sm font-medium">
                {{ __('market.browse_marketplace') }}
            </a>
        </div>
    @else
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="divide-y divide-slate-100">
                @foreach($orders as $order)
                    <a href="{{ route('customer.order', $order) }}"
                       class="flex items-center justify-between px-6 py-4 hover:bg-slate-50 transition">
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-slate-900 text-sm">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">
                                {{ $order->created_at->format('d M Y, H:i') }}
                                @if($order->tenant)
                                    · {{ $order->tenant->name }}
                                @endif
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 ml-4">
                            <div class="font-semibold text-slate-900">€{{ number_format($order->total, 2) }}</div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $order->status === 'pending_wire' ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100' }}">
                                {{ $order->status === 'pending_wire' ? __('market.pending_payment') : $order->status }}
                            </span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-300 ml-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @endif
</div>

<footer class="mt-16 border-t border-slate-200 py-6 text-center text-xs text-slate-400">
    {{ __('market.footer_tagline_short') }}
</footer>

</body>
</html>
