@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-semibold">Media Library</h1>
    <p class="text-sm text-slate-600 mt-1">Upload images once, reuse them across products and listings. Global assets are curated by Super Admin.</p>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
  <div class="lg:col-span-1">
    <div class="rounded-2xl border border-emerald-100 bg-white p-5">
      <form method="GET" action="{{ route('media.index') }}" class="space-y-3">
        <div>
          <label class="text-sm font-medium">Search</label>
          <input name="q" value="{{ request('q') }}" class="mt-1 w-full rounded-xl border-emerald-200" placeholder="filename...">
        </div>
        <button class="w-full inline-flex justify-center items-center h-10 px-4 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">Filter</button>
      </form>
    </div>

    <div class="mt-6 rounded-2xl border border-emerald-100 bg-white p-5">
      <div class="text-sm font-semibold mb-3">Upload</div>

      <form method="POST" action="{{ route('media.upload') }}" enctype="multipart/form-data" class="space-y-3">
        @csrf
        <input type="file" name="file" class="w-full text-sm">
        @error('file') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

        <input name="alt_text" value="{{ old('alt_text') }}" class="w-full rounded-xl border-emerald-200" placeholder="alt text (optional)">
        @error('alt_text') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

        <div class="flex items-center gap-2 text-sm">
          <input id="scope_tenant" type="radio" name="scope" value="tenant" class="rounded border-emerald-300" checked>
          <label for="scope_tenant">Tenant library</label>
        </div>

        @if(auth()->user()?->is_super_admin)
          <div class="flex items-center gap-2 text-sm">
            <input id="scope_global" type="radio" name="scope" value="global" class="rounded border-emerald-300">
            <label for="scope_global">Global library (Super Admin)</label>
          </div>
        @endif

        <button class="w-full inline-flex justify-center items-center h-10 px-4 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">
          Upload
        </button>
      </form>
    </div>
  </div>

  <div class="lg:col-span-3">
    <div class="rounded-2xl border border-emerald-100 bg-white p-5">
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($assets as $a)
          <div class="group rounded-xl border border-emerald-100 overflow-hidden bg-emerald-50/30">
            <div class="aspect-square bg-white overflow-hidden">
              <img src="{{ $a->url() }}" alt="{{ $a->alt_text ?? $a->filename }}" class="w-full h-full object-cover">
            </div>
            <div class="p-2">
              <div class="text-xs font-medium truncate">{{ $a->filename }}</div>
              <div class="text-[11px] text-slate-500 mt-0.5">
                {{ $a->tenant_id ? 'Tenant' : 'Global' }}
              </div>
            </div>
          </div>
        @empty
          <div class="text-slate-600">No media yet.</div>
        @endforelse
      </div>

      <div class="mt-6">
        {{ $assets->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
