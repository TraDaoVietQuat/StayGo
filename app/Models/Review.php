<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'hotel_id', 'user_id', 'booking_id', 'rating', 'comment', 'is_active',
        'partner_reply', 'partner_replied_at',
    ];

    protected $casts = [
        'is_active'          => 'boolean',
        'created_at'         => 'datetime',
        'partner_replied_at' => 'datetime',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
