<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'title', 'category', 'summary', 'content', 'thumb', 'img',
        'author', 'tags', 'read_time', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
