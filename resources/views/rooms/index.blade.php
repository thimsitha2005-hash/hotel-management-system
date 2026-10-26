<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-bold text-slate-800">Rooms</h1>
    </x-slot>

    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">បញ្ជីបន្ទប់</h3>
            <a href="{{ route('rooms.create') }}"
               class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-amber-600/30 hover:bg-amber-700">
                + Add Room
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 text-xs uppercase text-slate-400">
                        <th class="pb-3 font-semibold">Room</th>
                        <th class="pb-3 font-semibold">Type</th>
                        <th class="pb-3 font-semibold">Price / night</th>
                        <th class="pb-3 font-semibold">Capacity</th>
                        <th class="pb-3 font-semibold">Status</th>
                        <th class="pb-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($rooms as $room)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 font-semibold text-slate-800">{{ $room->room_number }}</td>
                            <td class="py-3 text-slate-600">{{ $room->type }}</td>
                            <td class="py-3 text-slate-600">${{ number_format($room->price, 2) }}</td>
                            <td class="py-3 text-slate-600">{{ $room->capacity }} 👤</td>
                            <td class="py-3"><x-status-badge :status="$room->status" /></td>
                            <td class="py-3">
                                <div class="flex justify-end gap-4">
                                    @if ($room->status === 'cleaning')
                                        <form method="POST" action="{{ route('rooms.clean', $room) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="rounded-lg bg-emerald-600 px-3 py-1 text-xs font-semibold text-white hover:bg-emerald-700">
                                                ✨ Mark as clean
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <a href="{{ route('rooms.edit', $room) }}" class="font-semibold text-amber-700 hover:underline">Edit</a>
                                    
                                    @if (auth()->user()->isAdmin())
                                        <form method="POST" action="{{ route('rooms.destroy', $room) }}"
                                              onsubmit="return confirm('លុបបន្ទប់នេះមែនទេ?')">
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
                            <td colspan="6" class="py-10 text-center text-slate-400">មិនទាន់មានបន្ទប់ទេ។ ចុច "+ Add Room" ដើម្បីបន្ថែម។</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $rooms->links() }}</div>
    </div>
</x-app-layout>