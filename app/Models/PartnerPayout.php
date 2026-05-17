<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerPayout extends Model
{
    protected $table = 'partner_payouts';

    protected $fillable = [
        'partner_user_id', 'hotel_id',
        'period_start', 'period_end',
        'gross_revenue', 'commission_rate', 'commission_amount', 'net_amount',
        'booking_count', 'status', 'transfer_ref', 'note',
        'processed_by', 'paid_at', 'email_sent_at',
    ];

    protected $casts = [
        'period_start'      => 'date',
        'period_end'        => 'date',
        'gross_revenue'     => 'float',
        'commission_rate'   => 'float',
        'commission_amount' => 'float',
        'net_amount'        => 'float',
        'booking_count'     => 'integer',
        'paid_at'           => 'datetime',
        'email_sent_at'     => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_user_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public static function statusLabels(): array
    {
        return [
            'pending'    => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'paid'       => 'Đã thanh toán',
            'cancelled'  => 'Đã hủy',
        ];
    }
}
