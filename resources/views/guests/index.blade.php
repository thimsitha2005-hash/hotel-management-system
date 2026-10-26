<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-bold text-slate-800">Guests</h1>
    </x-slot>

    <!-- Stats -->
    <div class="grid gap-5 sm:grid-cols-3">
        @foreach ([
            ['Total Guests', $totalGuests, '👥', 'bg-blue-100 text-blue-600'],
            ['Staying Now', $staying, '🔑', 'bg-emerald-100 text-emerald-600'],
            ['New This Month', $newThisMonth, '✨', 'bg-amber-100 text-amber-600'],
        ] as [$label, $value, $icon, $color])
            <div class="flex items-center justify-between rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                <div>
                    <p class="text-sm text-slate-500">{{ $label }}</p>
                    <p class="mt-1 text-3xl font-bold text-slate-800">{{ $value }}</p>
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-xl text-2xl {{ $color }}">{{ $icon }}</div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-lg font-bold text-slate-800">បញ្ជីភ្ញៀវ</h3>

            <div class="flex gap-3">
                <form method="GET" action="{{ route('guests.index') }}" class="flex gap-2">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="ស្វែងរកឈ្មោះ / អ៊ីមែល / ទូរស័ព្ទ"
                           class="w-64 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    <button class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Search</button>
                </form>
                <a href="{{ route('guests.create') }}"
                   class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-amber-600/30 hover:bg-amber-700">
                    + Add Guest
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 text-xs uppercase text-slate-400">
                        <th class="pb-3 font-semibold">Guest</th>
                        <th class="pb-3 font-semibold">Phone</th>
                        <th class="pb-3 font-semibold">Bookings</th>
                        <th class="pb-3 font-semibold">Total spent</th>
                        <th class="pb-3 font-semibold">Last stay</th>
                        <th class="pb-3 font-semibold">Status</th>
                        <th class="pb-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($guests as $g)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-100 font-bold text-amber-700">
                                        {{ strtoupper(mb_substr($g->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $g->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $g->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 text-slate-600">{{ $g->phone ?? '—' }}</td>
                            <td class="py-3 text-slate-600">{{ $g->bookings_count }}</td>
                            <td class="py-3 font-semibold text-slate-800">${{ number_format($g->total_spent ?? 0, 2) }}</td>
                            <td class="py-3 text-slate-600">
                                {{ $g->last_check_in ? \Carbon\Carbon::parse($g->last_check_in)->format('d M Y') : '—' }}
                            </td>
                            <td class="py-3">
                                @if ($g->is_staying)
                                    <x-status-badge status="checked_in" />
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="flex justify-end gap-4">
                                    <a href="{{ route('guests.show', $g) }}" class="font-semibold text-amber-700 hover:underline">View</a>
                                    <a href="{{ route('guests.edit', $g) }}" class="font-semibold text-slate-600 hover:underline">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">រកមិនឃើញភ្ញៀវទេ។</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $guests->links() }}</div>
    </div>
</x-app-layout>