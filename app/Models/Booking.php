<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'order_code', 'user_id', 'room_id', 'full_name', 'email', 'phone',
        'check_in', 'check_out', 'total_price', 'payment_method', 'note',
        'stay_type', 'discount_code', 'discount_percent', 'discount_amount',
        'status', 'refund_requested', 'refund_requested_at', 'refund_amount', 'created_at',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'refund_requested' => 'boolean',
        'refund_requested_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
