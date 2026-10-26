<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-bold text-slate-800">Reports</h1>
    </x-slot>

    @php
        $input = 'rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30';
        $maxMonth = max(1, $monthly->max('total'));
        $maxDay   = max(1, $daily->max('total'));
        $methodTotal = max(1, $byMethod->sum('total'));
    @endphp

    <style>
        @media print {
            aside, header, .no-print { display: none !important; }
            body { background: #fff !important; }
            main { padding: 0 !important; }
        }
    </style>

    <!-- តម្រង -->
    <form method="GET" action="{{ route('reports.index') }}"
          class="no-print flex flex-wrap items-end gap-4 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
        <div>
            <label class="block text-xs font-semibold uppercase text-slate-400">From</label>
            <input type="date" name="from" value="{{ $from->toDateString() }}" class="{{ $input }}">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase text-slate-400">To</label>
            <input type="date" name="to" value="{{ $to->toDateString() }}" class="{{ $input }}">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase text-slate-400">Year (monthly)</label>
            <input type="number" name="year" value="{{ $year }}" min="2000" max="2100" class="{{ $input }} w-28">
        </div>
        <button class="rounded-lg bg-amber-600 px-5 py-2 text-sm font-semibold text-white shadow-md shadow-amber-600/30 hover:bg-amber-700">Apply</button>

        <div class="flex gap-2 text-xs font-semibold">
            <a href="{{ route('reports.index', ['from' => now()->toDateString(), 'to' => now()->toDateString()]) }}" class="rounded-lg bg-slate-100 px-3 py-2 hover:bg-slate-200">Today</a>
            <a href="{{ route('reports.index', ['from' => now()->startOfMonth()->toDateString(), 'to' => now()->endOfMonth()->toDateString()]) }}" class="rounded-lg bg-slate-100 px-3 py-2 hover:bg-slate-200">This month</a>
            <a href="{{ route('reports.index', ['from' => now()->subMonth()->startOfMonth()->toDateString(), 'to' => now()->subMonth()->endOfMonth()->toDateString()]) }}" class="rounded-lg bg-slate-100 px-3 py-2 hover:bg-slate-200">Last month</a>
        </div>

        <button type="button" onclick="window.print()" class="ml-auto rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">🖨️ Print</button>
    </form>

    <p class="mt-4 text-sm text-slate-500">
        របាយការណ៍ពី <strong>{{ $from->format('d M Y') }}</strong> ដល់ <strong>{{ $to->format('d M Y') }}</strong> ({{ $days }} ថ្ងៃ)
    </p>

    <!-- កាតសង្ខេប -->
    <div class="mt-4 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['Revenue (received)', '$' . number_format($revenue, 2), '💰', 'bg-emerald-100 text-emerald-600'],
            ['Bookings', $bookingsCount, '📅', 'bg-blue-100 text-blue-600'],
            ['Occupancy rate', $occupancyRate . '%', '🛏️', 'bg-amber-100 text-amber-600'],
            ['ADR (avg / night)', '$' . number_format($adr, 2), '📈', 'bg-rose-100 text-rose-600'],
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

    <div class="mt-6 grid gap-6 xl:grid-cols-3">

        <!-- ចំណូលតាមខែ -->
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100 xl:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">ចំណូលតាមខែ {{ $year }}</h3>
                <span class="text-sm font-semibold text-slate-600">Total ${{ number_format($monthly->sum('total'), 2) }}</span>
            </div>

            <div class="flex h-56 items-end gap-2">
                @foreach ($monthly as $m)
                    <div class="flex flex-1 flex-col items-center justify-end gap-1" title="${{ number_format($m['total'], 2) }}">
                        <span class="text-[10px] font-semibold text-slate-500">{{ $m['total'] > 0 ? '$' . number_format($m['total'], 0) : '' }}</span>
                        <div class="w-full rounded-t-md bg-amber-500"
                             style="height: {{ $m['total'] > 0 ? max(3, round($m['total'] / $maxMonth * 170)) : 2 }}px; {{ $m['total'] > 0 ? '' : 'opacity:.25' }}"></div>
                        <span class="text-xs text-slate-500">{{ $m['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- តាមវិធីទូទាត់ -->
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            <h3 class="mb-4 text-lg font-bold text-slate-800">តាមវិធីទូទាត់</h3>
            <div class="space-y-4">
                @forelse ($byMethod as $m)
                    <div>
                        <div class="mb-1 flex justify-between text-sm">
                            <span class="text-slate-600">{{ \App\Models\Payment::METHODS[$m->method] ?? $m->method }} <span class="text-slate-400">({{ $m->count }})</span></span>
                            <span class="font-semibold text-slate-800">${{ number_format($m->total, 2) }}</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100">
                            <div class="h-2 rounded-full bg-amber-500" style="width: {{ round($m->total / $methodTotal * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-slate-400">គ្មានការទូទាត់ក្នុងចន្លោះនេះ។</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">

        <!-- ចំណូលតាមថ្ងៃ -->
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            <h3 class="mb-4 text-lg font-bold text-slate-800">ចំណូលតាមថ្ងៃ</h3>
            <div class="max-h-96 overflow-y-auto">
                <table class="w-full text-left text-sm">
                    <thead class="sticky top-0 bg-white">
                        <tr class="border-b border-slate-100 text-xs uppercase text-slate-400">
                            <th class="pb-3 font-semibold">Date</th>
                            <th class="pb-3 font-semibold">Payments</th>
                            <th class="pb-3 font-semibold"></th>
                            <th class="pb-3 text-right font-semibold">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($daily as $d)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2 text-slate-700">{{ $d['date']->format('D, d M') }}</td>
                                <td class="py-2 text-slate-500">{{ $d['count'] ?: '—' }}</td>
                                <td class="w-1/3 py-2">
                                    <div class="h-2 rounded-full bg-slate-100">
                                        <div class="h-2 rounded-full bg-emerald-500" style="width: {{ round($d['total'] / $maxDay * 100) }}%"></div>
                                    </div>
                                </td>
                                <td class="py-2 text-right font-semibold {{ $d['total'] > 0 ? 'text-emerald-700' : 'text-slate-300' }}">
                                    ${{ number_format($d['total'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-slate-200">
                            <td colspan="3" class="pt-3 font-bold text-slate-800">Total</td>
                            <td class="pt-3 text-right font-bold text-slate-800">${{ number_format($revenue, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Occupancy តាមបន្ទប់ -->
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">អត្រាបន្ទប់ពេញតាមបន្ទប់</h3>
                <span class="text-sm text-slate-500">{{ $nightsTotal }} room-nights</span>
            </div>
            <div class="max-h-96 space-y-4 overflow-y-auto pr-1">
                @forelse ($roomStats as $r)
                    <div>
                        <div class="mb-1 flex justify-between text-sm">
                            <span class="text-slate-600">
                                <strong class="text-slate-800">{{ $r['room']->room_number }}</strong> · {{ $r['room']->type }}
                                <span class="text-slate-400">({{ $r['nights'] }}/{{ $days }} យប់)</span>
                            </span>
                            <span class="font-semibold text-slate-800">{{ $r['occupancy'] }}%</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100">
                            <div class="h-2 rounded-full {{ $r['occupancy'] >= 70 ? 'bg-emerald-500' : ($r['occupancy'] >= 40 ? 'bg-amber-500' : 'bg-red-400') }}"
                                 style="width: {{ $r['occupancy'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-slate-400">មិនទាន់មានបន្ទប់ទេ។</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>