<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    public $timestamps = true;

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    public const PAYMENT_METHODS = [
        'bank', 'bank_transfer', 'momo', 'vnpay', 'card',
        'hotel', 'zalopay', 'cod', 'vietqr', 'wallet',
    ];

    protected $fillable = [
        'order_code', 'user_id', 'room_id', 'full_name', 'email', 'phone',
        'check_in', 'check_out', 'total_price', 'payment_method', 'note',
        'stay_type', 'discount_code', 'discount_percent', 'discount_amount',
        'status', 'refund_requested', 'refund_requested_at', 'refund_amount', 'created_at',
        'reminder_sent_at', 'reminder_7day_sent_at', 'morning_reminder_sent_at',
        'checkout_reminder_sent_at', 'survey_sent_at', 'survey_reminder_sent_at',
        'payment_status',
        'guest_country', 'guest_country_code', 'guest_city',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'refund_requested' => 'boolean',
        'refund_requested_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'reminder_7day_sent_at' => 'datetime',
        'morning_reminder_sent_at' => 'datetime',
        'checkout_reminder_sent_at' => 'datetime',
        'survey_sent_at' => 'datetime',
        'survey_reminder_sent_at' => 'datetime',
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
