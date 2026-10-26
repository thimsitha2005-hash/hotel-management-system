@php
    $input = 'mt-1 block w-full rounded-lg border border-slate-300 px-4 py-2.5 text-slate-800 shadow-sm transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30';
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-slate-700">Room number</label>
        <input type="text" name="room_number" value="{{ old('room_number', $room->room_number) }}" required class="{{ $input }}">
        <x-input-error :messages="$errors->get('room_number')" class="mt-2" />
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Type</label>
        <select name="type" class="{{ $input }}">
            @foreach (\App\Models\Room::TYPES as $t)
                <option value="{{ $t }}" @selected(old('type', $room->type) === $t)>{{ $t }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('type')" class="mt-2" />
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Price / night ($)</label>
        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $room->price) }}" required class="{{ $input }}">
        <x-input-error :messages="$errors->get('price')" class="mt-2" />
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Capacity (guests)</label>
        <input type="number" min="1" max="10" name="capacity" value="{{ old('capacity', $room->capacity) }}" required class="{{ $input }}">
        <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700">Status</label>
        <select name="status" class="{{ $input }}">
            @foreach (\App\Models\Room::STATUSES as $s)
                <option value="{{ $s }}" @selected(old('status', $room->status) === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700">Description</label>
        <textarea name="description" rows="3" class="{{ $input }}">{{ old('description', $room->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>
</div>

<div class="flex gap-3 pt-2">
    <button type="submit" class="rounded-lg bg-amber-600 px-6 py-2.5 font-semibold text-white shadow-md shadow-amber-600/30 transition hover:bg-amber-700">
        {{ $label }}
    </button>
    <a href="{{ route('rooms.index') }}" class="rounded-lg bg-slate-100 px-6 py-2.5 font-semibold text-slate-700 transition hover:bg-slate-200">Cancel</a>
</div>