<x-guest-layout>
    <div class="mb-5">
        <h1 class="text-lg font-semibold text-slate-900">Welcome back</h1>
        <p class="text-sm text-slate-500 mt-0.5">Sign in to your AgroFlux account</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-xs font-medium text-slate-500 mb-1">Email address</label>
            <input id="email" type="email" name="email"
                   value="{{ old('email') }}"
                   required autofocus autocomplete="username"
                   class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 transition @error('email') border-red-300 @enderror"
                   placeholder="you@example.com">
            @error('email')
                <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-xs font-medium text-slate-500 mb-1">Password</label>
            <input id="password" type="password" name="password"
                   required autocomplete="current-password"
                   class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 transition @error('password') border-red-300 @enderror"
                   placeholder="••••••••">
            @error('password')
                <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                <input type="checkbox" name="remember"
                       class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-200">
                Remember me
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-xs text-slate-500 hover:text-emerald-700 transition">
                    Forgot password?
                </a>
            @endif
        </div>

        <button type="submit"
                class="w-full h-10 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition">
            Sign In
        </button>
    </form>
</x-guest-layout>
