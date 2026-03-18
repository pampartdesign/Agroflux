@extends('admin._layout')

@section('page_title', 'Add Translation Key')
@section('page_subtitle', 'Manually add a new translation key and enter translations for all configured languages.')

@section('page_actions')
    <a href="{{ route('admin.languages.lines', ['group' => $currentGroup]) }}"
       class="text-sm text-slate-500 hover:text-slate-700 transition">← Back to Lines</a>
@endsection

@section('page_content')

<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.languages.lines.store') }}"
          class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf

        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <p class="text-xs text-slate-500">
                The <span class="font-mono bg-slate-100 px-1 rounded">group.key</span> pair
                is how you reference this string in code:
                <code class="bg-slate-100 px-1 rounded font-mono">__('group.key')</code>
            </p>
        </div>

        <div class="px-6 py-5 space-y-5">

            {{-- Group + Key --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">
                        Group <span class="text-red-500">*</span>
                        <span class="font-normal text-slate-400 ml-1">(e.g. app, profile, core)</span>
                    </label>
                    <input name="group"
                           value="{{ old('group', $currentGroup) }}"
                           list="group-suggestions"
                           required
                           class="w-full h-10 px-3 rounded-xl border @error('group') border-red-400 @else border-slate-200 @enderror bg-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-emerald-200"
                           placeholder="app">
                    <datalist id="group-suggestions">
                        @foreach($groups as $g)
                            <option value="{{ $g }}">
                        @endforeach
                    </datalist>
                    @error('group')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">
                        Key <span class="text-red-500">*</span>
                        <span class="font-normal text-slate-400 ml-1">(e.g. dashboard.title)</span>
                    </label>
                    <input name="key"
                           value="{{ old('key') }}"
                           required
                           class="w-full h-10 px-3 rounded-xl border @error('key') border-red-400 @else border-slate-200 @enderror bg-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-emerald-200"
                           placeholder="some.key">
                    @error('key')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- Translation inputs for each active locale --}}
            @foreach($locales as $code => $meta)
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">
                        {{ $meta['flag'] }} {{ $meta['label'] }}
                    </label>
                    <textarea name="{{ $code }}"
                              rows="2"
                              class="w-full rounded-xl border @error($code) border-red-400 @else border-slate-200 @enderror bg-white text-sm px-3 py-2 resize-y focus:outline-none focus:ring-2 focus:ring-emerald-200"
                              placeholder="Translation in {{ $meta['label'] }}…">{{ old($code) }}</textarea>
                    @error($code)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            @endforeach

        </div>

        <div class="px-6 py-4 border-t border-slate-100 flex items-center gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
                Save Translation Key
            </button>
            <a href="{{ route('admin.languages.lines', ['group' => $currentGroup]) }}"
               class="inline-flex items-center h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm text-slate-600">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection
