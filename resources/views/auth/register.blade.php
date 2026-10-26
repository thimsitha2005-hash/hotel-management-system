<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Create account ✨</h2>
        <p class="mt-1 text-sm text-slate-500">Fill in the details below to get started</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Full name -->
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700">Full name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                required autofocus autocomplete="name" placeholder="John Doe"
                class="mt-1 block w-full rounded-lg border border-slate-300 px-4 py-2.5 text-slate-800
                       placeholder-slate-400 shadow-sm transition
                       focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                required autocomplete="username" placeholder="you@example.com"
                class="mt-1 block w-full rounded-lg border border-slate-300 px-4 py-2.5 text-slate-800
                       placeholder-slate-400 shadow-sm transition
                       focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div x-data="{ show: false }">
            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
            <div class="relative mt-1">
                <input id="password" name="password" required autocomplete="new-password"
                    :type="show ? 'text' : 'password'" placeholder="At least 8 characters"
                    class="block w-full rounded-lg border border-slate-300 px-4 py-2.5 pr-16 text-slate-800
                           placeholder-slate-400 shadow-sm transition
                           focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 px-4 text-sm font-medium text-slate-500 hover:text-amber-700"
                    x-text="show ? 'Hide' : 'Show'"></button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm password -->
        <div x-data="{ show: false }">
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm password</label>
            <div class="relative mt-1">
                <input id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                    :type="show ? 'text' : 'password'" placeholder="Repeat your password"
                    class="block w-full rounded-lg border border-slate-300 px-4 py-2.5 pr-16 text-slate-800
                           placeholder-slate-400 shadow-sm transition
                           focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 px-4 text-sm font-medium text-slate-500 hover:text-amber-700"
                    x-text="show ? 'Hide' : 'Show'"></button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Button -->
        <button type="submit"
            class="w-full rounded-lg bg-amber-600 py-3 font-semibold text-white shadow-md shadow-amber-600/30
                   transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
            Register
        </button>

        <p class="text-center text-sm text-slate-500">
            Already have an account?
            <a href="{{ route('login') }}" class="font-semibold text-amber-700 hover:underline">Log in</a>
        </p>
    </form>
</x-guest-layout>