<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $guests = User::where('role', 'guest')
            ->withCount('bookings')
            ->withSum(
                ['bookings as total_spent' => fn ($q) => $q->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])],
                'total_price'
            )
            ->withMax('bookings as last_check_in', 'check_in')
            ->withExists(['bookings as is_staying' => fn ($q) => $q->where('status', 'checked_in')])
            ->when($request->q, function ($query, $s) {
                $query->where(fn ($w) => $w
                    ->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%"));
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $totalGuests = User::where('role', 'guest')->count();
        $newThisMonth = User::where('role', 'guest')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
        $staying = User::where('role', 'guest')
            ->whereHas('bookings', fn ($q) => $q->where('status', 'checked_in'))
            ->count();

        return view('guests.index', compact('guests', 'totalGuests', 'newThisMonth', 'staying'));
    }

    public function create()
    {
        return view('guests.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $guest = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role'     => 'guest',
        ]);
        $guest->forceFill(['email_verified_at' => now()])->save();

        return redirect()->route('guests.show', $guest)->with('success', 'បន្ថែមភ្ញៀវជោគជ័យ');
    }

    public function show(User $guest)
    {
        $this->ensureGuest($guest);

        $bookings = $guest->bookings()->with('room')->latest('check_in')->get();
        $done = $bookings->where('status', 'checked_out');

        $stats = [
            'bookings' => $bookings->count(),
            'stays'    => $done->count(),
            'nights'   => $done->sum(fn ($b) => $b->nights),
            'spent'    => $bookings->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])->sum('total_price'),
        ];

        return view('guests.show', compact('guest', 'bookings', 'stats'));
    }

    public function edit(User $guest)
    {
        $this->ensureGuest($guest);

        return view('guests.edit', compact('guest'));
    }

    public function update(Request $request, User $guest)
    {
        $this->ensureGuest($guest);

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($guest->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $guest->update($data);

        return redirect()->route('guests.show', $guest)->with('success', 'កែប្រែព័ត៌មានភ្ញៀវជោគជ័យ');
    }

    public function destroy(User $guest)
    {
        $this->ensureGuest($guest);

        if ($guest->bookings()->exists()) {
            return back()->with('error', 'មិនអាចលុបភ្ញៀវដែលមានប្រវត្តិកក់បានទេ');
        }

        $guest->delete();

        return redirect()->route('guests.index')->with('success', 'លុបភ្ញៀវជោគជ័យ');
    }

    // ការពារកុំឱ្យកែ/មើលបុគ្គលិកតាមទំព័រ Guests
    private function ensureGuest(User $user): void
    {
        abort_unless($user->role === 'guest', 404);
    }
}