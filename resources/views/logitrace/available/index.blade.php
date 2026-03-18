@extends('logitrace._layout')

@section('content')
  <div class="flex items-center justify-between mb-4">
    <div>
      <h2 class="text-xl font-semibold">Available Requests</h2>
      <p class="text-sm text-gray-600">Open requests published by farmers. Send offers (farmers may accept or self-deliver).</p>
    </div>
    <a href="{{ route('logi.trucker.profile.edit') }}" class="px-4 py-2 rounded-lg border bg-white">Trucker profile</a>
  </div>

  @if(!$profile)
    <div class="mb-4 rounded-lg border bg-yellow-50 text-yellow-800 px-4 py-3 text-sm">
      You don't have a trucker profile yet. Create one before sending offers.
      <a class="underline ml-2" href="{{ route('logi.trucker.profile.edit') }}">Create profile</a>
    </div>
  @endif

  <div class="rounded-xl border bg-white overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left px-4 py-3">#</th>
          <th class="text-left px-4 py-3">Pickup</th>
          <th class="text-left px-4 py-3">Requested date</th>
          <th class="text-right px-4 py-3">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($requests as $r)
          <tr class="border-t">
            <td class="px-4 py-3">{{ $r->id }}</td>
            <td class="px-4 py-3">{{ $r->pickup_address }}</td>
            <td class="px-4 py-3">{{ $r->requested_date?->format('Y-m-d') ?? '-' }}</td>
            <td class="px-4 py-3 text-right">
              <a class="underline" href="{{ route('logi.available.show', $r) }}">Open</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">No open requests.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $requests->links() }}
  </div>
@endsection
