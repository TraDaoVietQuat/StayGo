<?php

namespace App\Console\Commands;

use App\Mail\BlogWeeklyDigest;
use App\Models\BlogPost;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendBlogWeeklyDigest extends Command
{
    protected $signature   = 'staygo:blog-weekly-digest';
    protected $description = 'Gửi email digest bài viết cẩm nang mới trong tuần cho tất cả subscribers';

    public function handle(): int
    {
        $posts = BlogPost::where('is_active', true)
            ->where('created_at', '>=', now()->subWeek())
            ->latest()
            ->take(5)
            ->get();

        if ($posts->isEmpty()) {
            $this->info('Không có bài viết mới trong tuần. Bỏ qua.');
            return self::SUCCESS;
        }

        $subscribers = DB::table('newsletter_subscriptions')->pluck('email');

        if ($subscribers->isEmpty()) {
            $this->info('Không có subscriber nào.');
            return self::SUCCESS;
        }

        $mailable = new BlogWeeklyDigest($posts);
        $sent = 0;

        foreach ($subscribers as $email) {
            try {
                Mail::to($email)->send($mailable);
                $sent++;
            } catch (\Exception $e) {
                Log::warning("BlogWeeklyDigest: failed to send to {$email}: " . $e->getMessage());
                $this->warn("✗ {$email}: " . $e->getMessage());
            }
        }

        $this->info("Blog weekly digest đã gửi {$posts->count()} bài tới {$sent}/{$subscribers->count()} subscribers.");
        return self::SUCCESS;
    }
}
