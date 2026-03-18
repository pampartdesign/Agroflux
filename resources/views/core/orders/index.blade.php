@extends('layouts.app')

@section('content')
<div class="flex items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-semibold">Orders</h1>
        <p class="text-sm text-slate-600 mt-1">Track marketplace orders and fulfillment status.</p>
    </div>

    <form method="GET" action="{{ route('core.orders.index') }}" class="flex items-center gap-2">
        <select name="status" class="h-10 rounded-xl border-emerald-200">
            <option value="">All statuses</option>
            @foreach(($statuses ?? []) as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button class="h-10 px-4 rounded-xl border border-emerald-200 bg-white hover:bg-emerald-50 transition" type="submit">Filter</button>
    </form>
</div>

<div class="rounded-2xl bg-white border border-emerald-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-emerald-100 flex items-center justify-between">
        <div class="font-semibold text-sm">Recent Orders</div>
        <div class="text-xs text-slate-500">Organization: <span class="font-medium text-slate-700">{{ $tenant->name ?? '—' }}</span></div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
            <tr>
                <th class="text-left font-medium px-5 py-3">Order</th>
                <th class="text-left font-medium px-5 py-3">Customer</th>
                <th class="text-left font-medium px-5 py-3">Total</th>
                <th class="text-left font-medium px-5 py-3">Status</th>
                <th class="text-right font-medium px-5 py-3">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-emerald-50">
            @forelse($orders as $order)
                <tr class="hover:bg-emerald-50/40">
                    <td class="px-5 py-3 font-medium">#{{ $order->order_number ?? $order->id }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $order->customer_name ?? '-' }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $order->total ? number_format($order->total,2) : '-' }}</td>
                    <td class="px-5 py-3">
                        @php($st = $order->status ?? 'new')
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs border
                            {{ in_array($st,['new','pending']) ? 'bg-amber-50 border-amber-200 text-amber-800' : '' }}
                            {{ in_array($st,['confirmed']) ? 'bg-blue-50 border-blue-200 text-blue-800' : '' }}
                            {{ in_array($st,['delivered','completed']) ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : '' }}
                            {{ in_array($st,['cancelled','canceled']) ? 'bg-red-50 border-red-200 text-red-800' : '' }}
                        ">
                            {{ ucfirst($st) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <a class="text-emerald-700 hover:underline" href="{{ route('core.orders.show', $order) }}">Open</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center text-slate-500">
                        No orders yet.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
