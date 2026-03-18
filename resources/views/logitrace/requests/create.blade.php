@extends('logitrace._layout')

@section('content')
  <div class="rounded-xl border bg-white p-6 max-w-3xl">
    <div class="mb-4">
      <h2 class="text-xl font-semibold">Create Delivery Request</h2>
      <p class="text-sm text-gray-600">Create as draft first, then publish to receive offers. Farmers may also self-deliver.</p>
    </div>

    <form method="POST" action="{{ route('logi.requests.store') }}" class="space-y-4">
      @csrf

      <div>
        <label class="block text-sm font-medium mb-1">Farm</label>
        <select name="farm_id" class="w-full rounded-lg border-gray-300">
          @foreach($farms as $f)
            <option value="{{ $f->id }}" @selected((int)old('farm_id')===$f->id)>{{ $f->name }}</option>
          @endforeach
        </select>
        @error('farm_id')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Pickup address</label>
        <input name="pickup_address" value="{{ old('pickup_address') }}" class="w-full rounded-lg border-gray-300" />
        @error('pickup_address')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Delivery address (optional)</label>
        <input name="delivery_address" value="{{ old('delivery_address') }}" class="w-full rounded-lg border-gray-300" />
        @error('delivery_address')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Cargo description (optional)</label>
          <input name="cargo_description" value="{{ old('cargo_description') }}" class="w-full rounded-lg border-gray-300" />
          @error('cargo_description')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Weight (kg) (optional)</label>
          <input name="cargo_weight_kg" value="{{ old('cargo_weight_kg') }}" class="w-full rounded-lg border-gray-300" />
          @error('cargo_weight_kg')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Requested date (optional)</label>
        <input type="date" name="requested_date" value="{{ old('requested_date') }}" class="w-full rounded-lg border-gray-300" />
        @error('requested_date')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
      </div>

      <div class="flex items-center gap-2">
        <button class="px-4 py-2 rounded-lg bg-emerald-600 text-white">Create draft</button>
        <a href="{{ route('logi.requests.index') }}" class="px-4 py-2 rounded-lg border bg-white">Cancel</a>
      </div>
    </form>
  </div>
@endsection
