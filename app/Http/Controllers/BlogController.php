<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Hotel;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::where('is_active', true)->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $posts      = $query->paginate(9)->withQueryString();
        $categories = Cache::remember('blog.categories', 3600, fn() =>
            BlogPost::where('is_active', true)->distinct()->pluck('category')
        );

        return view('pages.blog', compact('posts', 'categories'));
    }

    public function show(BlogPost $blogPost)
    {
        abort_if(!$blogPost->is_active, 404);

        $related = Cache::remember("blog.related.{$blogPost->id}", 3600, fn() =>
            BlogPost::where('category', $blogPost->category)
                ->where('id', '!=', $blogPost->id)
                ->where('is_active', true)
                ->take(3)
                ->get()
        );

        $suggestedHotels = Cache::remember("blog.hotels.{$blogPost->id}", 3600, function () use ($blogPost) {
            $location = Location::where('name', $blogPost->category)->first();
            if (!$location) return collect();
            return Hotel::where('location_id', $location->id)
                ->where('is_active', true)
                ->orderByDesc('rating')
                ->take(4)
                ->get(['id', 'name', 'stars', 'rating', 'review_count', 'price', 'image', 'type']);
        });

        return view('pages.blog-detail', compact('blogPost', 'related', 'suggestedHotels'));
    }
}
