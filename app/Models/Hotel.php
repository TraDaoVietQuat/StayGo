<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name', 'type', 'stars', 'ranking_title', 'address', 'description', 'image', 'cover_position', 'location_id',
        'rating', 'review_text', 'review_count', 'price', 'old_price',
        'checkin_time', 'checkout_time', 'is_active', 'is_weekend_deal',
        'amenities', 'cancellation_policy', 'latitude', 'longitude', 'nearby_places',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'is_weekend_deal'=> 'boolean',
        'rating'         => 'float',
        'amenities'      => 'array',
        'nearby_places'  => 'array',
        'review_count'   => 'string',
    ];

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

    public function getImageUrlAttribute(): string
    {
        return static::resolveImageUrl($this->image);
    }

    public static function resolveImageUrl(?string $image): string
    {
        if (!$image) return asset('assets/images/placeholder.jpg');
        if (str_contains($image, '/')) {
            return \Illuminate\Support\Facades\Storage::url($image);
        }
        return asset('assets/images/' . $image);
    }
}
