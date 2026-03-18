<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('market.order_reference') }} #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} — AgroFlux</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">

<header class="sticky top-0 z-30 bg-white border-b border-emerald-100 shadow-sm">
    <div class="max-w-3xl mx-auto px-6 py-4 flex items-center justify-between gap-4">
        <a href="{{ route('customer.orders') }}" class="flex items-center gap-3">
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

<div class="max-w-3xl mx-auto px-6 py-8">

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('customer.orders') }}" class="text-slate-400 hover:text-slate-600 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-slate-900">{{ __('market.order_reference') }} #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h1>
            <div class="text-sm text-slate-500 mt-0.5">{{ $order->created_at->format('d M Y, H:i') }}</div>
        </div>
    </div>

    {{-- Order items --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden mb-5">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="font-semibold text-slate-900 text-sm">{{ __('market.order_summary') }}</div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                {{ $order->status === 'pending_wire' ? __('market.pending_payment') : $order->status }}
            </span>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach($order->items as $item)
                <div class="px-6 py-4 flex justify-between text-sm">
                    <span class="text-slate-700">
                        {{ $item->listing?->product?->default_name ?? 'Item' }}
                        <span class="text-slate-400">×{{ $item->qty }}</span>
                    </span>
                    <span class="font-medium text-slate-900">€{{ number_format((float)$item->price * (float)$item->qty, 2) }}</span>
                </div>
            @endforeach
            <div class="px-6 py-4 flex justify-between font-semibold">
                <span>{{ __('market.total') }}</span>
                <span class="text-emerald-700">€{{ number_format($order->total, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Delivery details --}}
    @if($order->delivery_address)
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 mb-5">
        <div class="font-semibold text-slate-900 text-sm mb-3">{{ __('market.delivery_address') }}</div>
        <div class="text-sm text-slate-600 space-y-0.5">
            <div>{{ $order->customer_name }} {{ $order->customer_surname }}</div>
            @if($order->customer_phone) <div>{{ $order->customer_phone }}</div> @endif
            <div>{{ $order->delivery_address }}</div>
            <div>{{ $order->delivery_city }}, {{ $order->delivery_zip }}</div>
            <div>{{ $order->delivery_country }}</div>
        </div>
    </div>
    @endif

    {{-- Document type --}}
    @if($order->document_type === 'invoice' && $order->vat_number)
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 mb-5">
        <div class="font-semibold text-slate-900 text-sm mb-3">{{ __('market.invoice') }}</div>
        <div class="text-sm text-slate-600 space-y-0.5">
            @if($order->company_name) <div class="font-medium">{{ $order->company_name }}</div> @endif
            <div>{{ __('market.vat_number') }}: <span class="font-mono">{{ $order->vat_country }}-{{ $order->vat_number }}</span></div>
        </div>
    </div>
    @endif

    {{-- Print --}}
    <div class="flex justify-center">
        <a href="{{ route('order.print', $order) }}" target="_blank"
           class="inline-flex items-center h-10 px-5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm">
            {{ __('market.print_order') }}
        </a>
    </div>
</div>

<footer class="mt-16 border-t border-slate-200 py-6 text-center text-xs text-slate-400">
    {{ __('market.footer_tagline_short') }}
</footer>

</body>
</html>
