<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Send Offer - AgroFlux</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
<div class="max-w-3xl mx-auto py-10 px-4">
  <div class="flex items-start justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold">Send Offer</h1>
      <p class="text-sm text-gray-600">Reply to a farmer request with a price and message.</p>
    </div>
    <a href="{{ route('logi.dashboard') }}" class="text-sm underline">Back</a>
  </div>

  <div class="bg-white shadow rounded-xl p-6 mb-6">
    <div class="font-semibold">{{ $request->title }}</div>
    <div class="text-sm text-gray-600 mt-1">
      Vehicle: {{ str_replace('_',' ', $request->vehicle_type) }}
      <span class="mx-2">•</span>
      Pickup: {{ $request->pickup_address }}
      <span class="mx-2">→</span>
      Dropoff: {{ $request->dropoff_address }}
    </div>
  </div>

  @if ($errors->any())
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-800">
      <div class="font-semibold mb-1">Please fix the errors:</div>
      <ul class="list-disc ml-5">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('logi.requests.offer.store', $request) }}" class="bg-white shadow rounded-xl p-6 space-y-4">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1">Price (EUR)</label>
        <input type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" class="w-full rounded-lg border-gray-300">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Available Date (optional)</label>
        <input type="date" name="available_date" value="{{ old('available_date') }}" class="w-full rounded-lg border-gray-300">
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Message (optional)</label>
      <textarea name="message" class="w-full rounded-lg border-gray-300" rows="4">{{ old('message') }}</textarea>
    </div>

    <div class="flex items-center gap-3">
      <button class="rounded-lg bg-black text-white px-4 py-2 text-sm">Send Offer</button>
      <a class="underline text-sm" href="{{ route('logi.dashboard') }}">Cancel</a>
    </div>
  </form>
</div>
</body>
</html>
