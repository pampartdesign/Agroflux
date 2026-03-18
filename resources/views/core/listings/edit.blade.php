<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Listing - AgroFlux</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="bg-gray-50 text-gray-900">
<div class="max-w-2xl mx-auto py-10 px-4">
  <div class="bg-white shadow rounded-xl p-6">
    <h1 class="text-2xl font-semibold mb-6">Edit Listing</h1>
    <form method="POST" action="{{ route('core.listings.update',$listing) }}" class="space-y-4">
      @csrf @method('PUT')
      <div>
        <label class="block text-sm font-medium mb-1">Product</label>
        <select name="product_id" class="w-full rounded-lg border-gray-300">
          @foreach($products as $p)
            <option value="{{ $p->id }}" @selected($listing->product_id===$p->id)>{{ $p->default_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Type</label>
          <select name="type" class="w-full rounded-lg border-gray-300">
            <option value="instock" @selected($listing->type==='instock')>instock</option>
            <option value="preorder" @selected($listing->type==='preorder')>preorder</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Price (€)</label>
          <input name="price" value="{{ old('price',$listing->price) }}" class="w-full rounded-lg border-gray-300">
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Available Qty (instock)</label>
          <input name="available_qty" value="{{ old('available_qty',$listing->available_qty) }}" class="w-full rounded-lg border-gray-300">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Expected Harvest (preorder)</label>
          <input type="date" name="expected_harvest_at" value="{{ old('expected_harvest_at', optional($listing->expected_harvest_at)->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300">
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Upfront percent (preorder, max 99.99)</label>
        <input name="upfront_percent" value="{{ old('upfront_percent',$listing->upfront_percent) }}" class="w-full rounded-lg border-gray-300">
      </div>

      <div class="flex items-center gap-3">
        <button class="rounded-lg bg-black text-white px-4 py-2 text-sm">Update</button>
        <a href="{{ route('core.listings.index') }}" class="text-sm underline">Cancel</a>
      </div>
    </form>
  </div>
</div>
</body></html>
