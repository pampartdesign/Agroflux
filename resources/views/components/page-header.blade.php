@props(['title' => '', 'subtitle' => null, 'actions' => null])

<div class="mb-6">
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-semibold tracking-tight">{{ $title }}</h1>
      @if($subtitle)
        <p class="mt-1 text-sm text-slate-600">{{ $subtitle }}</p>
      @endif
    </div>
    @if($actions)
      <div class="shrink-0">
        {{ $actions }}
      </div>
    @endif
  </div>
</div>
