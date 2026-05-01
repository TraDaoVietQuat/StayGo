<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Hotel;
use App\Models\Place;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $hotels    = Hotel::select('id')->get();
        $blogPosts = BlogPost::select('id', 'updated_at')->where('is_active', true)->get();
        $places    = Place::select('id')->get();

        $xml = response()->view('sitemap', compact('hotels', 'blogPosts', 'places'))
            ->header('Content-Type', 'application/xml');

        return $xml;
    }
}
