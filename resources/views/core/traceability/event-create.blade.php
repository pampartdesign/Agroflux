<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add Event - AgroFlux</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="bg-gray-50 text-gray-900">
<div class="max-w-2xl mx-auto py-10 px-4">
  <div class="bg-white shadow rounded-xl p-6">
    <h1 class="text-2xl font-semibold mb-2">Add Traceability Event</h1>
    <p class="text-sm text-gray-600 mb-6">Batch: <span class="font-medium">{{ $batch->code }}</span></p>

    <form method="POST" action="{{ route('core.traceability.event.store', $batch) }}" class="space-y-4">
      @csrf
      <div>
        <label class="block text-sm font-medium mb-1">Event type</label>
        <input name="event_type" value="{{ old('event_type') }}" class="w-full rounded-lg border-gray-300" placeholder="e.g., planting, treatment, harvest, packaging">
        @error('event_type')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Occurred at</label>
        <input type="datetime-local" name="occurred_at" value="{{ old('occurred_at') }}" class="w-full rounded-lg border-gray-300">
        @error('occurred_at')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Notes</label>
        <textarea name="notes" class="w-full rounded-lg border-gray-300" rows="4">{{ old('notes') }}</textarea>
      </div>

      <div class="flex items-center gap-3">
        <button class="rounded-lg bg-black text-white px-4 py-2 text-sm">Save</button>
        <a href="{{ route('core.traceability.batch.show', $batch) }}" class="text-sm underline">Cancel</a>
      </div>
    </form>
  </div>
</div>
</body></html>
