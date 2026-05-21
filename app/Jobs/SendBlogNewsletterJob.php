<?php

namespace App\Jobs;

use App\Mail\BlogNewsletterNotification;
use App\Models\BlogPost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendBlogNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(public BlogPost $post) {}

    public function handle(): void
    {
        $subscribers = DB::table('newsletter_subscriptions')->pluck('email');

        if ($subscribers->isEmpty()) return;

        $mailable = new BlogNewsletterNotification($this->post);

        foreach ($subscribers as $email) {
            try {
                Mail::to($email)->send($mailable);
            } catch (\Exception $e) {
                Log::warning("BlogNewsletter: failed to send to {$email}: " . $e->getMessage());
            }
        }

        Log::info("BlogNewsletter: sent new post [{$this->post->title}] to {$subscribers->count()} subscribers.");
    }
}
