<?php

namespace App\Observers;

use App\Models\BlogPost;
use App\Traits\ClearsRedisCache;

class BlogPostObserver
{
    use ClearsRedisCache;

    public function created(BlogPost $post): void { $this->clearAll(); }
    public function updated(BlogPost $post): void { $this->clearAll(); }
    public function deleted(BlogPost $post): void { $this->clearAll(); }

    private function clearAll(): void
    {
        $this->forgetMany([
            'home.blog_posts',
            'blog.categories',
        ]);

        // Xóa cache bài viết liên quan theo pattern (blog.related.{id})
        $this->forgetByPattern('blog.related.*');
    }
}
