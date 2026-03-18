@extends('admin._layout')

@section('page_title', 'Add Language')

@section('page_actions')
    <a href="{{ route('admin.languages.index') }}" class="text-sm text-slate-500 hover:text-slate-700 transition">← Back</a>
@endsection

@section('page_content')

{{-- 2-step reminder --}}
<div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 max-w-xl">
    <p class="font-semibold mb-1">Two steps to activate a language:</p>
    <ol class="list-decimal list-inside space-y-1 text-amber-700">
        <li>Add it here (creates the DB record).</li>
        <li>Add an entry to the <code class="bg-amber-100 px-1 rounded font-mono">locales</code> array in
            <code class="bg-amber-100 px-1 rounded font-mono">config/agroflux.php</code>
            — e.g. <code class="bg-amber-100 px-1 rounded font-mono">'fr' => ['label' => 'Français', 'flag' => '🇫🇷']</code>
        </li>
    </ol>
</div>

<form method="POST" action="{{ route('admin.languages.store') }}"
      class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4 max-w-xl">
    @csrf

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
            Language code <span class="text-red-500">*</span>
        </label>
        <input name="code" value="{{ old('code') }}"
               class="w-full h-10 px-3 rounded-xl border @error('code') border-red-400 @else border-slate-200 @enderror bg-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-emerald-200"
               placeholder="e.g. fr, de, tr" required>
        <p class="mt-1 text-xs text-slate-400">ISO 639-1 code. Must match what you put in config/agroflux.php.</p>
        @error('code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Name <span class="text-red-500">*</span></label>
        <input name="name" value="{{ old('name') }}"
               class="w-full h-10 px-3 rounded-xl border @error('name') border-red-400 @else border-slate-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
               placeholder="e.g. Français" required>
        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center gap-6 pt-1">
        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1"
                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-200"
                   @checked(old('is_active', true))>
            <span class="text-sm text-slate-700">Active</span>
        </label>

        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_default" value="1"
                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-200"
                   @checked(old('is_default'))>
            <span class="text-sm text-slate-700">Set as default language</span>
        </label>
    </div>

    <div class="pt-2 border-t border-slate-100 flex gap-3">
        <button class="inline-flex items-center gap-2 h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
            Add Language
        </button>
        <a href="{{ route('admin.languages.index') }}"
           class="inline-flex items-center h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm text-slate-600">
            Cancel
        </a>
    </div>
</form>

@endsection
