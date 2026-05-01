<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'description', 'image', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function hotels()
    {
        return $this->hasMany(Hotel::class);
    }

    public function places()
    {
        return $this->hasMany(Place::class);
    }
}
