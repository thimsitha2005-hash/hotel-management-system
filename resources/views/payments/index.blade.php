<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-bold text-slate-800">Payments</h1>
    </x-slot>

    <!-- Stats -->
    <div class="grid gap-5 sm:grid-cols-3">
        @foreach ([
            ['Received Today', $todayTotal, '💵', 'bg-emerald-100 text-emerald-600'],
            ['Received This Month', $monthTotal, '💰', 'bg-blue-100 text-blue-600'],
            ['Outstanding Balance', $outstanding, '⏳', 'bg-red-100 text-red-600'],
        ] as [$label, $value, $icon, $color])
            <div class="flex items-center justify-between rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                <div>
                    <p class="text-sm text-slate-500">{{ $label }}</p>
                    <p class="mt-1 text-3xl font-bold text-slate-800">${{ number_format($value, 2) }}</p>
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-xl text-2xl {{ $color }}">{{ $icon }}</div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-lg font-bold text-slate-800">បញ្ជីការទូទាត់</h3>
            <form method="GET" action="{{ route('payments.index') }}" class="flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="ឈ្មោះភ្ញៀវ / លេខយោង"
                       class="w-64 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                <button class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Search</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 text-xs uppercase text-slate-400">
                        <th class="pb-3 font-semibold">Date</th>
                        <th class="pb-3 font-semibold">Guest</th>
                        <th class="pb-3 font-semibold">Room</th>
                        <th class="pb-3 font-semibold">Method</th>
                        <th class="pb-3 font-semibold">Reference</th>
                        <th class="pb-3 font-semibold">Amount</th>
                        <th class="pb-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($payments as $p)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 text-slate-600">{{ $p->paid_at->format('d M Y') }}</td>
                            <td class="py-3 font-medium text-slate-700">{{ $p->booking->user->name }}</td>
                            <td class="py-3 text-slate-600">{{ $p->booking->room->room_number }}</td>
                            <td class="py-3 text-slate-600">{{ \App\Models\Payment::METHODS[$p->method] ?? $p->method }}</td>
                            <td class="py-3 text-slate-600">{{ $p->reference ?? '—' }}</td>
                            <td class="py-3 font-semibold text-emerald-700">${{ number_format($p->amount, 2) }}</td>
                            <td class="py-3">
                                <div class="flex justify-end gap-4">
                                    <a href="{{ route('bookings.invoice', $p->booking) }}" class="font-semibold text-amber-700 hover:underline">Invoice</a>
                                    @if (auth()->user()->isAdmin())
                                        <form method="POST" action="{{ route('payments.destroy', $p) }}"
                                              onsubmit="return confirm('លុបការទូទាត់នេះមែនទេ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="font-semibold text-red-600 hover:underline">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">មិនទាន់មានការទូទាត់ទេ។ ចូល Bookings ហើយចុច "Pay"។</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $payments->links() }}</div>
    </div>
</x-app-layout>