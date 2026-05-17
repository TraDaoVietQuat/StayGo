<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelPartnerProfile extends Model
{
    protected $table = 'hotel_partner_profiles';

    protected $fillable = [
        'user_id', 'status', 'commission_rate',
        'business_name', 'contact_name', 'contact_phone', 'tax_code',
        'bank_name', 'bank_account', 'bank_branch', 'bank_owner',
        'notes', 'rejection_reason', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'commission_rate' => 'float',
        'approved_at'     => 'datetime',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function hotel()
    {
        return $this->hasOne(Hotel::class, 'partner_user_id', 'user_id');
    }

    public function payouts()
    {
        return $this->hasMany(PartnerPayout::class, 'partner_user_id', 'user_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public static function statusLabels(): array
    {
        return [
            'pending'   => 'Chờ duyệt',
            'active'    => 'Đang hoạt động',
            'suspended' => 'Tạm đình chỉ',
            'rejected'  => 'Đã từ chối',
        ];
    }
}
