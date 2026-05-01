<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportReply extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'support_request_id', 'user_id', 'message', 'is_admin',
    ];

    protected $casts = [
        'is_admin'   => 'boolean',
        'created_at' => 'datetime',
    ];

    public function supportRequest()
    {
        return $this->belongsTo(SupportRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
