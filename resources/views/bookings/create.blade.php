<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-bold text-slate-800">New Booking</h1>
    </x-slot>

    @php
        $input = 'mt-1 block w-full rounded-lg border border-slate-300 px-4 py-2.5 text-slate-800 shadow-sm transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30';
    @endphp

    <div class="max-w-4xl space-y-6">

        <!-- ជំហាន 1: ជ្រើសថ្ងៃ -->
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            <h3 class="mb-4 text-lg font-bold text-slate-800">1. ជ្រើសថ្ងៃស្នាក់នៅ</h3>

            <form method="GET" action="{{ route('bookings.create') }}" class="grid gap-4 sm:grid-cols-4 sm:items-end">
                @if (request('user_id'))
                    <input type="hidden" name="user_id" value="{{ request('user_id') }}">
                @endif

                <div>
                    <label class="block text-sm font-medium text-slate-700">Check-in</label>
                    <input type="date" name="check_in" min="{{ now()->toDateString() }}" value="{{ request('check_in') }}" required class="{{ $input }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Check-out</label>
                    <input type="date" name="check_out" min="{{ now()->addDay()->toDateString() }}" value="{{ request('check_out') }}" required class="{{ $input }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Guests</label>
                    <input type="number" name="guests" min="1" value="{{ request('guests', 1) }}" required class="{{ $input }}">
                </div>
                <button type="submit" class="rounded-lg bg-slate-800 px-5 py-2.5 font-semibold text-white transition hover:bg-slate-900">
                    🔍 Search rooms
                </button>
            </form>

            <x-input-error :messages="$errors->get('check_in')" class="mt-3" />
            <x-input-error :messages="$errors->get('check_out')" class="mt-1" />
            <x-input-error :messages="$errors->get('guests')" class="mt-1" />
        </div>

        @if (! $searched)
            <div class="rounded-2xl border-2 border-dashed border-slate-200 p-10 text-center text-slate-400">
                ជ្រើសថ្ងៃ Check-in និង Check-out ខាងលើ ដើម្បីមើលបន្ទប់ទំនេរ។
            </div>
        @else
            <!-- ជំហាន 2: ជ្រើសបន្ទប់ -->
            <form method="POST" action="{{ route('bookings.store') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="check_in" value="{{ request('check_in') }}">
                <input type="hidden" name="check_out" value="{{ request('check_out') }}">
                <input type="hidden" name="guests" value="{{ request('guests', 1) }}">

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                        <h3 class="text-lg font-bold text-slate-800">2. ជ្រើសបន្ទប់ទំនេរ</h3>
                        <span class="text-sm text-slate-500">
                            {{ \Carbon\Carbon::parse(request('check_in'))->format('d M') }} → {{ \Carbon\Carbon::parse(request('check_out'))->format('d M Y') }}
                            · {{ $nights }} យប់ · {{ request('guests', 1) }} ភ្ញៀវ
                        </span>
                    </div>

                    @if ($rooms->isEmpty())
                        <p class="py-8 text-center text-slate-400">
                            😔 គ្មានបន្ទប់ទំនេរសម្រាប់ថ្ងៃ ឬចំនួនភ្ញៀវនេះទេ។ សូមសាកថ្ងៃផ្សេង។
                        </p>
                    @else
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach ($rooms as $r)
                                <label class="cursor-pointer">
                                    <input type="radio" name="room_id" value="{{ $r->id }}" required
                                           class="peer sr-only" @checked(old('room_id') == $r->id)>
                                    <div class="rounded-xl border border-slate-200 p-4 transition hover:border-amber-300
                                                peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:ring-2 peer-checked:ring-amber-500/30">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <p class="text-lg font-bold text-slate-800">Room {{ $r->room_number }}</p>
                                                <p class="text-sm text-slate-500">{{ $r->type }} · up to {{ $r->capacity }} guests</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-bold text-amber-700">${{ number_format($r->price, 2) }}</p>
                                                <p class="text-xs text-slate-400">/ night</p>
                                            </div>
                                        </div>
                                        @if ($r->description)
                                            <p class="mt-2 text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($r->description, 80) }}</p>
                                        @endif
                                        <p class="mt-3 border-t border-slate-100 pt-2 text-sm text-slate-600">
                                            {{ $nights }} យប់ · សរុប <strong class="text-slate-800">${{ number_format($r->price * $nights, 2) }}</strong>
                                        </p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    <x-input-error :messages="$errors->get('room_id')" class="mt-3" />
                </div>

                @if ($rooms->isNotEmpty())
                    <div class="space-y-5 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">

                        @if (auth()->user()->isStaff())
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Guest</label>
                                <select name="user_id" required class="{{ $input }}">
                                    <option value="">-- ជ្រើសរើសភ្ញៀវ --</option>
                                    @foreach ($guests as $g)
                                        <option value="{{ $g->id }}" @selected(old('user_id', request('user_id')) == $g->id)>
                                            {{ $g->name }} ({{ $g->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Notes (optional)</label>
                            <textarea name="notes" rows="3" class="{{ $input }}">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="rounded-lg bg-amber-600 px-6 py-2.5 font-semibold text-white shadow-md shadow-amber-600/30 transition hover:bg-amber-700">
                                Book now
                            </button>
                            <a href="{{ route('bookings.index') }}" class="rounded-lg bg-slate-100 px-6 py-2.5 font-semibold text-slate-700 transition hover:bg-slate-200">Cancel</a>
                        </div>
                    </div>
                @endif
            </form>
        @endif
    </div>
</x-app-layout>