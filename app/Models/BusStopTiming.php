<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusStopTiming extends Model
{
    protected $fillable = [
        'bus_id',
        'stop_name',
        'arrival_time'
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
}

