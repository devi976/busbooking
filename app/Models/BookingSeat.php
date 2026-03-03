<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Booking;

class BookingSeat extends Model
{
    protected $fillable = [
        'booking_id',
        'seat_id'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}