<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomUnavailableDate extends Model
{
    public $timestamps = false;

    protected $table = 'room_unavailable_dates';

    protected $fillable = ['room_id', 'date', 'reason'];

    protected $casts = [
        'date'       => 'date',
        'created_at' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
