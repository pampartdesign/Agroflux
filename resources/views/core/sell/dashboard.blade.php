@extends('layouts.app')

@section('content')

{{-- ── Page header ─────────────────────────────────────────────────────────── --}}
<div class="flex items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Sell on Marketplace</h1>
        <p class="text-sm text-slate-500 mt-1">Track your listings, orders and revenue performance.</p>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        <a href="{{ route('public.marketplace') }}" target="_blank"
           class="inline-flex items-center gap-2 h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm text-slate-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6M15 3h6v6M10 14L21 3"/>
            </svg>
            View Marketplace
        </a>
        <a href="{{ route('core.listings.create') }}"
           class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
            + New Listing
        </a>
    </div>
</div>

{{-- ── KPI Strip ────────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">Active Listings</div>
        <div class="text-3xl font-bold text-slate-900">{{ $activeListings }}</div>
        <div class="text-xs text-slate-400 mt-1">of {{ $totalListings }} total</div>
    </div>

    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">Total Orders</div>
        <div class="text-3xl font-bold {{ $totalOrders > 0 ? 'text-emerald-600' : 'text-slate-900' }}">{{ $totalOrders }}</div>
        <div class="text-xs text-slate-400 mt-1">{{ $thisMonthOrders }} this month</div>
    </div>

    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">Total Revenue</div>
        <div class="text-3xl font-bold text-slate-900">€{{ number_format($totalRevenue, 0) }}</div>
        <div class="text-xs text-slate-400 mt-1">avg €{{ number_format($avgOrderValue, 2) }} / order</div>
    </div>

    <div class="rounded-2xl border border-emerald-100 bg-emerald-50 shadow-sm p-5">
        <div class="text-xs font-semibold uppercase tracking-wide text-emerald-600 mb-1">This Month</div>
        <div class="text-3xl font-bold text-emerald-700">€{{ number_format($thisMonthRevenue, 0) }}</div>
        <div class="text-xs text-emerald-600 mt-1">{{ $thisMonthOrders }} order{{ $thisMonthOrders !== 1 ? 's' : '' }}</div>
    </div>

</div>

{{-- ── Chart + Recent Orders ─────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- Monthly Revenue Chart --}}
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
            <div class="h-8 w-8 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
            </div>
            <div>
                <div class="font-semibold text-sm text-slate-900">Revenue — Last 6 Months</div>
                <div class="text-xs text-slate-400">Monthly order totals (€)</div>
            </div>
        </div>
        <div class="p-6 flex-1 flex flex-col justify-center">
            <div style="position:relative; height:220px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-xl bg-sky-50 border border-sky-100 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <div class="font-semibold text-sm text-slate-900">Recent Orders</div>
            </div>
            <a href="{{ route('core.orders.index') }}" class="text-xs text-emerald-600 hover:underline">View all →</a>
        </div>

        @if($recentOrders->isEmpty())
            <div class="p-10 text-center">
                <div class="text-3xl mb-3">📦</div>
                <p class="text-slate-500 text-sm">No orders yet.</p>
                <p class="text-slate-400 text-xs mt-1">Activate listings on the marketplace to start receiving orders.</p>
            </div>
        @else
            <div class="divide-y divide-slate-50">
                @foreach($recentOrders as $order)
                    @php
                        $statusColors = [
                            'new'          => 'background:#fef3c7; color:#92400e;',
                            'pending'      => 'background:#fef3c7; color:#92400e;',
                            'pending_wire' => 'background:#e0f2fe; color:#075985;',
                            'confirmed'    => 'background:#d1fae5; color:#065f46;',
                            'delivered'    => 'background:#d1fae5; color:#065f46;',
                            'completed'    => 'background:#d1fae5; color:#065f46;',
                            'cancelled'    => 'background:#fee2e2; color:#991b1b;',
                        ];
                        $statusStyle = $statusColors[$order->status] ?? 'background:#f1f5f9; color:#64748b;';
                        $statusLabel = str_replace('_', ' ', ucfirst($order->status));
                    @endphp
                    <a href="{{ route('core.orders.show', $order) }}"
                       class="flex items-center justify-between px-5 py-3 hover:bg-slate-50/50 transition">
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-slate-800">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} · {{ $order->customer_name }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">{{ $order->items->count() }} item{{ $order->items->count() !== 1 ? 's' : '' }} · {{ $order->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="flex items-center gap-3 ml-3 flex-shrink-0">
                            <span class="text-sm font-semibold text-slate-900">€{{ number_format($order->total, 2) }}</span>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full" style="{{ $statusStyle }}">{{ $statusLabel }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

</div>

{{-- ── Top Products + Quick Actions ───────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Top Listings by Revenue --}}
    <div class="lg:col-span-2 rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
            <div class="h-8 w-8 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M20 7l-8-4-8 4 8 4 8-4z"/><path d="M4 7v10l8 4 8-4V7"/><path d="M12 11v10"/>
                </svg>
            </div>
            <div class="font-semibold text-sm text-slate-900">Top Listings by Revenue</div>
        </div>

        @if($topListings->isEmpty())
            <div class="p-10 text-center text-slate-400 text-sm">
                No sales data yet — activate listings to start selling.
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/60">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Product</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Orders</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Qty Sold</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($topListings as $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-3 font-medium text-slate-900">
                                {{ $item->listing?->product?->default_name ?? '—' }}
                                <div class="text-xs text-slate-400 mt-0.5">
                                    {{ $item->listing?->type === 'instock' ? 'In stock' : 'Pre-order' }}
                                    · €{{ number_format($item->listing?->price ?? 0, 2) }}/{{ $item->listing?->product?->unit ?? 'unit' }}
                                </div>
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ $item->order_count }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ number_format($item->total_qty, 1) }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-emerald-700">€{{ number_format($item->total_revenue, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Quick Actions --}}
    <div class="space-y-4">
        <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
            <div class="font-semibold text-sm text-slate-900 mb-4">Quick Actions</div>
            <div class="space-y-2">
                <a href="{{ route('core.listings.create') }}"
                   class="flex items-center gap-3 w-full h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Listing
                </a>
                <a href="{{ route('core.listings.index') }}"
                   class="flex items-center gap-3 w-full h-10 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm hover:bg-slate-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Manage Listings
                </a>
                <a href="{{ route('core.orders.index') }}"
                   class="flex items-center gap-3 w-full h-10 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm hover:bg-slate-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    Orders Inbox
                </a>
                <a href="{{ route('core.products.index') }}"
                   class="flex items-center gap-3 w-full h-10 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm hover:bg-slate-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    Products & Catalog
                </a>
                <a href="{{ route('public.marketplace') }}" target="_blank"
                   class="flex items-center gap-3 w-full h-10 px-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 text-sm hover:bg-emerald-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6M15 3h6v6M10 14L21 3"/>
                    </svg>
                    Open Marketplace ↗
                </a>
            </div>
        </div>

        {{-- Listing health --}}
        <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5">
            <div class="font-semibold text-sm text-slate-900 mb-3">Listing Health</div>
            <div class="space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Active</span>
                    <span class="font-semibold text-emerald-700">{{ $activeListings }}</span>
                </div>
                <div class="w-full h-2 rounded-full overflow-hidden" style="background:#e2e8f0;">
                    @php $pct = $totalListings > 0 ? round($activeListings / $totalListings * 100) : 0; @endphp
                    <div class="h-full rounded-full" style="width:{{ $pct }}%; background:#10b981;"></div>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Inactive</span>
                    <span class="font-semibold text-slate-500">{{ $totalListings - $activeListings }}</span>
                </div>
                @if($activeListings === 0 && $totalListings === 0)
                    <p class="text-xs text-slate-400">No listings yet. <a href="{{ route('core.listings.create') }}" class="text-emerald-600 hover:underline">Create your first →</a></p>
                @elseif($activeListings === 0)
                    <p class="text-xs text-amber-600">All listings are inactive. Activate them to appear on the marketplace.</p>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- ── Chart.js ─────────────────────────────────────────────────────────────── --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;

    const months = @json($chartMonths);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: months.map(m => m.label),
            datasets: [
                {
                    label: 'Revenue (€)',
                    data: months.map(m => m.revenue),
                    backgroundColor: '#10b981',
                    borderRadius: 6,
                    yAxisID: 'y',
                },
                {
                    label: 'Orders',
                    data: months.map(m => m.orders),
                    type: 'line',
                    borderColor: '#0ea5e9',
                    backgroundColor: '#0ea5e920',
                    tension: 0.3,
                    fill: false,
                    pointRadius: 4,
                    yAxisID: 'y2',
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top', labels: { usePointStyle: true, padding: 14, font: { size: 11 } } },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.dataset.label === 'Revenue (€)'
                            ? ` €${ctx.parsed.y.toFixed(2)}`
                            : ` ${ctx.parsed.y} orders`,
                    }
                }
            },
            scales: {
                x: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
                y: {
                    position: 'left',
                    title: { display: true, text: 'Revenue (€)', font: { size: 11 } },
                    ticks: { callback: v => '€' + v, font: { size: 11 } },
                    grid: { color: '#f1f5f9' },
                },
                y2: {
                    position: 'right',
                    title: { display: true, text: 'Orders', font: { size: 11 } },
                    ticks: { stepSize: 1, font: { size: 11 } },
                    grid: { display: false },
                },
            },
        },
    });
})();
</script>

@endsection
