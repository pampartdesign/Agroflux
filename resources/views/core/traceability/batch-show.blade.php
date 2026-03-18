<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Batch {{ $batch->code }} - AgroFlux</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="bg-gray-50 text-gray-900">
<div class="max-w-5xl mx-auto py-10 px-4">
  <div class="flex items-start justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold">Batch {{ $batch->code }}</h1>
      <p class="text-sm text-gray-600">Product: <span class="font-medium">{{ $batch->product->default_name }}</span> • Status: <span class="font-medium">{{ $batch->status }}</span></p>
      @if($qr)
        <p class="text-sm text-gray-600 mt-2">
          QR public link:
          <a class="underline" href="{{ route('public.trace', $qr->public_token) }}" target="_blank">{{ route('public.trace', $qr->public_token) }}</a>
        </p>
      @endif
    </div>
    <div class="flex items-center gap-3">
      <a href="{{ route('core.traceability.event.create', $batch) }}" class="rounded-lg bg-black text-white px-4 py-2 text-sm">Add Event</a>
      <a href="{{ route('core.traceability.index') }}" class="text-sm underline">Back</a>
    </div>
  </div>

  <div class="bg-white shadow rounded-xl overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="text-left p-3">Date</th>
          <th class="text-left p-3">Type</th>
          <th class="text-left p-3">Notes</th>
        </tr>
      </thead>
      <tbody>
      @forelse($batch->events as $e)
        <tr class="border-t">
          <td class="p-3">{{ $e->occurred_at->format('Y-m-d H:i') }}</td>
          <td class="p-3 font-medium">{{ $e->event_type }}</td>
          <td class="p-3">{{ $e->notes ?? '—' }}</td>
        </tr>
      @empty
        <tr><td class="p-6 text-gray-600" colspan="3">No events yet.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
</body></html>
