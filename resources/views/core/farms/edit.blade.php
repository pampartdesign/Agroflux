{{-- resources/views/core/farms/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-3xl">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">{{ __('farm.edit_title') }}</h1>
            <p class="text-sm text-slate-600 mt-1">{{ __('farm.edit_subtitle', ['name' => $farm->name]) }}</p>
        </div>
        <a href="{{ route('core.farms.show', $farm) }}"
           class="inline-flex items-center h-10 px-4 rounded-xl border border-emerald-200 bg-white hover:bg-emerald-50 transition text-sm">
            {{ __('farm.back_to_farm') }}
        </a>
    </div>

    <form method="POST" action="{{ route('core.farms.update', $farm) }}"
          class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-6">
        @csrf
        @method('PUT')

        @include('core.farms._form')

        <div class="mt-6 flex items-center gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-sm">
                <span class="text-sm font-medium">{{ __('app.save_changes') }}</span>
            </button>

            <a href="{{ route('core.farms.show', $farm) }}"
               class="inline-flex items-center h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-emerald-50 transition text-sm">
                {{ __('app.cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection
