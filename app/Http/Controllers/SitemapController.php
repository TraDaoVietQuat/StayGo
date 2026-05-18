<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Hotel;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $hotels    = Hotel::select('id', 'created_at')->where('is_active', true)->get();
        $blogPosts = BlogPost::select('id', 'updated_at')->where('is_active', true)->get();

        $xml = response()->view('sitemap', compact('hotels', 'blogPosts'))
            ->header('Content-Type', 'application/xml');

        return $xml;
    }
}
