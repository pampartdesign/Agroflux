<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Trucker Profile - AgroFlux</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
<div class="max-w-3xl mx-auto py-10 px-4">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold">Trucker Profile</h1>
      <p class="text-sm text-gray-600">Vehicle types and contact info used for matching and offers.</p>
    </div>
    <a href="{{ route('logi.dashboard') }}" class="text-sm underline">Back</a>
  </div>

  @if(session('status'))
    <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-3 text-sm text-green-800">
      {{ session('status') }}
    </div>
  @endif

  <form method="POST" action="{{ route('logi.profile.update') }}" class="bg-white shadow rounded-xl p-6 space-y-4">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1">Company Name</label>
        <input name="company_name" value="{{ old('company_name', $profile->company_name) }}" class="w-full rounded-lg border-gray-300">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Contact Name</label>
        <input name="contact_name" value="{{ old('contact_name', $profile->contact_name) }}" class="w-full rounded-lg border-gray-300">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Phone</label>
        <input name="phone" value="{{ old('phone', $profile->phone) }}" class="w-full rounded-lg border-gray-300">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input name="email" value="{{ old('email', $profile->email) }}" class="w-full rounded-lg border-gray-300">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Region</label>
        <select name="region_id" class="w-full rounded-lg border-gray-300">
          <option value="">—</option>
          @foreach($regions as $r)
            <option value="{{ $r->id }}" @selected((int)old('region_id',$profile->region_id)===$r->id)>{{ $r->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">City</label>
        <input name="city" value="{{ old('city', $profile->city) }}" class="w-full rounded-lg border-gray-300">
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Address</label>
        <input name="address_line" value="{{ old('address_line', $profile->address_line) }}" class="w-full rounded-lg border-gray-300">
      </div>
    </div>

    <div class="border-t pt-4">
      <div class="font-semibold">Vehicle Types</div>
      <div class="text-sm text-gray-600 mt-1">Enable what you can handle.</div>

      <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-3">
        <label class="flex items-center gap-2 text-sm">
          <input type="checkbox" name="supports_van" value="1" @checked(old('supports_van',$profile->supports_van))>
          Van
        </label>
        <label class="flex items-center gap-2 text-sm">
          <input type="checkbox" name="supports_small_pickup" value="1" @checked(old('supports_small_pickup',$profile->supports_small_pickup))>
          Small Pickup
        </label>
        <label class="flex items-center gap-2 text-sm">
          <input type="checkbox" name="supports_refrigerated_truck" value="1" @checked(old('supports_refrigerated_truck',$profile->supports_refrigerated_truck))>
          Refrigerated Truck
        </label>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Notes</label>
      <textarea name="notes" class="w-full rounded-lg border-gray-300" rows="4">{{ old('notes', $profile->notes) }}</textarea>
    </div>

    <div class="flex items-center gap-3">
      <button class="rounded-lg bg-black text-white px-4 py-2 text-sm">Save</button>
      <a class="underline text-sm" href="{{ route('logi.dashboard') }}">Cancel</a>
    </div>
  </form>
</div>
</body>
</html>
