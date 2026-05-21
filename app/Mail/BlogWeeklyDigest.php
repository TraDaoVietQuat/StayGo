<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BlogWeeklyDigest extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Collection $posts) {}

    public function envelope(): Envelope
    {
        $week = now()->format('d/m/Y');
        return new Envelope(
            subject: "📚 Cẩm Nang Tuần Này ({$week}) — StayGo Journal",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.blog-weekly-digest');
    }

    public function attachments(): array { return []; }
}
