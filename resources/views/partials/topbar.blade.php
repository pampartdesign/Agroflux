@php
  $tenant = $tenant ?? app(\App\Services\CurrentTenant::class)->model();
  $gate = app(\App\Services\FeatureGate::class);
  $effectivePlan = $effectivePlan ?? ($tenant ? $gate->effectivePlanKey($tenant) : 'core');
  $trialEndsAt = $tenant?->trial_ends_at ?? null;
@endphp

<header class="sticky top-0 z-10 bg-white/90 backdrop-blur border-b border-slate-100">
  <div class="px-6 py-4 flex items-center justify-between">
    <div class="lg:hidden font-semibold">AgroFlux</div>

    <div class="flex items-center gap-3">
      @if($trialEndsAt)
        <div class="hidden md:flex items-center gap-2 text-xs text-slate-600">
          <span class="inline-flex items-center rounded-full bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-emerald-700">
            Trial ends: {{ \Illuminate\Support\Carbon::parse($trialEndsAt)->toDateString() }}
          </span>
        </div>
      @endif

      <a href="{{ route('public.marketplace') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm hover:bg-slate-50">
        Marketplace
      </a>

      <a href="{{ route('profile.edit') }}" class="text-sm text-slate-700 hover:text-slate-900">Profile</a>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="text-sm text-slate-700 hover:text-slate-900" type="submit">Logout</button>
      </form>
    </div>
  </div>
</header>
