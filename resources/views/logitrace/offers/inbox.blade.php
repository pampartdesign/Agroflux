<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Offers Inbox - AgroFlux</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
<div class="max-w-6xl mx-auto py-10 px-4">
  <div class="flex items-start justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold">Offers Inbox</h1>
      <p class="text-sm text-gray-600">Offers received by your organization from trucking partners.</p>
    </div>
    <a href="{{ route('logi.dashboard') }}" class="text-sm underline">Back</a>
  </div>

  <form method="GET" class="flex flex-wrap gap-3 mb-4">
    <select name="status" class="rounded-lg border-gray-300">
      <option value="">All statuses</option>
      @foreach(['sent','accepted','rejected','cancelled'] as $s)
        <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
      @endforeach
    </select>
    <button class="rounded-lg border px-4 py-2 text-sm">Filter</button>
  </form>

  <div class="bg-white shadow rounded-xl overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="text-left p-3">From</th>
          <th class="text-left p-3">Vehicle</th>
          <th class="text-left p-3">Price</th>
          <th class="text-left p-3">Available</th>
          <th class="text-left p-3">Status</th>
        </tr>
      </thead>
      <tbody>
      @forelse($offers as $o)
        <tr class="border-t">
          <td class="p-3 font-medium">{{ $o->tenant?->name ?? ('Tenant #'.$o->tenant_id) }}</td>
          <td class="p-3">{{ str_replace('_',' ', $o->vehicle_type) }}</td>
          <td class="p-3">{{ $o->price !== null ? number_format((float)$o->price,2) : '—' }} {{ $o->currency }}</td>
          <td class="p-3">{{ $o->available_date?->format('Y-m-d') ?? '—' }}</td>
          <td class="p-3"><span class="rounded border px-2 py-1 text-xs">{{ $o->status }}</span></td>
        </tr>
      @empty
        <tr class="border-t"><td colspan="5" class="p-6 text-sm text-gray-600">No offers received.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $offers->links() }}</div>
</div>
</body>
</html>
