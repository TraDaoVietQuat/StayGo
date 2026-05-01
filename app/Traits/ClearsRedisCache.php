<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

trait ClearsRedisCache
{
    protected function forgetMany(array $keys): void
    {
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    // Xóa tất cả Redis keys khớp pattern (prefix tự động thêm vào)
    protected function forgetByPattern(string $pattern): void
    {
        $prefix = config('cache.prefix') . ':';
        $keys   = Redis::keys($prefix . $pattern);
        foreach ($keys as $key) {
            Redis::del($key);
        }
    }
}
