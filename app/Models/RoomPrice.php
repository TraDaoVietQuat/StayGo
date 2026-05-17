<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomPrice extends Model
{
    public $timestamps = false;

    protected $table = 'room_prices';

    protected $fillable = [
        'room_id', 'start_date', 'end_date', 'price', 'label', 'price_type',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'price'      => 'float',
        'created_at' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public static function typeLabels(): array
    {
        return [
            'custom'      => 'Tùy chỉnh',
            'holiday'     => 'Lễ / Tết',
            'weekend'     => 'Cuối tuần',
            'early_bird'  => 'Đặt sớm',
            'last_minute' => 'Đặt muộn',
        ];
    }
}
