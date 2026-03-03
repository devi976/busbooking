<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Booking;

class Bus extends Model
{
    protected $fillable = [
        'bus_name',
        'is_active',
        'from',
        'to',
        'availability_type',
        'available_days',
        'total_seats',
        'available_seats',
        'fare',
        'operator_id',
        'contact_number',
        'current_lat',
        'current_lng',
        'last_location_update',
    ];
    public function fares()
    {
        return $this->hasMany(BusFare::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
    public function stopTimings()
{
    return $this->hasMany(BusStopTiming::class);
}
    public function seats()
{
    return $this->hasMany(Seat::class);
}
public function bookings()
{
    return $this->hasMany(Booking::class);
}

public function seatLocks()
{
    return $this->hasMany(SeatLock::class);
}
}
