<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelImage extends Model
{
    public $timestamps = false;

    protected $fillable = ['hotel_id', 'image', 'caption', 'sort_order'];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
