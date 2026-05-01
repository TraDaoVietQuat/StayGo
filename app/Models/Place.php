<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'description', 'image', 'location_id'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
