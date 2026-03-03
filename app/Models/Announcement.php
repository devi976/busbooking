<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'message',
        'show_date',
        'start_time',
        'end_time',
        'target_group',
        'target_date',
        'close_bookings'
    ];
}