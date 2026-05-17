<?php

namespace App\Mail;

use App\Models\SupportRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminChatEscalation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SupportRequest $ticket,
        public string $userMessage
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[StayGo] Chatbot không trả lời được — #' . $this->ticket->id . ' từ ' . $this->ticket->full_name,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-chat-escalation');
    }
}
