<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 'room_id', 'check_in', 'check_out',
        'guests', 'total_price', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'check_in'  => 'date',
            'check_out' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function getNightsAttribute(): int
    {
        return (int) $this->check_in->diffInDays($this->check_out);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getPaidAttribute(): float
    {
        return (float) $this->payments->sum('amount');
    }

    public function getBalanceAttribute(): float
    {
        return max(0, (float) $this->total_price - $this->paid);
    }

    public function getPaymentStatusAttribute(): string
    {
        if ($this->paid <= 0) return 'unpaid';

        return $this->paid >= (float) $this->total_price ? 'paid' : 'partial';
    }

    public function getDateFlagAttribute(): ?string
    {
        $today = today();

        if ($this->status === 'checked_in') {
            if ($this->check_out->lt($today)) return 'overdue';    // ហួសថ្ងៃ Check-out ហើយ
            if ($this->check_out->eq($today)) return 'due_today';  // Check-out ថ្ងៃនេះ
        }

        if ($this->status === 'confirmed' && $this->check_in->lt($today)) {
            return 'late';                                         // ហួសថ្ងៃ Check-in (No-show?)
        }

        return null;
    }
}