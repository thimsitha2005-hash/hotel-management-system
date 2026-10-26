@props(['role'])

@php
    $map = [
        'admin'        => 'bg-rose-100 text-rose-700',
        'receptionist' => 'bg-blue-100 text-blue-700',
        'guest'        => 'bg-emerald-100 text-emerald-700',
    ];
@endphp

<span class="rounded-full px-3 py-1 text-xs font-semibold {{ $map[$role] ?? 'bg-slate-100 text-slate-600' }}">
    {{ ucfirst($role) }}
</span>