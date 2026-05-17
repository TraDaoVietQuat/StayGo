<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'hotel_id', 'room_name', 'package_name', 'bed_type', 'price', 'day_price', 'quantity', 'max_guests', 'max_children', 'area', 'is_refundable', 'is_sale', 'is_tax_included', 'cancellation_policy', 'image', 'benefits', 'room_amenities', 'room_notes', 'room_badge',
    ];

    protected $casts = [
        'image'           => 'array',
        'benefits'        => 'array',
        'room_amenities'  => 'array',
        'room_notes'      => 'array',
        'is_tax_included' => 'boolean',
    ];

    /** Số phòng còn trống cho khoảng ngày đã chọn */
    public function availableCount(string $checkIn, string $checkOut): int
    {
        $booked = $this->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->count();

        return max(0, ($this->quantity ?? 1) - $booked);
    }

    /**
     * Kiểm tra + đặt chỗ với DB lock để tránh race condition.
     * Trả về true nếu đặt thành công, false nếu hết phòng.
     * Phải gọi bên trong DB::transaction().
     */
    public function lockAndCheckAvailability(string $checkIn, string $checkOut): bool
    {
        // Dùng lockForUpdate để ngăn concurrent reads trên cùng room
        $room = static::lockForUpdate()->find($this->id);
        if (!$room) return false;

        $booked = $room->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->count();

        return ($room->quantity ?? 1) - $booked > 0;
    }

    /** Trả về mảng tất cả ảnh phòng (cast array tự xử lý JSON, null nếu string không hợp lệ) */
    public function getImagesListAttribute(): array
    {
        $imgs = $this->image; // cast 'array' → array|null
        if (empty($imgs)) return [];
        return array_values(array_filter((array) $imgs));
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function prices()
    {
        return $this->hasMany(RoomPrice::class);
    }

    public function unavailableDates()
    {
        return $this->hasMany(RoomUnavailableDate::class);
    }
}
