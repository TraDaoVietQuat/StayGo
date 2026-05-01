<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportRequest extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'full_name', 'phone', 'email', 'subject',
        'note', 'admin_note', 'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(SupportReply::class)->orderBy('created_at');
    }
}
