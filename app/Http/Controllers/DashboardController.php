<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Guest មិនឃើញ Dashboard បុគ្គលិកទេ
        if ($request->user()->isGuest()) {
            return redirect()->route('bookings.index');
        }

        $roomCounts = Room::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalRooms    = $roomCounts->sum();
        $guestsInHouse = Booking::where('status', 'checked_in')->sum('guests');

        // ចំណូលទទួលបានពិត (ពីតារាង payments)
        $monthRevenue = Payment::whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->sum('amount');

        $todayRevenue = Payment::whereDate('paid_at', today())->sum('amount');

        // ប្រាក់ដែលនៅជំពាក់ (ការកក់ដែលមិនទាន់បង់គ្រប់)
        $outstanding = Booking::with('payments')
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->get()
            ->sum(fn ($b) => $b->balance);

        $recentBookings = Booking::with(['user', 'room'])->latest()->take(5)->get();

        return view('dashboard', compact(
            'roomCounts', 'totalRooms', 'guestsInHouse', 'monthRevenue',
            'todayRevenue', 'outstanding', 'recentBookings'
        ));
        
    }
}