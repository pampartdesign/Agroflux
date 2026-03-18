<x-guest-layout>
    <div class="mb-5">
        <h1 class="text-lg font-semibold text-slate-900">Create your account</h1>
        <p class="text-sm text-slate-500 mt-0.5">Start managing your farm with AgroFlux</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-xs font-medium text-slate-500 mb-1">Full name</label>
            <input id="name" type="text" name="name"
                   value="{{ old('name') }}"
                   required autofocus autocomplete="name"
                   class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 transition @error('name') border-red-300 @enderror"
                   placeholder="Nikos Papadopoulos">
            @error('name')
                <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-xs font-medium text-slate-500 mb-1">Email address</label>
            <input id="email" type="email" name="email"
                   value="{{ old('email') }}"
                   required autocomplete="username"
                   class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 transition @error('email') border-red-300 @enderror"
                   placeholder="you@example.com">
            @error('email')
                <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-xs font-medium text-slate-500 mb-1">Password</label>
            <input id="password" type="password" name="password"
                   required autocomplete="new-password"
                   class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 transition @error('password') border-red-300 @enderror"
                   placeholder="Min. 8 characters">
            @error('password')
                <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-medium text-slate-500 mb-1">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   required autocomplete="new-password"
                   class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 transition"
                   placeholder="••••••••">
            @error('password_confirmation')
                <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit"
                class="w-full h-10 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition">
            Create Account
        </button>
    </form>
</x-guest-layout>
