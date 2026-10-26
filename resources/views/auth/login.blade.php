<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Welcome back 👋</h2>
        <p class="mt-1 text-sm text-slate-500">Please log in to your account</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                required autofocus autocomplete="username" placeholder="you@example.com"
                class="mt-1 block w-full rounded-lg border border-slate-300 px-4 py-2.5 text-slate-800
                       placeholder-slate-400 shadow-sm transition
                       focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div x-data="{ show: false }">
            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
            <div class="relative mt-1">
                <input id="password" name="password" required autocomplete="current-password"
                    :type="show ? 'text' : 'password'" placeholder="••••••••"
                    class="block w-full rounded-lg border border-slate-300 px-4 py-2.5 pr-16 text-slate-800
                           placeholder-slate-400 shadow-sm transition
                           focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 px-4 text-sm font-medium text-slate-500 hover:text-amber-700"
                    x-text="show ? 'Hide' : 'Show'"></button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember + Forgot -->
        <div class="flex items-center justify-between">
            <label class="flex items-center text-sm text-slate-600">
                <input type="checkbox" name="remember"
                    class="mr-2 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                Remember me
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm font-medium text-amber-700 hover:underline">
                    Forgot password?
                </a>
            @endif
        </div>

        <!-- Button -->
        <button type="submit"
            class="w-full rounded-lg bg-amber-600 py-3 font-semibold text-white shadow-md shadow-amber-600/30
                   transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
            Log in
        </button>

        <!-- Divider -->
        <div class="flex items-center gap-3 text-xs text-slate-400">
            <div class="h-px flex-1 bg-slate-200"></div>
            <span>OR</span>
            <div class="h-px flex-1 bg-slate-200"></div>
        </div>

        <!-- Register -->
        <p class="text-center text-sm text-slate-500">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-semibold text-amber-700 hover:underline">Register now</a>
        </p>
    </form>
</x-guest-layout>