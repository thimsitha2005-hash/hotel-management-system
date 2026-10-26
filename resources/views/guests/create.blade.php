<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-bold text-slate-800">Add Guest</h1>
    </x-slot>

    <div class="max-w-3xl rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-100">
        <form method="POST" action="{{ route('guests.store') }}" class="space-y-5">
            @csrf
            @include('guests._form', ['guest' => new \App\Models\User(), 'label' => 'Save guest'])
        </form>
    </div>
</x-app-layout>