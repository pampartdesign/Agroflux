@extends('admin._layout')

@section('page_title', 'Languages')
@section('page_subtitle', 'Manage platform languages. To activate a language in the UI, add it to config/agroflux.php locales.')

@section('page_actions')
    <a href="{{ route('admin.languages.lines') }}"
       class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
        Translation Lines
    </a>
    <a href="{{ route('admin.languages.create') }}"
       class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-white text-sm font-medium hover:bg-emerald-700 transition">
        + Add Language
    </a>
@endsection

@section('page_content')

{{-- Info callout about config --}}
<div class="mb-5 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
    <strong>How languages work:</strong>
    After adding a language here, add its code to the <code class="bg-blue-100 px-1 rounded font-mono">locales</code> array
    in <code class="bg-blue-100 px-1 rounded font-mono">config/agroflux.php</code> to make it appear in the header
    language selector and profile dropdown. Then use <strong>Translation Lines</strong> to fill in all the translations.
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Flag</th>
                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Code</th>
                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Name</th>
                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Default</th>
                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Active</th>
                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">In App UI</th>
                <th class="text-right px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($languages as $lang)
                @php
                    $inConfig = array_key_exists($lang->code, $configLocales);
                    $flag     = $configLocales[$lang->code]['flag'] ?? '🌐';
                @endphp
                <tr class="border-t border-slate-100 hover:bg-slate-50/50 transition">
                    <td class="px-4 py-3 text-xl">{{ $flag }}</td>
                    <td class="px-4 py-3 font-mono text-slate-700">{{ $lang->code }}</td>
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $lang->name }}</td>
                    <td class="px-4 py-3">
                        @if($lang->is_default)
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-800">DEFAULT</span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                            {{ $lang->is_active ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-500' }}">
                            {{ $lang->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @if($inConfig)
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-800">
                                ✓ Visible
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium bg-amber-100 text-amber-700"
                                  title="Add '{{ $lang->code }}' to config/agroflux.php locales to show it in the UI">
                                ⚠ Not in config
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.languages.lines', ['group' => 'app']) }}"
                           class="text-xs text-slate-500 hover:text-emerald-700 mr-3 transition">Translations</a>
                        <a href="{{ route('admin.languages.edit', $lang) }}"
                           class="text-xs text-slate-700 underline hover:text-emerald-700 transition">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-slate-400">No languages configured.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
