<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'booking_id', 'hotel_id', 'hotel_name', 'room_name', 'method',
        'full_name', 'email', 'phone', 'amount', 'payment_status', 'qr_scanned',
    ];

    protected $casts = [
        'qr_scanned' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
