@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold">@yield('page_title')</h1>
      @hasSection('page_subtitle')
        <p class="text-sm text-gray-600 mt-1">@yield('page_subtitle')</p>
      @endif
    </div>
    <div class="flex items-center gap-2">
      @yield('page_actions')
    </div>
  </div>

  @if (session('status'))
    <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
      {{ session('status') }}
    </div>
  @endif
  @if (session('error'))
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
      {{ session('error') }}
    </div>
  @endif

  @yield('page_content')
</div>
@endsection
