<section>
  <header>
    <h2 class="text-lg font-medium text-slate-900">Language</h2>
    <p class="mt-1 text-sm text-slate-600">Set your default language for AgroFlux.</p>
  </header>

  <form method="POST" action="{{ route('profile.locale.update') }}" class="mt-6 space-y-4 max-w-xl">
    @csrf
    @method('PUT')

    <div>
      <label class="block text-sm font-medium text-slate-700">Default language</label>
      <select name="locale" class="mt-1 w-full rounded-xl border-emerald-200">
        @foreach(config('agroflux.locales') as $code => $label)
          <option value="{{ $code }}" @selected(old('locale', auth()->user()->locale ?? 'en')===$code)>{{ $label }}</option>
        @endforeach
      </select>
      @error('locale') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <button class="inline-flex items-center h-10 px-4 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">
      Save
    </button>

    @if(session('status') === 'Language preference saved.')
      <p class="text-sm text-emerald-700">Saved.</p>
    @endif
  </form>
</section>
