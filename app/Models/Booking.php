<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BookingSeat;
use App\Models\Bus;

class Booking extends Model
{
    protected $fillable = [
        'bus_id',
        'user_id',
        'name',
        'phone',
        'travel_date',
        'total_seats',
        'total_amount',
        'status',
        'payment_method',
        'entry_point',
        'exit_point'
    ];

    public function seats()
    {
        return $this->hasMany(BookingSeat::class);
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
    public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}
}