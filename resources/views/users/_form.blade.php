@php
    $input = 'mt-1 block w-full rounded-lg border border-slate-300 px-4 py-2.5 text-slate-800 shadow-sm transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30';
    $isEdit = $user->exists;
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700">Full name</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="{{ $input }}">
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="{{ $input }}">
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="012 345 678" class="{{ $input }}">
        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Role</label>
        <select name="role" class="{{ $input }}">
            @foreach (\App\Http\Controllers\UserController::ROLES as $r)
                <option value="{{ $r }}" @selected(old('role', $user->role ?? 'guest') === $r)>{{ ucfirst($r) }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('role')" class="mt-2" />
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">
            {{ $isEdit ? 'New password (optional)' : 'Password' }}
        </label>
        <input type="text" name="password" value="{{ old('password') }}" {{ $isEdit ? '' : 'required' }}
               placeholder="{{ $isEdit ? 'ទុកទទេ បើមិនប្តូរ' : 'យ៉ាងតិច 8 តួអក្សរ' }}" class="{{ $input }}">
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>
</div>

<div class="flex gap-3 pt-2">
    <button type="submit" class="rounded-lg bg-amber-600 px-6 py-2.5 font-semibold text-white shadow-md shadow-amber-600/30 transition hover:bg-amber-700">
        {{ $label }}
    </button>
    <a href="{{ route('users.index') }}" class="rounded-lg bg-slate-100 px-6 py-2.5 font-semibold text-slate-700 transition hover:bg-slate-200">Cancel</a>
</div>