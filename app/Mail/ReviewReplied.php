<?php

namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewReplied extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Review $review) {}

    public function envelope(): Envelope
    {
        $hotel = $this->review->hotel?->name ?? 'khách sạn';
        return new Envelope(subject: "[StayGo] 💬 {$hotel} vừa phản hồi đánh giá của bạn");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.review-replied');
    }
}
