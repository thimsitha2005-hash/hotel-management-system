<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public const ROLES = ['admin', 'receptionist', 'guest'];

    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->role, fn ($q, $role) => $q->where('role', $role))
            ->when($request->q, function ($query, $s) {
                $query->where(fn ($w) => $w
                    ->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%"));
            })
            ->orderByRaw("FIELD(role, 'admin', 'receptionist', 'guest')")
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $counts = User::selectRaw('role, count(*) as total')->groupBy('role')->pluck('total', 'role');

        return view('users.index', compact('users', 'counts'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'role'     => ['required', Rule::in(self::ROLES)],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();

        return redirect()->route('users.index')->with('success', 'បង្កើតអ្នកប្រើប្រាស់ជោគជ័យ');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'    => ['nullable', 'string', 'max:20'],
            'role'     => ['required', Rule::in(self::ROLES)],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        // ការពារ៖ Admin មិនអាចដក Role Admin ពីខ្លួនឯង
        if ($user->id === $request->user()->id && $data['role'] !== 'admin') {
            return back()->withInput()->withErrors(['role' => 'អ្នកមិនអាចដក Role Admin ពីគណនីខ្លួនឯងបានទេ']);
        }

        $user->fill([
            'name'  => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role'  => $data['role'],
        ]);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'កែប្រែអ្នកប្រើប្រាស់ជោគជ័យ');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'អ្នកមិនអាចលុបគណនីខ្លួនឯងបានទេ');
        }

        if ($user->bookings()->exists()) {
            return back()->with('error', 'មិនអាចលុបអ្នកប្រើប្រាស់ដែលមានប្រវត្តិកក់បានទេ');
        }

        $user->delete();

        return back()->with('success', 'លុបអ្នកប្រើប្រាស់ជោគជ័យ');
    }
}