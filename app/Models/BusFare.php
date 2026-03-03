<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusFare extends Model
{
    protected $fillable = [
        'bus_id',
        'from_stop',
        'to_stop',
        'fare'
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
}
