<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    // Staff ឃើញការកក់ទាំងអស់ / Guest ឃើញតែរបស់ខ្លួន
    public function index(Request $request)
    {
        $user = $request->user();

        $bookings = Booking::with(['user', 'room', 'payments'])
            ->when($user->isGuest(), fn ($q) => $q->where('user_id', $user->id))
            ->latest()
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    // ជំហាន 1: ជ្រើសថ្ងៃ → ជំហាន 2: ជ្រើសបន្ទប់ទំនេរ
    public function create(Request $request)
    {
        $user     = $request->user();
        $searched = false;
        $rooms    = collect();
        $nights   = 0;
        $validator = null;

        if ($request->filled('check_in') || $request->filled('check_out')) {
            $validator = Validator::make($request->all(), [
                'check_in'  => ['required', 'date', 'after_or_equal:today'],
                'check_out' => ['required', 'date', 'after:check_in'],
                'guests'    => ['nullable', 'integer', 'min:1'],
            ]);

            if ($validator->passes()) {
                $searched = true;
                $count    = (int) $request->input('guests', 1);

                $rooms = Room::availableBetween($request->check_in, $request->check_out)
                    ->where('capacity', '>=', $count)
                    ->orderBy('price')
                    ->orderBy('room_number')
                    ->get();

                $nights = (int) Carbon::parse($request->check_in)->diffInDays(Carbon::parse($request->check_out));
            }
        }

        $guests = $user->isStaff()
            ? User::where('role', 'guest')->orderBy('name')->get()
            : collect();

        $view = view('bookings.create', compact('rooms', 'guests', 'searched', 'nights'));

        return $validator && $validator->fails() ? $view->withErrors($validator) : $view;
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $rules = [
            'room_id'   => ['required', 'exists:rooms,id'],
            'check_in'  => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests'    => ['required', 'integer', 'min:1'],
            'notes'     => ['nullable', 'string', 'max:500'],
        ];

        if ($user->isStaff()) {
            $rules['user_id'] = ['required', Rule::exists('users', 'id')->where('role', 'guest')];
        }

        $data = $request->validate($rules);
        $room = Room::findOrFail($data['room_id']);

        if ($error = $this->roomProblem($room, $data)) {
            return back()->withInput()->withErrors($error);
        }

        Booking::create([
            'user_id'     => $user->isStaff() ? $data['user_id'] : $user->id,
            'room_id'     => $room->id,
            'check_in'    => $data['check_in'],
            'check_out'   => $data['check_out'],
            'guests'      => $data['guests'],
            'notes'       => $data['notes'] ?? null,
            'total_price' => $room->price * $this->nights($data),
            'status'      => $user->isStaff() ? 'confirmed' : 'pending',
        ]);

        return redirect()->route('bookings.index')->with('success', 'កក់បន្ទប់ជោគជ័យ');
    }

    public function edit(Request $request, Booking $booking)
    {
        $this->authorizeEdit($request, $booking);

        $booking->load(['user', 'payments']);

        $rooms = Room::where('status', '!=', 'maintenance')
            ->orWhere('id', $booking->room_id)
            ->orderBy('room_number')
            ->get();

        return view('bookings.edit', compact('booking', 'rooms'));
    }

    public function update(Request $request, Booking $booking)
    {
        $this->authorizeEdit($request, $booking);

        // ទាមទារថ្ងៃអនាគត តែពេលថ្ងៃ Check-in ត្រូវបានប្តូរ
        $checkInRules = ['required', 'date'];
        if ($request->input('check_in') !== $booking->check_in->toDateString()) {
            $checkInRules[] = 'after_or_equal:today';
        }

        $data = $request->validate([
            'room_id'   => ['required', 'exists:rooms,id'],
            'check_in'  => $checkInRules,
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests'    => ['required', 'integer', 'min:1'],
            'notes'     => ['nullable', 'string', 'max:500'],
        ]);

        $room = Room::findOrFail($data['room_id']);

        if ($error = $this->roomProblem($room, $data, $booking->id)) {
            return back()->withInput()->withErrors($error);
        }

        $total = $room->price * $this->nights($data);
        $paid  = (float) $booking->payments()->sum('amount');

        if ($paid > $total) {
            return back()->withInput()->withErrors([
                'check_out' => 'ប្រាក់ដែលបានបង់ ($' . number_format($paid, 2) . ') លើសតម្លៃថ្មី ($' . number_format($total, 2) . ') សូមដោះស្រាយការទូទាត់ជាមុន',
            ]);
        }

        $booking->update([
            'room_id'     => $room->id,
            'check_in'    => $data['check_in'],
            'check_out'   => $data['check_out'],
            'guests'      => $data['guests'],
            'notes'       => $data['notes'] ?? null,
            'total_price' => $total,
        ]);

        return redirect()->route('bookings.index')->with('success', 'កែប្រែការកក់ជោគជ័យ');
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $user = $request->user();
        $status = $request->validate([
            'status' => ['required', Rule::in(['confirmed', 'checked_in', 'checked_out', 'cancelled'])],
        ])['status'];

        // Guest អាចលុបចោលបានតែការកក់ Pending របស់ខ្លួន
        if ($user->isGuest()) {
            abort_unless(
                $booking->user_id === $user->id && $status === 'cancelled' && $booking->status === 'pending',
                403
            );
        }

        $allowed = [
            'pending'    => ['confirmed', 'cancelled'],
            'confirmed'  => ['checked_in', 'cancelled'],
            'checked_in' => ['checked_out'],
        ];
        abort_unless(in_array($status, $allowed[$booking->status] ?? []), 422);

        $booking->update(['status' => $status]);

        if ($status === 'checked_in') {
            $booking->room->update(['status' => 'occupied']);
        }
        if ($status === 'checked_out') {
            $booking->room->update(['status' => 'cleaning']);
        }

        return back()->with('success', 'ធ្វើបច្ចុប្បន្នភាពស្ថានភាពជោគជ័យ');
    }

    // ---------- Helpers ----------

    private function authorizeEdit(Request $request, Booking $booking): void
    {
        $user = $request->user();

        if ($user->isGuest()) {
            abort_unless($booking->user_id === $user->id && $booking->status === 'pending', 403);
        } else {
            abort_unless(in_array($booking->status, ['pending', 'confirmed']), 403, 'មិនអាចកែការកក់ស្ថានភាពនេះបានទេ');
        }
    }

    private function nights(array $data): int
    {
        return (int) Carbon::parse($data['check_in'])->diffInDays(Carbon::parse($data['check_out']));
    }

    // ពិនិត្យបន្ទប់៖ ជួសជុល / លើសចំនួនមនុស្ស / ជាន់ថ្ងៃ
    private function roomProblem(Room $room, array $data, ?int $ignoreId = null): ?array
    {
        if ($room->status === 'maintenance') {
            return ['room_id' => 'បន្ទប់នេះកំពុងជួសជុល'];
        }

        if ($data['guests'] > $room->capacity) {
            return ['guests' => "បន្ទប់នេះអាចស្នាក់បានអតិបរមា {$room->capacity} នាក់"];
        }

        $free = Room::availableBetween($data['check_in'], $data['check_out'], $ignoreId)
            ->whereKey($room->id)
            ->exists();

        return $free ? null : ['room_id' => 'បន្ទប់នេះត្រូវបានកក់រួចហើយក្នុងថ្ងៃទាំងនោះ'];
    }
}