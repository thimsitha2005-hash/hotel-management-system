@props(['status'])

@php
    $map = [
        'pending'     => 'bg-amber-100 text-amber-700',
        'confirmed'   => 'bg-blue-100 text-blue-700',
        'checked_in'  => 'bg-emerald-100 text-emerald-700',
        'checked_out' => 'bg-slate-200 text-slate-700',
        'cancelled'   => 'bg-red-100 text-red-700',
        'available'   => 'bg-emerald-100 text-emerald-700',
        'occupied'    => 'bg-amber-100 text-amber-700',
        'cleaning'    => 'bg-blue-100 text-blue-700',
        'maintenance' => 'bg-red-100 text-red-700',
        'unpaid'  => 'bg-red-100 text-red-700',
        'partial' => 'bg-amber-100 text-amber-700',
        'paid'    => 'bg-emerald-100 text-emerald-700',
        'overdue'   => 'bg-red-100 text-red-700',
        'due_today' => 'bg-amber-100 text-amber-700',
        'late'      => 'bg-amber-100 text-amber-700',
    ];
@endphp

<span class="rounded-full px-3 py-1 text-xs font-semibold {{ $map[$status] ?? 'bg-slate-100 text-slate-600' }}">
    {{ ucfirst(str_replace('_', ' ', $status)) }}
</span>