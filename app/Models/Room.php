<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    public const TYPES = ['Standard', 'Deluxe', 'Suite'];
    public const STATUSES = ['available', 'occupied', 'cleaning', 'maintenance'];

    protected $fillable = ['room_number', 'type', 'price', 'capacity', 'status', 'description'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeAvailableBetween($query, $checkIn, $checkOut, $ignoreBookingId = null)
    {
        return $query
            ->where('status', '!=', 'maintenance')
            ->whereDoesntHave('bookings', function ($q) use ($checkIn, $checkOut, $ignoreBookingId) {
                $q->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                ->where('check_in', '<', $checkOut)
                ->where('check_out', '>', $checkIn)
                ->when($ignoreBookingId, fn ($b) => $b->where('id', '!=', $ignoreBookingId));
            });
    }
    
}
