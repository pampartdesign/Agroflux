@extends('layouts.app')
@section('content')

{{-- Header --}}
<div class="flex items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('app.notifications') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('app.notifications_subtitle') }}</p>
    </div>
    @if($notifications->total() > 0)
    <form method="POST" action="{{ route('notifications.read_all') }}">
        @csrf
        <button type="submit"
                class="inline-flex items-center gap-2 h-10 px-4 rounded-xl border border-emerald-200 bg-white hover:bg-emerald-50 transition text-sm font-medium"
                style="color:#047857;">
            ✓ {{ __('app.mark_all_read') }}
        </button>
    </form>
    @endif
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    {{ session('success') }}
</div>
@endif

<div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">

    @forelse($notifications as $n)
    @php
        $d        = $n->data ?? [];
        $isUnread = is_null($n->read_at);
        $type     = $d['type'] ?? '';

        // Icon by type
        $icon = match(true) {
            str_starts_with($type, 'logi.')  => '🚛',
            str_starts_with($type, 'plus.')  => '📡',
            str_starts_with($type, 'core.')  => '🛒',
            $type === 'order_placed'         => '🛒',
            default                          => '🔔',
        };

        // Fallback title for old notifications that lack the field
        $title = $d['title'] ?? match($type) {
            'order_placed'       => 'New order placed',
            'core.order.placed'  => 'New order received',
            'logi.offer.accepted'=> 'Offer accepted',
            'logi.offer.received'=> 'New delivery offer',
            'plus.iot.alert'     => 'IoT alert triggered',
            default              => __('app.notification_default'),
        };

        // Fallback message for old notifications that lack the field
        $message = $d['message'] ?? match($type) {
            'order_placed'        => 'Order #' . ($d['order_id'] ?? '—') . ' was placed' . (!empty($d['total']) ? ' — total: ' . number_format((float)$d['total'], 2) . ' €' : '') . '.',
            'core.order.placed'   => 'A new order has been placed in your marketplace.',
            'logi.offer.accepted' => 'Your delivery offer was accepted.',
            'logi.offer.received' => 'A trucker sent a delivery offer for your request.',
            'plus.iot.alert'      => 'A sensor alert was triggered.',
            default               => null,
        };
    @endphp
    <div class="flex items-start gap-4 px-6 py-4 border-b border-slate-50 transition hover:bg-slate-50/60
                {{ $isUnread ? 'bg-emerald-50/30' : '' }}">

        {{-- Icon --}}
        <div class="flex-shrink-0 mt-0.5 h-9 w-9 rounded-2xl flex items-center justify-center text-lg"
             style="{{ $isUnread ? 'background:#f0fdf4;border:1px solid #d1fae5;' : 'background:#f8fafc;border:1px solid #e2e8f0;' }}">
            {{ $icon }}
        </div>

        {{-- Content --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-sm font-semibold text-slate-900">
                    {{ $title }}
                </span>
                @if($isUnread)
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                          style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;">{{ __('app.new') }}</span>
                @endif
            </div>
            @if($message)
            <div class="text-sm text-slate-600 mt-0.5">{{ $message }}</div>
            @endif
            <div class="text-xs text-slate-400 mt-1">{{ $n->created_at->diffForHumans() }}</div>
        </div>

        {{-- Action --}}
        <div class="flex-shrink-0 self-center">
            @if($isUnread)
            <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                @csrf
                <button type="submit"
                        class="text-xs h-8 px-3 rounded-lg border border-slate-200 hover:bg-emerald-50 hover:border-emerald-200 transition"
                        style="color:#047857;">
                    {{ __('app.mark_read') }}
                </button>
            </form>
            @else
            <span class="text-xs text-slate-300">{{ __('app.read') }}</span>
            @endif
        </div>
    </div>

    @empty
    <div class="py-16 text-center">
        <div class="text-4xl mb-3">🔔</div>
        <div class="text-sm font-semibold text-slate-700 mb-1">{{ __('app.all_caught_up_title') }}</div>
        <div class="text-xs text-slate-400">{{ __('app.no_notifications_desc') }}</div>
    </div>
    @endforelse

</div>

@if($notifications->hasPages())
<div class="mt-6">
    {{ $notifications->links() }}
</div>
@endif

@endsection
