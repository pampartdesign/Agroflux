@extends('logitrace._layout')

@section('content')
  <div class="flex items-center justify-between mb-4">
    <div>
      <h2 class="text-xl font-semibold">My Offers</h2>
      <p class="text-sm text-gray-600">means: offers you sent as a trucker.</p>
    </div>
    <a href="{{ route('logi.available.index') }}" class="px-4 py-2 rounded-lg border bg-white">Browse Requests</a>
  </div>

  <div class="rounded-xl border bg-white overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left px-4 py-3">Request</th>
          <th class="text-left px-4 py-3">Price</th>
          <th class="text-left px-4 py-3">Status</th>
          <th class="text-left px-4 py-3">Sent</th>
        </tr>
      </thead>
      <tbody>
        @forelse($offers as $o)
          <tr class="border-t">
            <td class="px-4 py-3">
              <a class="underline" href="{{ route('logi.available.show', $o->request) }}">#{{ $o->delivery_request_id }}</a>
            </td>
            <td class="px-4 py-3">€{{ number_format((float)$o->price, 2) }}</td>
            <td class="px-4 py-3">
              <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-700 text-xs">{{ strtoupper($o->status) }}</span>
            </td>
            <td class="px-4 py-3">{{ $o->created_at?->format('Y-m-d H:i') }}</td>
          </tr>
        @empty
          <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">No offers yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $offers->links() }}
  </div>
@endsection
