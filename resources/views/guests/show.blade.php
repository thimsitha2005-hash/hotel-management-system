<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-bold text-slate-800">Guest Profile</h1>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-3">

        <!-- Profile -->
        <div class="space-y-6">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                <div class="flex flex-col items-center text-center">
                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-amber-500 text-3xl font-bold text-white">
                        {{ strtoupper(mb_substr($guest->name, 0, 1)) }}
                    </div>
                    <h2 class="mt-3 text-xl font-bold text-slate-800">{{ $guest->name }}</h2>
                    <p class="text-sm text-slate-500">Guest since {{ $guest->created_at->format('d M Y') }}</p>
                </div>

                <dl class="mt-6 space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">📧 Email</dt><dd class="font-medium text-slate-800">{{ $guest->email }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">📞 Phone</dt><dd class="font-medium text-slate-800">{{ $guest->phone ?? '—' }}</dd></div>
                </dl>

                <div class="mt-6 grid grid-cols-2 gap-3 text-sm font-semibold">
                    <a href="{{ route('bookings.create', ['user_id' => $guest->id]) }}"
                       class="rounded-xl bg-amber-600 px-4 py-2.5 text-center text-white shadow-md shadow-amber-600/30 hover:bg-amber-700">+ New Booking</a>
                    <a href="{{ route('guests.edit', $guest) }}"
                       class="rounded-xl bg-slate-100 px-4 py-2.5 text-center text-slate-700 hover:bg-slate-200">Edit</a>
                </div>

                @if (auth()->user()->isAdmin())
                    <form method="POST" action="{{ route('guests.destroy', $guest) }}" class="mt-3"
                          onsubmit="return confirm('លុបភ្ញៀវនេះមែនទេ?')">
                        @csrf
                        @method('DELETE')
                        <button class="w-full rounded-xl bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-100">Delete guest</button>
                    </form>
                @endif
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 gap-4">
                @foreach ([
                    ['Bookings', $stats['bookings']],
                    ['Completed stays', $stats['stays']],
                    ['Total nights', $stats['nights']],
                    ['Total spent', '$' . number_format($stats['spent'], 2)],
                ] as [$label, $value])
                    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
                        <p class="text-xs text-slate-500">{{ $label }}</p>
                        <p class="mt-1 text-xl font-bold text-slate-800">{{ $value }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Stay history -->
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100 xl:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">ប្រវត្តិស្នាក់នៅ</h3>
                <a href="{{ route('guests.index') }}" class="text-sm font-semibold text-amber-700 hover:underline">← Back to guests</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[620px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs uppercase text-slate-400">
                            <th class="pb-3 font-semibold">Room</th>
                            <th class="pb-3 font-semibold">Check-in</th>
                            <th class="pb-3 font-semibold">Check-out</th>
                            <th class="pb-3 font-semibold">Nights</th>
                            <th class="pb-3 font-semibold">Total</th>
                            <th class="pb-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse ($bookings as $b)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 font-medium text-slate-700">{{ $b->room->room_number }} · {{ $b->room->type }}</td>
                                <td class="py-3 text-slate-600">{{ $b->check_in->format('d M Y') }}</td>
                                <td class="py-3 text-slate-600">{{ $b->check_out->format('d M Y') }}</td>
                                <td class="py-3 text-slate-600">{{ $b->nights }}</td>
                                <td class="py-3 font-semibold text-slate-800">${{ number_format($b->total_price, 2) }}</td>
                                <td class="py-3"><x-status-badge :status="$b->status" /></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-slate-400">ភ្ញៀវនេះមិនទាន់មានប្រវត្តិកក់ទេ។</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>