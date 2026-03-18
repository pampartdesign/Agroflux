@php
    $tenantId = session('tenant_id');
    $user = auth()->user();
@endphp

<header class="sticky top-0 z-10 border-b bg-white/80 backdrop-blur">
    <div class="px-6 py-4 flex items-center justify-between gap-4">
        <div class="min-w-0">
            <div class="text-sm text-slate-500">{{ __('app.welcome_back') }}</div>
            <div class="font-semibold truncate">{{ $user?->name }}</div>
        </div>

        <div class="flex items-center gap-3">
            <div class="text-xs text-slate-500 hidden sm:block">
                @if($tenantId)
                    {{ __('app.tenant_id_label') }} <span class="font-medium text-slate-700">{{ $tenantId }}</span>
                @endif
            </div>

            <a href="/public/market" class="hidden md:inline-flex items-center rounded-lg border px-3 py-2 text-sm hover:bg-slate-50">{{ __('app.nav_marketplace') }}</a>
        </div>
    </div>
</header>
