<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-bold text-slate-800">Invoice</h1>
    </x-slot>

    <style>
        @media print {
            aside, header, .no-print { display: none !important; }
            body { background: #fff !important; }
            main { padding: 0 !important; }
            #invoice { box-shadow: none !important; border: none !important; }
        }
    </style>

    <div class="no-print mb-4 flex max-w-3xl flex-wrap gap-3">
        <button onclick="window.print()" class="rounded-lg bg-amber-600 px-5 py-2 text-sm font-semibold text-white shadow-md shadow-amber-600/30 hover:bg-amber-700">
            🖨️ Print / Save PDF
        </button>
        @if (auth()->user()->isStaff() && $booking->balance > 0 && $booking->status !== 'cancelled')
            <a href="{{ route('payments.create', $booking) }}" class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700">+ Record payment</a>
        @endif
        <a href="{{ route('bookings.index') }}" class="rounded-lg bg-slate-100 px-5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">← Back</a>
    </div>

    <div id="invoice" class="max-w-3xl rounded-2xl bg-white p-10 shadow-sm ring-1 ring-slate-100">

        <!-- Header -->
        <div class="flex items-start justify-between border-b border-slate-100 pb-6">
            <div>
                <div class="flex items-center gap-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-xl">🏨</div>
                    <span class="text-xl font-bold text-slate-800">Hotel Management</span>
                </div>
                <p class="mt-2 text-sm text-slate-500">Phnom Penh, Cambodia</p>
            </div>
            <div class="text-right">
                <h2 class="text-3xl font-bold tracking-wide text-slate-800">INVOICE</h2>
                <p class="mt-1 text-sm text-slate-500">No. INV-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</p>
                <p class="text-sm text-slate-500">Date: {{ now()->format('d M Y') }}</p>
                <div class="mt-2"><x-status-badge :status="$booking->payment_status" /></div>
            </div>
        </div>

        <!-- Bill to -->
        <div class="grid gap-6 py-6 text-sm sm:grid-cols-2">
            <div>
                <p class="text-xs font-semibold uppercase text-slate-400">Billed to</p>
                <p class="mt-1 text-base font-bold text-slate-800">{{ $booking->user->name }}</p>
                <p class="text-slate-600">{{ $booking->user->email }}</p>
                @if ($booking->user->phone)<p class="text-slate-600">{{ $booking->user->phone }}</p>@endif
            </div>
            <div class="sm:text-right">
                <p class="text-xs font-semibold uppercase text-slate-400">Stay</p>
                <p class="mt-1 text-slate-700">Check-in: <strong>{{ $booking->check_in->format('d M Y') }}</strong></p>
                <p class="text-slate-700">Check-out: <strong>{{ $booking->check_out->format('d M Y') }}</strong></p>
                <p class="text-slate-700">Guests: <strong>{{ $booking->guests }}</strong></p>
            </div>
        </div>

        <!-- Items -->
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-y border-slate-200 bg-slate-50 text-xs uppercase text-slate-500">
                    <th class="px-3 py-3 font-semibold">Description</th>
                    <th class="px-3 py-3 text-right font-semibold">Nights</th>
                    <th class="px-3 py-3 text-right font-semibold">Rate</th>
                    <th class="px-3 py-3 text-right font-semibold">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-slate-100">
                    <td class="px-3 py-4">
                        <p class="font-semibold text-slate-800">Room {{ $booking->room->room_number }} · {{ $booking->room->type }}</p>
                    </td>
                    <td class="px-3 py-4 text-right">{{ $booking->nights }}</td>
                    <td class="px-3 py-4 text-right">${{ number_format($booking->room->price, 2) }}</td>
                    <td class="px-3 py-4 text-right font-semibold">${{ number_format($booking->total_price, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="mt-4 ml-auto w-full max-w-xs space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-slate-500">Total</span><span class="font-semibold">${{ number_format($booking->total_price, 2) }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Paid</span><span class="font-semibold text-emerald-700">− ${{ number_format($booking->paid, 2) }}</span></div>
            <div class="flex justify-between border-t border-slate-200 pt-2 text-lg">
                <span class="font-bold text-slate-800">Balance due</span>
                <span class="font-bold {{ $booking->balance > 0 ? 'text-red-600' : 'text-emerald-700' }}">${{ number_format($booking->balance, 2) }}</span>
            </div>
        </div>

        <!-- Payment history -->
        <div class="mt-8">
            <h3 class="mb-2 text-sm font-bold uppercase text-slate-500">Payment history</h3>
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-xs uppercase text-slate-400">
                        <th class="py-2 font-semibold">Date</th>
                        <th class="py-2 font-semibold">Method</th>
                        <th class="py-2 font-semibold">Reference</th>
                        <th class="py-2 text-right font-semibold">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($booking->payments->sortBy('paid_at') as $p)
                        <tr>
                            <td class="py-2 text-slate-600">{{ $p->paid_at->format('d M Y') }}</td>
                            <td class="py-2 text-slate-600">{{ \App\Models\Payment::METHODS[$p->method] ?? $p->method }}</td>
                            <td class="py-2 text-slate-600">{{ $p->reference ?? '—' }}</td>
                            <td class="py-2 text-right font-semibold">${{ number_format($p->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-4 text-center text-slate-400">No payments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <p class="mt-10 border-t border-slate-100 pt-4 text-center text-xs text-slate-400">
            Thank you for staying with us! · អរគុណដែលបានស្នាក់នៅជាមួយយើង
        </p>
    </div>
</x-app-layout>