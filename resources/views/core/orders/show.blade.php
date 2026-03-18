<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Order #{{ $order->id }} - AgroFlux</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="bg-gray-50 text-gray-900">
<div class="max-w-4xl mx-auto py-10 px-4">
  <div class="flex items-start justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold">Order #{{ $order->id }}</h1>
      <p class="text-sm text-gray-600">{{ $order->customer_name }} • {{ $order->customer_email }}</p>
      <p class="text-sm text-gray-600">Status: <span class="font-medium">{{ $order->status }}</span></p>
    </div>
    <div class="flex items-center gap-3">
      <a href="{{ route('core.orders.print',$order) }}" class="rounded-lg bg-black text-white px-4 py-2 text-sm">Print</a>
      <a href="{{ route('core.orders.index') }}" class="text-sm underline">Back</a>
    </div>
  </div>

  <div class="bg-white shadow rounded-xl overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-100"><tr>
        <th class="text-left p-3">Product</th><th class="text-left p-3">Type</th><th class="text-left p-3">Qty</th><th class="text-left p-3">Price</th><th class="text-left p-3">Line</th>
      </tr></thead>
      <tbody>
      @foreach($order->items as $it)
        <tr class="border-t">
          <td class="p-3 font-medium">{{ $it->listing->product->default_name }}</td>
          <td class="p-3">{{ $it->listing->type }}</td>
          <td class="p-3">{{ $it->qty }}</td>
          <td class="p-3">€{{ number_format($it->price,2) }}</td>
          <td class="p-3">€{{ number_format($it->price * $it->qty,2) }}</td>
        </tr>
      @endforeach
      </tbody>
    </table>
    <div class="p-4 text-right font-semibold">Total: €{{ number_format($order->total,2) }}</div>
  </div>
</div>
</body></html>
