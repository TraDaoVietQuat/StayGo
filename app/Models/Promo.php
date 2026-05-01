<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Promo extends Model
{
    protected $fillable = [
        'code', 'description', 'type', 'value', 'min_order',
        'max_discount', 'max_uses', 'used_count', 'max_uses_per_user',
        'is_active', 'new_user_only', 'starts_at', 'expires_at',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'new_user_only' => 'boolean',
        'starts_at'     => 'datetime',
        'expires_at'    => 'datetime',
        'value'         => 'float',
        'min_order'     => 'float',
        'max_discount'  => 'float',
    ];

    /**
     * Kiểm tra promo còn hợp lệ không
     */
    public function isValid(float $orderAmount, ?User $user = null): bool
    {
        if (!$this->is_active) return false;

        $now = Carbon::now();
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->expires_at && $now->gt($this->expires_at)) return false;

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) return false;
        if ($orderAmount < $this->min_order) return false;

        if ($user) {
            if ($this->new_user_only && !$user->is_new_user) return false;

            // Kiểm tra số lần user đã dùng promo này
            $timesUsed = Booking::where('user_id', $user->id)
                ->where('discount_code', $this->code)
                ->whereNotIn('status', ['cancelled'])
                ->count();

            if ($timesUsed >= $this->max_uses_per_user) return false;
        }

        return true;
    }

    /**
     * Tính số tiền giảm giá
     */
    public function calculateDiscount(float $amount): float
    {
        $discount = $this->type === 'percent'
            ? $amount * ($this->value / 100)
            : $this->value;

        if ($this->max_discount !== null) {
            $discount = min($discount, $this->max_discount);
        }

        return round($discount, 2);
    }

    /**
     * Tăng used_count
     */
    public function incrementUsed(): void
    {
        $this->increment('used_count');
    }
}
