<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'description', 'image'];

    public function hotels()
    {
        return $this->hasMany(Hotel::class);
    }

    public function places()
    {
        return $this->hasMany(Place::class);
    }
}
