<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Truckers Directory - AgroFlux</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
<div class="max-w-6xl mx-auto py-10 px-4">
  <div class="flex items-start justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold">Truckers Directory</h1>
      <p class="text-sm text-gray-600">Filter by region and vehicle type. Use this to request offers.</p>
    </div>
    <a href="{{ route('logi.dashboard') }}" class="text-sm underline">Back</a>
  </div>

  <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
    <select name="region_id" class="rounded-lg border-gray-300">
      <option value="">All regions</option>
      @foreach($regions as $r)
        <option value="{{ $r->id }}" @selected((int)request('region_id')===$r->id)>{{ $r->name }}</option>
      @endforeach
    </select>
    <select name="vehicle" class="rounded-lg border-gray-300">
      <option value="">All vehicles</option>
      <option value="van" @selected(request('vehicle')==='van')>Van</option>
      <option value="small_pickup" @selected(request('vehicle')==='small_pickup')>Small Pickup</option>
      <option value="refrigerated_truck" @selected(request('vehicle')==='refrigerated_truck')>Refrigerated Truck</option>
    </select>
    <button class="rounded-lg bg-black text-white px-4 py-2 text-sm">Filter</button>
  </form>

  <div class="bg-white shadow rounded-xl overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="text-left p-3">Company</th>
          <th class="text-left p-3">Region/City</th>
          <th class="text-left p-3">Vehicles</th>
          <th class="text-left p-3">Contact</th>
          <th class="text-left p-3">Action</th>
        </tr>
      </thead>
      <tbody>
      @foreach($truckers as $t)
        <tr class="border-t">
          <td class="p-3 font-medium">{{ $t->company_name ?? ('Tenant #'.$t->tenant_id) }}</td>
          <td class="p-3">{{ $t->region?->name ?? '—' }}{{ $t->city ? ' • '.$t->city : '' }}</td>
          <td class="p-3 text-xs">
            @if($t->supports_van) <span class="rounded border px-2 py-1 mr-1">Van</span> @endif
            @if($t->supports_small_pickup) <span class="rounded border px-2 py-1 mr-1">Small Pickup</span> @endif
            @if($t->supports_refrigerated_truck) <span class="rounded border px-2 py-1 mr-1">Refrigerated</span> @endif
          </td>
          <td class="p-3 text-xs">
            {{ $t->contact_name ?? '—' }}<br>
            {{ $t->phone ?? '' }} {{ $t->email ? ' • '.$t->email : '' }}
          </td>
          <td class="p-3">
            <a class="underline" href="{{ route('logi.requests.create', ['target' => $t->tenant_id]) }}">Request Offer</a>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $truckers->links() }}</div>
</div>
</body>
</html>
