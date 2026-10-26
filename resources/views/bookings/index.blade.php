<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-bold text-slate-800">
            {{ auth()->user()->isGuest() ? 'My Bookings' : 'Bookings' }}
        </h1>
    </x-slot>

    @php $isStaff = auth()->user()->isStaff(); @endphp

    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">បញ្ជីការកក់</h3>
            <a href="{{ route('bookings.create') }}"
               class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-amber-600/30 hover:bg-amber-700">
                + New Booking
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 text-xs uppercase text-slate-400">
                        @if ($isStaff) <th class="pb-3 font-semibold">Guest</th> @endif
                        <th class="pb-3 font-semibold">Room</th>
                        <th class="pb-3 font-semibold">Check-in</th>
                        <th class="pb-3 font-semibold">Check-out</th>
                        <th class="pb-3 font-semibold">Nights</th>
                        <th class="pb-3 font-semibold">Total</th>
                        <th class="pb-3 font-semibold">Status</th>
                        <th class="pb-3 font-semibold">Payment</th>
                        <th class="pb-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($bookings as $b)
                        @php
                            $actions = [];
                            $canEdit = in_array($b->status, ['pending', 'confirmed'])
                                && ($isStaff || ($b->user_id === auth()->id() && $b->status === 'pending'));
                            if ($isStaff) {
                                $actions = match ($b->status) {
                                    'pending'    => ['confirmed' => 'Confirm', 'cancelled' => 'Cancel'],
                                    'confirmed'  => ['checked_in' => 'Check-in', 'cancelled' => 'Cancel'],
                                    'checked_in' => ['checked_out' => 'Check-out'],
                                    default      => [],
                                };
                            } elseif ($b->status === 'pending') {
                                $actions = ['cancelled' => 'Cancel'];
                            }
                        @endphp
                        <tr class="hover:bg-slate-50">
                            @if ($isStaff)
                                <td class="py-3 font-medium text-slate-700">{{ $b->user->name }}</td>
                            @endif
                            <td class="py-3 text-slate-600">{{ $b->room->room_number }} · {{ $b->room->type }}</td>
                            <td class="py-3 text-slate-600">{{ $b->check_in->format('d M Y') }}</td>
                            <td class="py-3 text-slate-600">{{ $b->check_out->format('d M Y') }}</td>
                            <td class="py-3 text-slate-600">{{ $b->nights }}</td>
                            <td class="py-3 font-semibold text-slate-800">${{ number_format($b->total_price, 2) }}</td>
                            <td class="py-3">
                                <div class="flex flex-wrap gap-1">
                                    <x-status-badge :status="$b->status" />
                                    @if ($b->date_flag)
                                        <x-status-badge :status="$b->date_flag" />
                                    @endif
                                </div>
                            </td>
                            <td class="py-3"><x-status-badge :status="$b->payment_status" /></td>
                            <td class="py-3">
                                <div class="flex justify-end gap-2">
                                    @if ($canEdit)
                                        <a href="{{ route('bookings.edit', $b) }}"
                                        class="rounded-lg bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-100">Edit</a>
                                    @endif
                                    
                                    <a href="{{ route('bookings.invoice', $b) }}"
                                    class="rounded-lg bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-200">Invoice</a>
                                    
                                    @if ($isStaff && $b->status !== 'cancelled' && $b->balance > 0)
                                        <a href="{{ route('payments.create', $b) }}"
                                        class="rounded-lg bg-emerald-600 px-3 py-1 text-xs font-semibold text-white hover:bg-emerald-700">Pay</a>
                                    @endif
                                    @foreach ($actions as $status => $label)
                                        <form method="POST" action="{{ route('bookings.status', $b) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $status }}">
                                            <button class="rounded-lg px-3 py-1 text-xs font-semibold
                                                {{ $status === 'cancelled'
                                                    ? 'bg-red-50 text-red-600 hover:bg-red-100'
                                                    : 'bg-amber-600 text-white hover:bg-amber-700' }}">
                                                {{ $label }}
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-10 text-center text-slate-400">មិនទាន់មានការកក់ទេ។</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $bookings->links() }}</div>
    </div>
</x-app-layout>