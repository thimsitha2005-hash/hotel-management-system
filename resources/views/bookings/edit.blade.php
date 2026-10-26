<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-bold text-slate-800">Edit Booking #{{ $booking->id }}</h1>
    </x-slot>

    @php
        $input = 'mt-1 block w-full rounded-lg border border-slate-300 px-4 py-2.5 text-slate-800 shadow-sm transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30';
    @endphp

    <div class="max-w-3xl rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-100">

        <div class="mb-6 flex flex-wrap items-center gap-x-6 gap-y-2 rounded-lg bg-slate-50 px-4 py-3 text-sm text-slate-600">
            <span>Guest: <strong class="text-slate-800">{{ $booking->user->name }}</strong></span>
            <span class="flex items-center gap-2">Status: <x-status-badge :status="$booking->status" /></span>
            <span>Paid: <strong class="text-emerald-700">${{ number_format($booking->paid, 2) }}</strong></span>
            <span>Current total: <strong class="text-slate-800">${{ number_format($booking->total_price, 2) }}</strong></span>
        </div>

        <form method="POST" action="{{ route('bookings.update', $booking) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-slate-700">Room</label>
                <select name="room_id" required class="{{ $input }}">
                    @foreach ($rooms as $r)
                        <option value="{{ $r->id }}" @selected(old('room_id', $booking->room_id) == $r->id)>
                            {{ $r->room_number }} · {{ $r->type }} · ${{ number_format($r->price, 2) }}/night · up to {{ $r->capacity }} guests
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('room_id')" class="mt-2" />
            </div>

            <div class="grid gap-5 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Check-in</label>
                    <input type="date" name="check_in" value="{{ old('check_in', $booking->check_in->toDateString()) }}" required class="{{ $input }}">
                    <x-input-error :messages="$errors->get('check_in')" class="mt-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Check-out</label>
                    <input type="date" name="check_out" value="{{ old('check_out', $booking->check_out->toDateString()) }}" required class="{{ $input }}">
                    <x-input-error :messages="$errors->get('check_out')" class="mt-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Guests</label>
                    <input type="number" name="guests" min="1" value="{{ old('guests', $booking->guests) }}" required class="{{ $input }}">
                    <x-input-error :messages="$errors->get('guests')" class="mt-2" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Notes (optional)</label>
                <textarea name="notes" rows="3" class="{{ $input }}">{{ old('notes', $booking->notes) }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>

            <p class="text-xs text-slate-500">ប្រព័ន្ធពិនិត្យថ្ងៃជាន់គ្នា និងគណនាតម្លៃសរុបឡើងវិញដោយស្វ័យប្រវត្តិ ពេលចុច Save។</p>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-lg bg-amber-600 px-6 py-2.5 font-semibold text-white shadow-md shadow-amber-600/30 transition hover:bg-amber-700">Save changes</button>
                <a href="{{ route('bookings.index') }}" class="rounded-lg bg-slate-100 px-6 py-2.5 font-semibold text-slate-700 transition hover:bg-slate-200">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>