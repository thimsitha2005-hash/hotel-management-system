<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    // បញ្ជីការទូទាត់ទាំងអស់
    public function index(Request $request)
    {
        $payments = Payment::with(['booking.user', 'booking.room', 'receiver'])
            ->when($request->q, function ($query, $s) {
                $query->where('reference', 'like', "%{$s}%")
                    ->orWhereHas('booking.user', fn ($u) => $u->where('name', 'like', "%{$s}%"));
            })
            ->latest('paid_at')->latest('id')
            ->paginate(10)
            ->withQueryString();

        $todayTotal = Payment::whereDate('paid_at', today())->sum('amount');
        $monthTotal = Payment::whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)->sum('amount');

        $outstanding = Booking::with('payments')
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->get()
            ->sum(fn ($b) => $b->balance);

        return view('payments.index', compact('payments', 'todayTotal', 'monthTotal', 'outstanding'));
    }

    // ទម្រង់កត់ត្រាការទូទាត់
    public function create(Booking $booking)
    {
        abort_if($booking->status === 'cancelled', 403, 'ការកក់នេះត្រូវបានលុបចោល');

        $booking->load(['user', 'room', 'payments']);

        return view('payments.create', compact('booking'));
    }

    public function store(Request $request, Booking $booking)
    {
        abort_if($booking->status === 'cancelled', 403, 'ការកក់នេះត្រូវបានលុបចោល');

        $booking->load('payments');

        $data = $request->validate([
            'amount'    => ['required', 'numeric', 'min:0.01'],
            'method'    => ['required', Rule::in(array_keys(Payment::METHODS))],
            'reference' => ['nullable', 'string', 'max:100'],
            'paid_at'   => ['required', 'date', 'before_or_equal:today'],
            'notes'     => ['nullable', 'string', 'max:500'],
        ]);

        if ($data['amount'] > $booking->balance) {
            return back()->withInput()->withErrors([
                'amount' => 'ចំនួនទឹកប្រាក់លើសពីសមតុល្យដែលនៅសល់ ($' . number_format($booking->balance, 2) . ')',
            ]);
        }

        $booking->payments()->create($data + ['received_by' => $request->user()->id]);

        return redirect()->route('bookings.invoice', $booking)->with('success', 'កត់ត្រាការទូទាត់ជោគជ័យ');
    }

    // វិក្កយបត្រ
    public function invoice(Request $request, Booking $booking)
    {
        $user = $request->user();

        // Guest មើលបានតែ Invoice របស់ខ្លួន
        abort_if($user->isGuest() && $booking->user_id !== $user->id, 403);

        $booking->load(['user', 'room', 'payments.receiver']);

        return view('payments.invoice', compact('booking'));
    }

    // លុបការទូទាត់ (Admin ប៉ុណ្ណោះ)
    public function destroy(Payment $payment)
    {
        $payment->delete();

        return back()->with('success', 'លុបការទូទាត់ជោគជ័យ');
    }
}