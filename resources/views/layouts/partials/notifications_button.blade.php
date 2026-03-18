@php
  $unread = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
@endphp

<a href="{{ route('notifications.index') }}" class="relative inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 hover:bg-gray-50">
  <span>Notifications</span>
  @if($unread > 0)
    <span class="absolute -top-2 -right-2 text-xs bg-rose-600 text-white rounded-full px-2 py-0.5">{{ $unread }}</span>
  @endif
</a>
