@php
  $tenant = $tenant ?? null;
  $plan = strtoupper($effectivePlan ?? 'CORE');
  $user = auth()->user();
  $isAdmin = $user && method_exists($user, 'hasRole') ? $user->hasRole('Super Admin') || $user->hasRole('Admin') : false;
@endphp

<aside class="w-72 bg-white border-r border-slate-100 hidden lg:block">
  <div class="px-6 py-5 flex items-center gap-3">
    <div class="h-10 w-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-bold">A</div>
    <div>
      <div class="font-semibold leading-5">AgroFlux</div>
      <div class="text-xs text-slate-500">Smart Farm Platform</div>
    </div>
  </div>

  <div class="px-6 pb-3">
    <div class="rounded-2xl bg-emerald-50 border border-emerald-100 px-4 py-3">
      <div class="text-xs text-slate-500">Organization</div>
      <div class="font-semibold truncate">{{ $tenant?->name ?? '—' }}</div>
      <div class="mt-1 inline-flex items-center rounded-full bg-white border border-emerald-200 px-2 py-0.5 text-xs text-emerald-700">
        Plan: {{ $plan }}
      </div>
    </div>
  </div>

  <nav class="px-3 pb-6 space-y-1">
    <div class="px-3 pt-2 pb-1 text-xs font-semibold text-slate-400 uppercase tracking-wide">Core</div>
    <a class="block rounded-xl px-3 py-2 hover:bg-emerald-50 {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-900' : 'text-slate-700' }}"
       href="{{ route('dashboard') }}">Dashboard</a>

    <a class="block rounded-xl px-3 py-2 hover:bg-emerald-50 {{ request()->routeIs('core.farms.*') ? 'bg-emerald-50 text-emerald-900' : 'text-slate-700' }}"
       href="{{ route('core.farms.index') }}">Farm Management</a>

    <a class="block rounded-xl px-3 py-2 hover:bg-emerald-50 {{ request()->routeIs('core.products.*') ? 'bg-emerald-50 text-emerald-900' : 'text-slate-700' }}"
       href="{{ route('core.products.index') }}">Products & Catalog</a>

    <a class="block rounded-xl px-3 py-2 hover:bg-emerald-50 {{ request()->routeIs('core.listings.*') ? 'bg-emerald-50 text-emerald-900' : 'text-slate-700' }}"
       href="{{ route('core.listings.index') }}">Listings</a>

    <a class="block rounded-xl px-3 py-2 hover:bg-emerald-50 {{ request()->routeIs('core.orders.*') ? 'bg-emerald-50 text-emerald-900' : 'text-slate-700' }}"
       href="{{ route('core.orders.index') }}">Orders</a>

    <a class="block rounded-xl px-3 py-2 hover:bg-emerald-50 {{ request()->routeIs('core.traceability.*') ? 'bg-emerald-50 text-emerald-900' : 'text-slate-700' }}"
       href="{{ route('core.traceability.index') }}">Traceability</a>

    <div class="px-3 pt-4 pb-1 text-xs font-semibold text-slate-400 uppercase tracking-wide">AgroFlux Plus</div>
    <a class="block rounded-xl px-3 py-2 hover:bg-emerald-50 {{ request()->routeIs('plus.iot.*') ? 'bg-emerald-50 text-emerald-900' : 'text-slate-700' }}"
       href="{{ route('plus.iot.dashboard') }}">IoT Dashboard</a>

    <a class="block rounded-xl px-3 py-2 hover:bg-emerald-50 {{ request()->routeIs('plus.iot.sensors.*') ? 'bg-emerald-50 text-emerald-900' : 'text-slate-700' }}"
       href="{{ route('plus.iot.sensors.index') }}">IoT Configuration</a>

    <a class="block rounded-xl px-3 py-2 hover:bg-emerald-50 {{ request()->routeIs('plus.iot.simulator') ? 'bg-emerald-50 text-emerald-900' : 'text-slate-700' }}"
       href="{{ route('plus.iot.simulator') }}">IoT Simulator</a>

    <div class="px-3 pt-4 pb-1 text-xs font-semibold text-slate-400 uppercase tracking-wide">LogiTrace</div>
    <a class="block rounded-xl px-3 py-2 hover:bg-emerald-50 {{ request()->routeIs('logi.*') ? 'bg-emerald-50 text-emerald-900' : 'text-slate-700' }}"
       href="{{ route('logi.dashboard') }}">LogiTrace Dashboard</a>

    <div class="px-3 pt-4 pb-1 text-xs font-semibold text-slate-400 uppercase tracking-wide">Organization</div>
    <a class="block rounded-xl px-3 py-2 hover:bg-emerald-50 {{ request()->routeIs('members.*') ? 'bg-emerald-50 text-emerald-900' : 'text-slate-700' }}"
       href="{{ route('members.index') }}">Members</a>

    @if($isAdmin)
      <div class="px-3 pt-4 pb-1 text-xs font-semibold text-slate-400 uppercase tracking-wide">Administration</div>
      <a class="block rounded-xl px-3 py-2 hover:bg-emerald-50 {{ request()->routeIs('admin.languages.*') ? 'bg-emerald-50 text-emerald-900' : 'text-slate-700' }}"
         href="{{ route('admin.languages.index') }}">Languages</a>
      <a class="block rounded-xl px-3 py-2 hover:bg-emerald-50 {{ request()->routeIs('admin.regions.*') ? 'bg-emerald-50 text-emerald-900' : 'text-slate-700' }}"
         href="{{ route('admin.regions.index') }}">Regions</a>
    @endif
  </nav>

  <div class="px-6 py-5 border-t border-slate-100">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-semibold">
        {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
      </div>
      <div class="min-w-0">
        <div class="text-sm font-semibold truncate">{{ $user?->name ?? 'User' }}</div>
        <div class="text-xs text-slate-500 truncate">{{ $user?->email ?? '' }}</div>
      </div>
    </div>
  </div>
</aside>
