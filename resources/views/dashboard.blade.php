<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-bold text-slate-800">Dashboard</h1>
    </x-slot>

    @php
        $stats = [
            ['label' => 'Total Rooms',           'value' => $totalRooms,                            'icon' => '🛏️', 'color' => 'bg-blue-100 text-blue-600'],
            ['label' => 'Occupied Rooms',        'value' => $roomCounts['occupied'] ?? 0,           'icon' => '🔑', 'color' => 'bg-amber-100 text-amber-600'],
            ['label' => 'Guests In-House',       'value' => $guestsInHouse,                         'icon' => '👥', 'color' => 'bg-emerald-100 text-emerald-600'],
            ['label' => 'Received (This Month)', 'value' => '$' . number_format($monthRevenue, 2),  'icon' => '💰', 'color' => 'bg-rose-100 text-rose-600'],
        ];

        $roomStatus = [
            ['label' => 'Available',   'value' => $roomCounts['available'] ?? 0,   'bar' => 'bg-emerald-500'],
            ['label' => 'Occupied',    'value' => $roomCounts['occupied'] ?? 0,    'bar' => 'bg-amber-500'],
            ['label' => 'Cleaning',    'value' => $roomCounts['cleaning'] ?? 0,    'bar' => 'bg-blue-500'],
            ['label' => 'Maintenance', 'value' => $roomCounts['maintenance'] ?? 0, 'bar' => 'bg-red-500'],
        ];
    @endphp

    <!-- Welcome banner -->
    <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <div class="flex items-center justify-between rounded-2xl bg-white px-6 py-4 shadow-sm ring-1 ring-slate-100">
            <span class="text-sm text-slate-500">💵 Received Today</span>
            <span class="text-xl font-bold text-emerald-700">${{ number_format($todayRevenue, 2) }}</span>
        </div>
        <div class="flex items-center justify-between rounded-2xl bg-white px-6 py-4 shadow-sm ring-1 ring-slate-100">
            <span class="text-sm text-slate-500">⏳ Outstanding Balance</span>
            <span class="text-xl font-bold text-red-600">${{ number_format($outstanding, 2) }}</span>
        </div>
    </div>

    <!-- Stat cards -->
    <div class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $stat)
            <div class="flex items-center justify-between rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100 transition hover:shadow-md">
                <div>
                    <p class="text-sm text-slate-500">{{ $stat['label'] }}</p>
                    <p class="mt-1 text-3xl font-bold text-slate-800">{{ $stat['value'] }}</p>
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-xl text-2xl {{ $stat['color'] }}">
                    {{ $stat['icon'] }}
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">

        <!-- Recent bookings -->
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100 xl:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">Recent Bookings</h3>
                <a href="#" class="text-sm font-semibold text-amber-700 hover:underline">View all</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[560px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs uppercase text-slate-400">
                            <th class="pb-3 font-semibold">Guest</th>
                            <th class="pb-3 font-semibold">Room</th>
                            <th class="pb-3 font-semibold">Check-in</th>
                            <th class="pb-3 font-semibold">Check-out</th>
                            <th class="pb-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse ($recentBookings as $b)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-200 text-xs font-bold text-slate-600">
                                            {{ strtoupper(substr($b->user->name, 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-slate-700">{{ $b->user->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3 text-slate-600">{{ $b->room->room_number }} · {{ $b->room->type }}</td>
                                <td class="py-3 text-slate-600">{{ $b->check_in->format('d M') }}</td>
                                <td class="py-3 text-slate-600">{{ $b->check_out->format('d M') }}</td>
                                <td class="py-3"><x-status-badge :status="$b->status" /></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">មិនទាន់មានការកក់ទេ។</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right column -->
        <div class="space-y-6">

            <!-- Room status -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                <h3 class="mb-4 text-lg font-bold text-slate-800">Room Status</h3>
                @php $roomTotal = max(1, collect($roomStatus)->sum('value')); @endphp
                <div class="space-y-4">
                    @foreach ($roomStatus as $r)
                        <div>
                            <div class="mb-1 flex justify-between text-sm">
                                <span class="text-slate-600">{{ $r['label'] }}</span>
                                <span class="font-semibold text-slate-800">{{ $r['value'] }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100">
                                <div class="h-2 rounded-full {{ $r['bar'] }}"
                                    style="width: {{ round($r['value'] / $roomTotal * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick actions -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                <h3 class="mb-4 text-lg font-bold text-slate-800">Quick Actions</h3>
                <div class="grid grid-cols-2 gap-3 text-sm font-semibold">
                    <a href="{{ route('bookings.create') }}" class="rounded-xl bg-amber-600 px-4 py-3 text-center text-white shadow-md shadow-amber-600/30 transition hover:bg-amber-700">+ New Booking</a>
                    <a href="{{ route('guests.create') }}" class="rounded-xl bg-slate-100 px-4 py-3 text-center text-slate-700 transition hover:bg-slate-200">+ Add Guest</a>
                    <a href="{{ route('rooms.create') }}" class="rounded-xl bg-slate-100 px-4 py-3 text-center text-slate-700 transition hover:bg-slate-200">+ Add Room</a>
                    <a href="{{ route('reports.index') }}" class="rounded-xl bg-slate-100 px-4 py-3 text-center text-slate-700 transition hover:bg-slate-200">📄 Reports</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>