<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    protected $fillable = [
        'booking_id', 'user_id', 'hotel_id', 'support_request_id',
        'type', 'priority', 'status',
        'title', 'description', 'timeline', 'hotel_response', 'evidence',
        'fault_party', 'fault_details',
        'ai_analysis', 'ai_recommendation', 'ai_analyzed_at',
        'verdict', 'verdict_details', 'refund_amount', 'refund_percentage', 'voucher_amount',
        'requires_supervisor', 'supervisor_approved_by', 'supervisor_approved_at',
        'penalty_applied', 'penalty_details',
        'customer_notified_at', 'hotel_notified_at', 'faq_updated',
        'assigned_to', 'resolved_by', 'resolved_at', 'deadline_at',
    ];

    protected $casts = [
        'evidence'               => 'array',
        'requires_supervisor'    => 'boolean',
        'penalty_applied'        => 'boolean',
        'faq_updated'            => 'boolean',
        'refund_amount'          => 'decimal:2',
        'refund_percentage'      => 'decimal:2',
        'voucher_amount'         => 'decimal:2',
        'ai_analyzed_at'         => 'datetime',
        'supervisor_approved_at' => 'datetime',
        'customer_notified_at'   => 'datetime',
        'hotel_notified_at'      => 'datetime',
        'resolved_at'            => 'datetime',
        'deadline_at'            => 'datetime',
    ];

    // -----------------------------------------------------------------------
    // Labels
    // -----------------------------------------------------------------------

    public static function typeLabels(): array
    {
        return [
            'no_show'     => 'A — No-show dispute',
            'overbooking' => 'B — Overbooking',
            'quality'     => 'C — Chất lượng không như mô tả',
            'hidden_fees' => 'D — Phí ẩn',
            'slow_refund' => 'E — Hoàn tiền chậm',
            'misconduct'  => 'F — Hành vi không chuyên nghiệp',
        ];
    }

    public static function typeColors(): array
    {
        return [
            'no_show'     => 'gray',
            'overbooking' => 'danger',
            'quality'     => 'warning',
            'hidden_fees' => 'warning',
            'slow_refund' => 'info',
            'misconduct'  => 'danger',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            'open'             => 'Mới tiếp nhận',
            'investigating'    => 'Đang điều tra',
            'pending_hotel'    => 'Chờ phản hồi KS',
            'pending_customer' => 'Chờ phản hồi KH',
            'resolved'         => 'Đã giải quyết',
            'closed'           => 'Đã đóng',
            'escalated'        => 'Leo thang cấp trên',
        ];
    }

    public static function verdictLabels(): array
    {
        return [
            'refund_full'      => '✅ Hoàn tiền 100%',
            'refund_partial'   => '💰 Hoàn tiền một phần',
            'voucher'          => '🎟️ Cấp voucher bù đắp',
            'rejected'         => '❌ Bác khiếu nại',
            'hotel_compensate' => '🏨 Yêu cầu KS bồi thường',
        ];
    }

    public static function faultLabels(): array
    {
        return [
            'hotel'       => 'Lỗi khách sạn',
            'customer'    => 'Lỗi khách hàng',
            'platform'    => 'Lỗi nền tảng',
            'third_party' => 'Lỗi bên thứ ba',
            'mixed'       => 'Nhiều bên chịu trách nhiệm',
        ];
    }

    public static function aiRecommendationLabels(): array
    {
        return [
            'REFUND_100'       => 'Hoàn 100%',
            'REFUND_PARTIAL'   => 'Hoàn một phần',
            'VOUCHER'          => 'Cấp voucher',
            'REJECT'           => 'Bác khiếu nại',
            'HOTEL_COMPENSATE' => 'KS bồi thường',
            'NEED_MORE_INFO'   => 'Cần thêm thông tin',
        ];
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    public function isUrgent(): bool
    {
        return $this->priority === 'urgent';
    }

    public function isResolved(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    public function isOverdue(): bool
    {
        return $this->deadline_at && $this->deadline_at->isPast() && !$this->isResolved();
    }

    // -----------------------------------------------------------------------
    // Relationships
    // -----------------------------------------------------------------------

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function supportRequest()
    {
        return $this->belongsTo(SupportRequest::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function supervisorApprovedBy()
    {
        return $this->belongsTo(User::class, 'supervisor_approved_by');
    }
}
