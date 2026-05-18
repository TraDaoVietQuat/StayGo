<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

  <url><loc>{{ url('/') }}</loc><changefreq>daily</changefreq><priority>1.0</priority></url>
  <url><loc>{{ url('/hotels') }}</loc><changefreq>daily</changefreq><priority>0.9</priority></url>
  <url><loc>{{ url('/uu-dai') }}</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>
  <url><loc>{{ url('/blog') }}</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>
  <url><loc>{{ url('/lien-he') }}</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>

  @foreach($hotels as $hotel)
  <url>
    <loc>{{ url('/hotels/' . $hotel->id) }}</loc>
    @if($hotel->created_at)
    <lastmod>{{ $hotel->created_at->toAtomString() }}</lastmod>
    @endif
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>
  @endforeach

  @foreach($blogPosts as $post)
  <url>
    <loc>{{ url('/blog/' . $post->id) }}</loc>
    @if($post->updated_at)
    <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
    @endif
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
  @endforeach

</urlset>
