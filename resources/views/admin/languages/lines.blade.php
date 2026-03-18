@extends('admin._layout')

@section('page_title', 'Translation Lines')
@section('page_subtitle', 'DB translations override PHP lang files. Changes take effect immediately.')

@section('page_actions')
    <a href="{{ route('admin.languages.lines.create', ['group' => $group]) }}"
       class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-white text-sm font-medium hover:bg-emerald-700 transition">
        + New Key
    </a>

    {{-- Export CSV --}}
    <a href="{{ route('admin.languages.lines.export', ['group' => $group]) }}"
       class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
        ↓ Export CSV
    </a>

    {{-- Import CSV --}}
    <button type="button" onclick="document.getElementById('import-panel').classList.toggle('hidden')"
            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
        ↑ Import CSV
    </button>

    <form method="POST" action="{{ route('admin.languages.lines.sync') }}" class="inline">
        @csrf
        <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition"
                onclick="return confirm('Import all keys from lang/*.php files into the DB? Existing values will be overwritten.')">
            ⚡ Sync from Files
        </button>
    </form>

    <form method="POST" action="{{ route('admin.languages.lines.sync-to-files') }}" class="inline">
        @csrf
        <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-sm font-medium transition"
                onclick="return confirm('Export all DB translations to lang/*.php files?\n\nThis activates all translations in the app.')">
            🌐 Export to Files (Activate)
        </button>
    </form>
    <a href="{{ route('admin.languages.index') }}" class="text-sm text-slate-500 hover:text-slate-700 transition">← Languages</a>
@endsection

@section('page_content')

{{-- ── Progress bars ───────────────────────────────────────────────────────── --}}
<div class="grid gap-3 mb-6" style="grid-template-columns: repeat({{ count($locales) }}, minmax(160px,1fr))">
    @foreach($locales as $code => $meta)
        @php $p = $progress[$code]; @endphp
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-slate-700">{{ $meta['flag'] }} {{ $meta['label'] }}</span>
                <span class="text-xs text-slate-500 font-mono">{{ $p['filled'] }}/{{ $p['total'] }}</span>
            </div>
            <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-2 rounded-full transition-all
                    {{ $p['pct'] === 100 ? 'bg-emerald-500' : ($p['pct'] >= 50 ? 'bg-amber-400' : 'bg-red-400') }}"
                     style="width: {{ $p['pct'] }}%"></div>
            </div>
            <div class="mt-1 text-right text-xs {{ $p['pct'] === 100 ? 'text-emerald-600' : 'text-slate-400' }}">
                {{ $p['pct'] }}%{{ $p['pct'] === 100 ? ' ✓' : '' }}
            </div>
        </div>
    @endforeach
</div>

{{-- ── Import panel (hidden by default) ───────────────────────────────────── --}}
<div id="import-panel" class="hidden mb-5">
    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
        <p class="text-sm font-semibold text-blue-800 mb-1">Import translations from CSV</p>
        <p class="text-xs text-blue-600 mb-3">
            Upload a CSV with columns: <code class="font-mono bg-blue-100 px-1 rounded">group, key, en, el</code>
            (header row required). Only non-empty cells will be written — existing translations are NOT overwritten unless the CSV cell has a value.
        </p>
        <form method="POST" action="{{ route('admin.languages.lines.import') }}"
              enctype="multipart/form-data" class="flex items-center gap-3 flex-wrap">
            @csrf
            <input type="hidden" name="group" value="{{ $group }}">
            <input type="file" name="csv_file" accept=".csv,.txt" required
                   class="text-sm text-slate-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-white file:text-slate-700 file:cursor-pointer hover:file:bg-slate-50">
            <button type="submit"
                    class="rounded-lg bg-blue-600 px-4 py-1.5 text-sm font-medium text-white hover:bg-blue-700 transition">
                Upload &amp; Import
            </button>
        </form>
        @error('csv_file')
            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

{{-- ── Filters ─────────────────────────────────────────────────────────────── --}}
<form method="GET" class="flex flex-wrap items-end gap-3 mb-5">
    <input type="hidden" name="group" value="{{ $group }}">

    <div>
        <label class="block text-xs text-slate-500 mb-1">Group</label>
        <select name="group" onchange="this.form.submit()"
                class="h-9 rounded-lg border border-slate-200 bg-white text-sm px-3 focus:outline-none focus:ring-2 focus:ring-emerald-200">
            @foreach($groups as $g)
                <option value="{{ $g }}" @selected($g === $group)>{{ $g }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-xs text-slate-500 mb-1">Search</label>
        <input name="search" value="{{ $search }}" placeholder="Key or translation text…"
               class="h-9 rounded-lg border border-slate-200 bg-white text-sm px-3 w-60 focus:outline-none focus:ring-2 focus:ring-emerald-200">
    </div>

    <div>
        <label class="block text-xs text-slate-500 mb-1">Show</label>
        <select name="filter" onchange="this.form.submit()"
                class="h-9 rounded-lg border border-slate-200 bg-white text-sm px-3 focus:outline-none focus:ring-2 focus:ring-emerald-200">
            <option value="" @selected(empty($filter))>All keys</option>
            <option value="missing" @selected($filter === 'missing')>⚠ Missing only</option>
        </select>
    </div>

    <button type="submit"
            class="h-9 rounded-lg bg-emerald-600 px-4 text-white text-sm font-medium hover:bg-emerald-700 transition">
        Search
    </button>

    @if($search || $filter)
        <a href="{{ route('admin.languages.lines', ['group' => $group]) }}"
           class="h-9 inline-flex items-center text-sm text-slate-500 hover:text-slate-700 transition">
            ✕ Clear
        </a>
    @endif
</form>

{{-- ── Translation table ───────────────────────────────────────────────────── --}}
{{-- We use a CSS-grid div layout (not <table>) so each row can be its own <form> --}}
@php $colCount = count($locales); @endphp

{{-- Header row --}}
<div class="hidden sm:grid rounded-t-xl border border-b-0 border-slate-200 bg-slate-50 overflow-hidden"
     style="grid-template-columns: 200px repeat({{ $colCount }}, 1fr) 80px">
    <div class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Key</div>
    @foreach($locales as $code => $meta)
        <div class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
            {{ $meta['flag'] }} {{ $meta['label'] }}
        </div>
    @endforeach
    <div></div>
</div>

{{-- Data rows --}}
@forelse($lines as $loop_index => $line)
    @php $hasMissing = $line->isMissingTranslation(); @endphp

    <form method="POST"
          action="{{ route('admin.languages.lines.update', $line) }}"
          class="{{ $hasMissing ? 'bg-amber-50/60' : 'bg-white' }} hover:bg-slate-50/40 transition
                 border border-slate-200 border-t-0
                 {{ $loop->last ? 'rounded-b-xl' : '' }}
                 sm:grid"
          style="grid-template-columns: 200px repeat({{ $colCount }}, 1fr) 80px">
        @csrf
        @method('PUT')

        {{-- Key column --}}
        <div class="px-4 py-3 sm:border-r border-slate-100 flex items-start gap-1.5">
            <span class="font-mono text-xs text-slate-600 break-all leading-5">{{ $line->group }}.{{ $line->key }}</span>
            @if($hasMissing)
                <span class="flex-shrink-0 text-amber-500 text-sm leading-5" title="One or more translations missing">⚠</span>
            @endif
        </div>

        {{-- One textarea per locale --}}
        @foreach($locales as $code => $meta)
            @php
                $val      = $line->getTranslation($code);
                $isEmpty  = empty($val);
            @endphp
            <div class="px-3 py-2 sm:border-r border-slate-100">
                <label class="block text-xs text-slate-400 mb-1 sm:hidden">{{ $meta['flag'] }} {{ $meta['label'] }}</label>
                <textarea name="{{ $code }}"
                          rows="2"
                          placeholder="{{ $isEmpty ? 'Not translated yet…' : '' }}"
                          class="w-full text-sm rounded-lg px-2.5 py-1.5 resize-y transition
                                 focus:outline-none focus:ring-2
                                 {{ $isEmpty
                                     ? 'border border-amber-300 bg-amber-50 focus:ring-amber-200 placeholder-amber-400'
                                     : 'border border-slate-200 bg-white focus:ring-emerald-200' }}">{{ old($code.'_'.$line->id, $val) }}</textarea>
            </div>
        @endforeach

        {{-- Save button --}}
        <div class="px-3 py-3 flex items-start justify-end sm:justify-center pt-3">
            <button type="submit"
                    class="w-full sm:w-auto h-8 px-3 rounded-lg bg-emerald-600 text-white text-xs font-medium hover:bg-emerald-700 transition shadow-sm">
                Save
            </button>
        </div>
    </form>
@empty
    <div class="rounded-b-xl border border-slate-200 border-t-0 bg-white px-6 py-14 text-center">
        <div class="text-3xl mb-3">🌐</div>
        <p class="text-slate-500 text-sm">
            @if($search || $filter)
                No keys match your filters.
                <a href="{{ route('admin.languages.lines', ['group' => $group]) }}" class="underline text-emerald-600">Clear filters</a>
            @else
                No translation keys in group <strong class="font-mono">{{ $group }}</strong> yet.<br>
                <a href="{{ route('admin.languages.lines.sync') }}"
                   class="underline text-emerald-600">Sync from PHP files</a>
                or
                <a href="{{ route('admin.languages.lines.create', ['group' => $group]) }}"
                   class="underline text-emerald-600">add a key manually</a>.
            @endif
        </p>
    </div>
@endforelse

{{-- Pagination --}}
@if($lines->hasPages())
    <div class="mt-5">{{ $lines->links() }}</div>
@endif

<p class="mt-2 text-xs text-slate-400">
    Showing {{ $lines->firstItem() ?? 0 }}–{{ $lines->lastItem() ?? 0 }} of {{ $lines->total() }} key(s)
    @if($filter === 'missing') &mdash; missing translations only @endif
</p>

@endsection
