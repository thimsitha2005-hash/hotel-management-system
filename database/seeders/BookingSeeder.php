<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        // ការពារកុំឱ្យបង្កើតជាន់ ពេលមានការកក់ស្រាប់
        if (Booking::exists()) {
            $this->command?->warn('មានការកក់រួចហើយ ដូច្នេះរំលង BookingSeeder');
            return;
        }

        $guests = User::where('role', 'guest')->orderBy('id')->get()->values();
        $staff  = User::where('role', 'receptionist')->first() ?? User::where('role', 'admin')->first();

        if ($guests->count() < 5 || ! $staff) {
            $this->command?->warn('ត្រូវការ Guest យ៉ាងតិច 5 និងបុគ្គលិក 1');
            return;
        }

        $today = today();

        $plan = [
            // [guest#, បន្ទប់, in(ថ្ងៃ), out(ថ្ងៃ), ភ្ញៀវ, ស្ថានភាព, សមាមាត្របង់, វិធី, ស្ថានភាពបន្ទប់ក្រោយ]
            [0, '101', -1,  2, 2, 'checked_in',  1.0, 'cash', 'occupied'],
            [1, '201', -2,  1, 2, 'checked_in',  0.5, 'aba',  'occupied'],
            [2, '301',  3,  5, 3, 'confirmed',   0.3, 'wing', null],
            [3, '102',  1,  3, 2, 'pending',     0,   null,   null],
            [4, '103', -6, -4, 2, 'checked_out', 1.0, 'card', 'cleaning'],
            [0, '202', -12, -10, 2, 'checked_out', 1.0, 'cash', null],
            [1, '203',  7,  9, 2, 'cancelled',   0,   null,   null],
            [4, '303', -2, -1, 2, 'confirmed',   1.0, 'cash', null], // ហួសថ្ងៃ Check-in → ស្លាក "Late"
        ];

        foreach ($plan as [$g, $roomNo, $inOff, $outOff, $count, $status, $ratio, $method, $roomAfter]) {
            $room  = Room::where('room_number', $roomNo)->first();
            if (! $room) {
                continue;
            }

            $in     = $today->copy()->addDays($inOff);
            $out    = $today->copy()->addDays($outOff);
            $nights = $outOff - $inOff;
            $total  = $room->price * $nights;

            $booking = Booking::create([
                'user_id'     => $guests[$g]->id,
                'room_id'     => $room->id,
                'check_in'    => $in,
                'check_out'   => $out,
                'guests'      => $count,
                'total_price' => $total,
                'status'      => $status,
            ]);

            if ($ratio > 0 && $method) {
                $booking->payments()->create([
                    'received_by' => $staff->id,
                    'amount'      => round($total * $ratio, 2),
                    'method'      => $method,
                    'reference'   => $method === 'cash' ? null : strtoupper($method) . '-' . random_int(100000, 999999),
                    'paid_at'     => $in->lt($today) ? $in : $today,
                ]);
            }

            if ($roomAfter) {
                $room->update(['status' => $roomAfter]);
            }
        }
    }
}