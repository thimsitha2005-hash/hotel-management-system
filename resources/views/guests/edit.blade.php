<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-bold text-slate-800">Edit Guest</h1>
    </x-slot>

    <div class="max-w-3xl rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-100">
        <form method="POST" action="{{ route('guests.update', $guest) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @include('guests._form', ['guest' => $guest, 'label' => 'Update guest'])
        </form>
    </div>
</x-app-layout>