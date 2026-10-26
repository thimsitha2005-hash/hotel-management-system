<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-bold text-slate-800">Add Room</h1>
    </x-slot>

    <div class="max-w-3xl rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-100">
        <form method="POST" action="{{ route('rooms.store') }}" class="space-y-5">
            @csrf
            @include('rooms._form', [
                'room'  => new \App\Models\Room(['type' => 'Standard', 'status' => 'available', 'capacity' => 2]),
                'label' => 'Save room',
            ])
        </form>
    </div>
</x-app-layout>