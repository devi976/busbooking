<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeatLock extends Model
{
    protected $fillable = [
        'bus_id',
        'travel_date',
        'seat_number',
        'user_id',
        'expires_at'
    ];
}
