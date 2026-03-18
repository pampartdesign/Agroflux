{{-- resources/views/core/farms/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-3xl">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">{{ __('farm.create_title') }}</h1>
            <p class="text-sm text-slate-600 mt-1">{{ __('farm.create_subtitle') }}</p>
        </div>
        <a href="{{ route('core.farms.index') }}"
           class="inline-flex items-center h-10 px-4 rounded-xl border border-emerald-200 bg-white hover:bg-emerald-50 transition text-sm">
            {{ __('app.back') }}
        </a>
    </div>

    <form method="POST" action="{{ route('core.farms.store') }}"
          class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-6">
        @csrf

        @include('core.farms._form')

        <div class="mt-6 flex items-center gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-sm">
                <span class="text-sm font-medium">{{ __('farm.btn_create_farm') }}</span>
            </button>

            <a href="{{ route('core.farms.index') }}"
               class="inline-flex items-center h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-emerald-50 transition text-sm">
                {{ __('app.cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection
