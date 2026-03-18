@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
  <div class="mb-6">
    <h1 class="text-2xl font-semibold">{{ __('app.activity_title') }}</h1>
    <p class="text-sm text-gray-600 mt-1">{{ __('app.activity_subtitle') }}</p>
  </div>

  <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
    @forelse($logs as $log)
      <div class="p-4 border-b border-gray-100">
        <div class="flex items-center justify-between gap-4">
          <div class="font-medium">{{ $log->action }}</div>
          <div class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</div>
        </div>
        <div class="text-sm text-gray-700 mt-1">
          @if($log->entity_type && $log->entity_id)
            <span class="text-gray-500">{{ $log->entity_type }} #{{ $log->entity_id }}</span>
          @endif
        </div>
        @if(!empty($log->meta))
          <pre class="mt-2 text-xs bg-gray-50 border border-gray-100 rounded-lg p-3 overflow-auto">{{ json_encode($log->meta, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
        @endif
      </div>
    @empty
      <div class="p-6 text-sm text-gray-600">{{ __('app.no_activity_yet') }}</div>
    @endforelse
  </div>

  <div class="mt-6">
    {{ $logs->links() }}
  </div>
</div>
@endsection
