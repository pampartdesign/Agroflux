@php
    $locales     = config('agroflux.locales', []);
    $currentCode = app()->getLocale();
    $current     = $locales[$currentCode] ?? ['flag' => '🌐', 'label' => strtoupper($currentCode)];
@endphp
<div x-data="{ open: false }" class="relative shrink-0">
    <button @click="open = !open" @click.away="open = false"
            class="inline-flex items-center gap-1.5 h-8 px-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm text-slate-600 font-medium">
        <span>{{ $current['flag'] }}</span>
        <span class="hidden sm:inline text-xs">{{ $current['label'] }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div x-show="open" x-cloak
         class="absolute right-0 mt-1 w-36 bg-white border border-slate-200 rounded-xl shadow-lg py-1 z-50 text-sm">
        @foreach($locales as $code => $loc)
            <a href="{{ route('public.locale.switch', $code) }}"
               class="flex items-center gap-2 px-3 py-1.5 hover:bg-emerald-50 transition
                      {{ $code === $currentCode ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-700' }}">
                <span>{{ $loc['flag'] }}</span>
                <span>{{ $loc['label'] }}</span>
                @if($code === $currentCode)
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-auto text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                @endif
            </a>
        @endforeach
    </div>
</div>
