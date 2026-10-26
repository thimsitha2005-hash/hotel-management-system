<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-bold text-slate-800">Record Payment</h1>
    </x-slot>

    @php
        $input = 'mt-1 block w-full rounded-lg border border-slate-300 px-4 py-2.5 text-slate-800 shadow-sm transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30';
    @endphp

    <div class="grid max-w-4xl gap-6 lg:grid-cols-3">

        <!-- សង្ខេបការកក់ -->
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100 lg:col-span-1">
            <h3 class="mb-4 text-lg font-bold text-slate-800">Booking #{{ $booking->id }}</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Guest</dt><dd class="font-medium">{{ $booking->user->name }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Room</dt><dd class="font-medium">{{ $booking->room->room_number }} · {{ $booking->room->type }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Stay</dt><dd class="font-medium">{{ $booking->check_in->format('d M') }} → {{ $booking->check_out->format('d M') }}</dd></div>
                <hr class="border-slate-100">
                <div class="flex justify-between"><dt class="text-slate-500">Total</dt><dd class="font-semibold">${{ number_format($booking->total_price, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Paid</dt><dd class="font-semibold text-emerald-700">${{ number_format($booking->paid, 2) }}</dd></div>
                <div class="flex justify-between text-base"><dt class="font-semibold text-slate-700">Balance</dt><dd class="font-bold text-red-600">${{ number_format($booking->balance, 2) }}</dd></div>
            </dl>
        </div>

        <!-- ទម្រង់ -->
        <div class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-100 lg:col-span-2">
            @if ($booking->balance <= 0)
                <div class="rounded-lg bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-emerald-200">
                    ✅ ការកក់នេះបានបង់គ្រប់ចំនួនរួចហើយ។
                </div>
                <a href="{{ route('bookings.invoice', $booking) }}" class="mt-4 inline-block font-semibold text-amber-700 hover:underline">មើល Invoice →</a>
            @else
                <form method="POST" action="{{ route('payments.store', $booking) }}" class="space-y-5">
                    @csrf

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Amount ($)</label>
                            <input type="number" step="0.01" min="0.01" max="{{ $booking->balance }}" name="amount"
                                   value="{{ old('amount', $booking->balance) }}" required class="{{ $input }}">
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Method</label>
                            <select name="method" class="{{ $input }}">
                                @foreach (\App\Models\Payment::METHODS as $key => $label)
                                    <option value="{{ $key }}" @selected(old('method', 'cash') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('method')" class="mt-2" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Payment date</label>
                            <input type="date" name="paid_at" max="{{ now()->toDateString() }}"
                                   value="{{ old('paid_at', now()->toDateString()) }}" required class="{{ $input }}">
                            <x-input-error :messages="$errors->get('paid_at')" class="mt-2" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Reference (optional)</label>
                            <input type="text" name="reference" value="{{ old('reference') }}" placeholder="លេខប្រតិបត្តិការ" class="{{ $input }}">
                            <x-input-error :messages="$errors->get('reference')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Notes (optional)</label>
                        <textarea name="notes" rows="2" class="{{ $input }}">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="rounded-lg bg-amber-600 px-6 py-2.5 font-semibold text-white shadow-md shadow-amber-600/30 transition hover:bg-amber-700">
                            Save payment
                        </button>
                        <a href="{{ route('bookings.index') }}" class="rounded-lg bg-slate-100 px-6 py-2.5 font-semibold text-slate-700 transition hover:bg-slate-200">Cancel</a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>