<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-bold text-slate-800">Users</h1>
    </x-slot>

    <!-- Stats -->
    <div class="grid gap-5 sm:grid-cols-3">
        @foreach ([
            ['Admins', $counts['admin'] ?? 0, '🛡️', 'bg-rose-100 text-rose-600'],
            ['Receptionists', $counts['receptionist'] ?? 0, '🧑‍💼', 'bg-blue-100 text-blue-600'],
            ['Guests', $counts['guest'] ?? 0, '👥', 'bg-emerald-100 text-emerald-600'],
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
        <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <h3 class="text-lg font-bold text-slate-800">បញ្ជីអ្នកប្រើប្រាស់</h3>

            <div class="flex flex-wrap gap-3">
                <form method="GET" action="{{ route('users.index') }}" class="flex gap-2">
                    <select name="role" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                        <option value="">All roles</option>
                        @foreach (\App\Http\Controllers\UserController::ROLES as $r)
                            <option value="{{ $r }}" @selected(request('role') === $r)>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="ស្វែងរកឈ្មោះ / អ៊ីមែល / ទូរស័ព្ទ"
                           class="w-64 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    <button class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Filter</button>
                </form>
                <a href="{{ route('users.create') }}"
                   class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-amber-600/30 hover:bg-amber-700">
                    + Add User
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 text-xs uppercase text-slate-400">
                        <th class="pb-3 font-semibold">User</th>
                        <th class="pb-3 font-semibold">Phone</th>
                        <th class="pb-3 font-semibold">Role</th>
                        <th class="pb-3 font-semibold">Joined</th>
                        <th class="pb-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($users as $u)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-100 font-bold text-amber-700">
                                        {{ strtoupper(mb_substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800">
                                            {{ $u->name }}
                                            @if ($u->id === auth()->id())
                                                <span class="ml-1 rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold text-slate-500">YOU</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-slate-500">{{ $u->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 text-slate-600">{{ $u->phone ?? '—' }}</td>
                            <td class="py-3"><x-role-badge :role="$u->role" /></td>
                            <td class="py-3 text-slate-600">{{ $u->created_at->format('d M Y') }}</td>
                            <td class="py-3">
                                <div class="flex justify-end gap-4">
                                    <a href="{{ route('users.edit', $u) }}" class="font-semibold text-amber-700 hover:underline">Edit</a>
                                    @if ($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $u) }}"
                                              onsubmit="return confirm('លុបអ្នកប្រើប្រាស់នេះមែនទេ?')">
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
                            <td colspan="5" class="py-10 text-center text-slate-400">រកមិនឃើញអ្នកប្រើប្រាស់ទេ។</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $users->links() }}</div>
    </div>
</x-app-layout>