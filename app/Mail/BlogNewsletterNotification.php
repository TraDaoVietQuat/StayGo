<?php

namespace App\Mail;

use App\Models\BlogPost;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BlogNewsletterNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public BlogPost $post) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "📖 Bài mới: {$this->post->title} — StayGo Cẩm Nang",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.blog-newsletter-notification');
    }

    public function attachments(): array { return []; }
}
