<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    public $timestamps = false;
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'hotel_id', 'user_id', 'booking_id', 'rating',
        'cleanliness', 'service_score', 'location_score', 'value_score',
        'comment', 'is_active',
        'partner_reply', 'partner_replied_at',
    ];

    protected $casts = [
        'is_active'          => 'boolean',
        'rating'             => 'float',
        'cleanliness'        => 'float',
        'service_score'      => 'float',
        'location_score'     => 'float',
        'value_score'        => 'float',
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
