<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'partner_user_id',
        'name', 'type', 'stars', 'ranking_title', 'address', 'description', 'image', 'cover_position', 'location_id',
        'rating', 'review_text', 'review_count', 'price', 'old_price',
        'checkin_time', 'checkout_time', 'is_active', 'is_weekend_deal',
        'amenities', 'cancellation_policy', 'refund_policy', 'latitude', 'longitude', 'nearby_places',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'is_weekend_deal'=> 'boolean',
        'rating'         => 'float',
        'amenities'      => 'array',
        'nearby_places'  => 'array',
        'review_count'   => 'string',
    ];

    // Tính % hoàn tiền theo policy và số ngày trước check-in
    public function calculateRefundAmount(float $total, int $daysBeforeCheckIn): float
    {
        return match($this->refund_policy ?? 'moderate') {
            'flexible'       => $daysBeforeCheckIn >= 1 ? $total : $total * 0.5,
            'moderate'       => $daysBeforeCheckIn >= 5 ? $total : ($daysBeforeCheckIn >= 1 ? $total * 0.5 : 0),
            'strict'         => $daysBeforeCheckIn >= 7 ? $total * 0.5 : 0,
            'non_refundable' => 0,
            default          => $total * 0.8,
        };
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function images()
    {
        return $this->hasMany(HotelImage::class)->orderBy('sort_order');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, Room::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function partnerUser()
    {
        return $this->belongsTo(User::class, 'partner_user_id');
    }

    public function payouts()
    {
        return $this->hasMany(PartnerPayout::class);
    }

    public function getImageUrlAttribute(): string
    {
        $path = $this->image ?? 'placeholder.jpg';
        $encoded = implode('/', array_map('rawurlencode', explode('/', $path)));
        return asset('assets/images/' . $encoded);
    }
}
