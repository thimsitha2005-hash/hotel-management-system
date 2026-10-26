<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public const METHODS = [
        'cash' => 'Cash',
        'card' => 'Credit/Debit Card',
        'aba'  => 'ABA Pay',
        'wing' => 'Wing',
        'bank' => 'Bank Transfer',
    ];

    protected $fillable = ['booking_id', 'received_by', 'amount', 'method', 'reference', 'paid_at', 'notes'];

    protected function casts(): array
    {
        return ['paid_at' => 'date'];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}