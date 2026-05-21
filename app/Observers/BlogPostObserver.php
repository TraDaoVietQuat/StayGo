<?php

namespace App\Observers;

use App\Jobs\SendBlogNewsletterJob;
use App\Models\BlogPost;
use App\Traits\ClearsRedisCache;

class BlogPostObserver
{
    use ClearsRedisCache;

    public function created(BlogPost $post): void
    {
        $this->clearAll();
        if ($post->is_active) {
            SendBlogNewsletterJob::dispatch($post)->delay(now()->addSeconds(10));
        }
    }

    public function updated(BlogPost $post): void
    {
        $this->clearAll();
        // Gửi newsletter khi bài được publish (inactive → active)
        if ($post->wasChanged('is_active') && $post->is_active) {
            SendBlogNewsletterJob::dispatch($post)->delay(now()->addSeconds(10));
        }
    }

    public function deleted(BlogPost $post): void { $this->clearAll(); }

    private function clearAll(): void
    {
        $this->forgetMany([
            'home.blog_posts',
            'blog.categories',
        ]);

        $this->forgetByPattern('blog.related.*');
    }
}
