<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Hotel Management') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <style>[x-cloak]{display:none !important;}</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100 text-slate-800">
<div x-data="{ sidebar: false }" class="min-h-screen lg:flex">

    <!-- Overlay សម្រាប់ទូរស័ព្ទ -->
    <div x-show="sidebar" x-cloak @click="sidebar = false"
         class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden"></div>

    <!-- ===== SIDEBAR ===== -->
    <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-40 flex w-64 transform flex-col bg-slate-900 text-slate-300
                  transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0">

        <!-- Logo -->
        <div class="flex items-center gap-3 border-b border-slate-800 px-6 py-5">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-xl">🏨</div>
            <span class="text-lg font-bold text-white">Hotel Management</span>
        </div>

        <!-- Menu -->
        <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-6 text-sm font-medium">
            @php
                $me = Auth::user();

                $menu = collect([
                    ['label' => 'Dashboard',   'icon' => '📊', 'href' => route('dashboard'),       'active' => request()->routeIs('dashboard'),       'roles' => ['admin', 'receptionist']],
                    ['label' => 'Rooms',       'icon' => '🛏️', 'href' => route('rooms.index'),     'active' => request()->routeIs('rooms.*'),         'roles' => ['admin', 'receptionist']],
                    ['label' => $me->isGuest() ? 'My Bookings' : 'Bookings', 'icon' => '📅', 'href' => route('bookings.index'), 'active' => request()->routeIs('bookings.index'), 'roles' => ['admin', 'receptionist', 'guest']],
                    ['label' => 'New Booking', 'icon' => '➕', 'href' => route('bookings.create'), 'active' => request()->routeIs('bookings.create'), 'roles' => ['admin', 'receptionist', 'guest']],
                    ['label' => 'Guests', 'icon' => '👥', 'href' => route('guests.index'), 'active' => request()->routeIs('guests.*'), 'roles' => ['admin', 'receptionist']],
                    ['label' => 'Users', 'icon' => '🛡️', 'href' => route('users.index'), 'active' => request()->routeIs('users.*'), 'roles' => ['admin']],
                    ['label' => 'Payments', 'icon' => '💳', 'href' => route('payments.index'), 'active' => request()->routeIs('payments.*'), 'roles' => ['admin', 'receptionist']],
                    ['label' => 'Reports', 'icon' => '📄', 'href' => route('reports.index'), 'active' => request()->routeIs('reports.*'), 'roles' => ['admin', 'receptionist']],
                    ['label' => 'Profile',     'icon' => '⚙️', 'href' => route('profile.edit'),    'active' => request()->routeIs('profile.*'),       'roles' => ['admin', 'receptionist', 'guest']],
                ])->filter(fn ($item) => in_array($me->role, $item['roles']));
            @endphp

            @foreach ($menu as $item)
                <a href="{{ $item['href'] }}"
                   class="flex items-center gap-3 rounded-lg px-4 py-2.5 transition
                          {{ $item['active']
                              ? 'bg-amber-600 text-white shadow-md shadow-amber-600/30'
                              : 'hover:bg-slate-800 hover:text-white' }}">
                    <span class="text-lg">{{ $item['icon'] }}</span>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <!-- Log out -->
        <div class="border-t border-slate-800 p-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex w-full items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium
                           text-slate-300 transition hover:bg-red-500/10 hover:text-red-400">
                    <span class="text-lg">🚪</span> Log out
                </button>
            </form>
        </div>
    </aside>

    <!-- ===== MAIN ===== -->
    <div class="min-w-0 flex-1">

        <!-- Top bar -->
        <header class="sticky top-0 z-20 flex items-center justify-between border-b border-slate-200 bg-white/90 px-4 py-3 backdrop-blur sm:px-8">
            <div class="flex items-center gap-3">
                <button @click="sidebar = true" class="rounded-lg p-2 text-2xl leading-none hover:bg-slate-100 lg:hidden">☰</button>
                <div>
                    @isset($header)
                        {{ $header }}
                    @endisset
                </div>
            </div>

            <!-- User dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-3 rounded-lg px-2 py-1 hover:bg-slate-100">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-500 font-bold text-white">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden text-left sm:block">
                        <p class="text-sm font-semibold leading-tight">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500">{{ Auth::user()->email }}</p>
                    </div>
                    <span class="text-xs text-slate-400">▼</span>
                </button>

                <div x-show="open" x-cloak @click.outside="open = false"
                     class="absolute right-0 mt-2 w-48 rounded-xl bg-white py-2 text-sm shadow-xl ring-1 ring-slate-100">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-slate-50">👤 Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-2 text-left text-red-600 hover:bg-red-50">🚪 Log out</button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="p-4 sm:p-8">
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-emerald-200">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm font-medium text-red-700 ring-1 ring-red-200">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>