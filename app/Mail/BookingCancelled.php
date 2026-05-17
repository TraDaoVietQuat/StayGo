<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public bool  $freeCancellation; // Case A: true, Case B: false
    public float $refundAmount;
    public string $refundEta;

    public function __construct(public Booking $booking)
    {
        // Xác định Case A hay Case B dựa trên chính sách hủy phòng
        $policy  = $booking->room?->hotel?->cancellation_policy ?? '';
        $paid    = $booking->payment?->payment_status === 'completed';
        $hasFree = stripos($policy, 'miễn phí') !== false || stripos($policy, 'free') !== false;

        // Case A: hủy miễn phí hoặc chưa thanh toán
        $this->freeCancellation = $hasFree || !$paid;
        $this->refundAmount = $paid
            ? ($this->freeCancellation ? $booking->total_price : $booking->total_price * 0.8)
            : 0;
        $this->refundEta = '3–5 ngày làm việc';
    }

    public function envelope(): Envelope
    {
        $code = $this->booking->order_code;
        $subject = $this->freeCancellation
            ? "Đã hủy đặt phòng #{$code} — Hoàn tiền " . number_format($this->refundAmount, 0, ',', '.') . 'đ'
            : "Xác nhận hủy phòng #{$code} — Lưu ý chính sách hoàn tiền";
        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-cancelled',
            with: [
                'booking'          => $this->booking,
                'freeCancellation' => $this->freeCancellation,
                'refundAmount'     => $this->refundAmount,
                'refundEta'        => $this->refundEta,
            ],
        );
    }
}
