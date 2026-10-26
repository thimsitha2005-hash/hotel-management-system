<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date', 'after_or_equal:from'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ]);

        $from = Carbon::parse($data['from'] ?? now()->startOfMonth())->startOfDay();
        $to   = Carbon::parse($data['to'] ?? now()->endOfMonth())->startOfDay();
        $year = (int) ($data['year'] ?? now()->year);

        // កំណត់ចន្លោះអតិបរមា 366 ថ្ងៃ ដើម្បីកុំឱ្យយឺត
        if ($from->diffInDays($to) > 366) {
            $to = $from->copy()->addDays(366);
        }

        // ---------- ចំណូលតាមថ្ងៃ ----------
        $paymentsByDay = Payment::whereBetween('paid_at', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('paid_at, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('paid_at')
            ->get()
            ->keyBy(fn ($r) => Carbon::parse($r->paid_at)->toDateString());

        $daily = collect(CarbonPeriod::create($from, $to))->map(function ($d) use ($paymentsByDay) {
            $row = $paymentsByDay->get($d->toDateString());

            return [
                'date'  => $d->copy(),
                'total' => (float) ($row->total ?? 0),
                'count' => (int) ($row->count ?? 0),
            ];
        });

        $revenue = $daily->sum('total');

        // ---------- ចំណូលតាមខែ ----------
        $paymentsByMonth = Payment::whereYear('paid_at', $year)
            ->selectRaw('MONTH(paid_at) as m, SUM(amount) as total')
            ->groupBy('m')
            ->pluck('total', 'm');

        $monthly = collect(range(1, 12))->map(fn ($m) => [
            'label' => Carbon::create($year, $m, 1)->format('M'),
            'total' => (float) ($paymentsByMonth[$m] ?? 0),
        ]);

        // ---------- តាមវិធីទូទាត់ ----------
        $byMethod = Payment::whereBetween('paid_at', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('method')
            ->orderByDesc('total')
            ->get();

        // ---------- Occupancy ----------
        $rooms = Room::orderBy('room_number')->get();
        $days  = $from->diffInDays($to) + 1;
        $rangeEnd = $to->copy()->addDay(); // check_out ជាថ្ងៃចេញ មិនរាប់យប់នោះ

        $bookings = Booking::whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->where('check_in', '<', $rangeEnd->toDateString())
            ->where('check_out', '>', $from->toDateString())
            ->get();

        $nightsByRoom = [];
        $nightsTotal  = 0;
        $roomRevenue  = 0;

        foreach ($bookings as $b) {
            $start = $b->check_in->greaterThan($from) ? $b->check_in : $from;
            $end   = $b->check_out->lessThan($rangeEnd) ? $b->check_out : $rangeEnd;
            $n     = max(0, (int) $start->diffInDays($end));

            $nightsByRoom[$b->room_id] = ($nightsByRoom[$b->room_id] ?? 0) + $n;
            $nightsTotal += $n;
            $roomRevenue += $n * ($b->nights > 0 ? $b->total_price / $b->nights : 0);
        }

        $roomStats = $rooms->map(function ($room) use ($nightsByRoom, $days) {
            $nights = $nightsByRoom[$room->id] ?? 0;

            return [
                'room'      => $room,
                'nights'    => $nights,
                'occupancy' => $days > 0 ? min(100, round($nights / $days * 100)) : 0,
            ];
        });

        $available     = $rooms->count() * $days;
        $occupancyRate = $available > 0 ? min(100, round($nightsTotal / $available * 100, 1)) : 0;
        $adr           = $nightsTotal > 0 ? $roomRevenue / $nightsTotal : 0;

        return view('reports.index', [
            'from'          => $from,
            'to'            => $to,
            'year'          => $year,
            'daily'         => $daily,
            'monthly'       => $monthly,
            'byMethod'      => $byMethod,
            'roomStats'     => $roomStats,
            'revenue'       => $revenue,
            'bookingsCount' => $bookings->count(),
            'occupancyRate' => $occupancyRate,
            'adr'           => $adr,
            'nightsTotal'   => $nightsTotal,
            'days'          => $days,
        ]);
    }
}