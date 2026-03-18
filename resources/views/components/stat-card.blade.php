@props(['label' => '', 'value' => '0', 'hint' => null, 'icon' => null])

<div class="rounded-2xl bg-white border border-slate-100 shadow-sm px-5 py-4">
  <div class="flex items-start justify-between gap-3">
    <div>
      <div class="text-xs text-slate-500">{{ $label }}</div>
      <div class="mt-1 text-2xl font-semibold">{{ $value }}</div>
      @if($hint)
        <div class="mt-1 text-xs text-slate-500">{{ $hint }}</div>
      @endif
    </div>
    <div class="h-10 w-10 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-700">
      {!! $icon ?? '▦' !!}
    </div>
  </div>
</div>
