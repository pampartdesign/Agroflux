<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Manual Sensor Entry - AgroFlux</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="bg-gray-50 text-gray-900">
<div class="max-w-3xl mx-auto py-10 px-4">
  <div class="bg-white shadow rounded-xl p-6">
    <h1 class="text-2xl font-semibold mb-2">Manual Data Entry</h1>
    <p class="text-sm text-gray-600 mb-6">Used for Core (manual only) and as fallback for Plus.</p>

    <form method="POST" action="{{ route('plus.iot.manual.store') }}" class="space-y-4">
      @csrf
      <div>
        <label class="block text-sm font-medium mb-1">Sensor</label>
        <select name="sensor_id" class="w-full rounded-lg border-gray-300">
          @foreach($sensors as $s)
            <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->group_key }})</option>
          @endforeach
        </select>
        @error('sensor_id')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Value</label>
        <input name="value" class="w-full rounded-lg border-gray-300">
        @error('value')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Recorded at</label>
        <input type="datetime-local" name="recorded_at" class="w-full rounded-lg border-gray-300">
        @error('recorded_at')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
      </div>
      <button class="rounded-lg bg-black text-white px-4 py-2 text-sm">Save</button>
    </form>

    <div class="mt-6 text-sm">
      <a class="underline" href="{{ route('plus.iot.dashboard') }}">Back</a>
    </div>
  </div>
</div>
</body></html>
