<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::orderBy('room_number')->paginate(10);

        return view('rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('rooms.create');
    }

    public function store(Request $request)
    {
        Room::create($this->validated($request));

        return redirect()->route('rooms.index')->with('success', 'បន្ថែមបន្ទប់ជោគជ័យ');
    }

    public function edit(Room $room)
    {
        return view('rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $room->update($this->validated($request, $room));

        return redirect()->route('rooms.index')->with('success', 'កែប្រែបន្ទប់ជោគជ័យ');
    }

    public function destroy(Room $room)
    {
        if ($room->bookings()->exists()) {
            return back()->with('error', 'មិនអាចលុបបន្ទប់ដែលមានប្រវត្តិកក់បានទេ');
        }

        $room->delete();

        return back()->with('success', 'លុបបន្ទប់ជោគជ័យ');
    }

    public function markClean(Room $room)
    {
        if ($room->status !== 'cleaning') {
            return back()->with('error', 'មានតែបន្ទប់ដែលកំពុងសម្អាតទេ ទើបអាចសម្គាល់ថាស្អាតបាន');
        }

        $room->update(['status' => 'available']);

        return back()->with('success', "បន្ទប់ {$room->room_number} ស្អាតរួច ហើយទំនេរសម្រាប់កក់");
    }

    private function validated(Request $request, ?Room $room = null): array
    {
        return $request->validate([
            'room_number' => ['required', 'string', 'max:10', Rule::unique('rooms', 'room_number')->ignore($room?->id)],
            'type'        => ['required', Rule::in(Room::TYPES)],
            'price'       => ['required', 'numeric', 'min:0'],
            'capacity'    => ['required', 'integer', 'min:1', 'max:10'],
            'status'      => ['required', Rule::in(Room::STATUSES)],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
    }
}