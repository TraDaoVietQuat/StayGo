@if ($paginator->hasPages())
<nav class="sg-pagination">
    {{-- Prev --}}
    @if ($paginator->onFirstPage())
        <span class="sg-page sg-page--disabled">&laquo;</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="sg-page">&laquo;</a>
    @endif

    {{-- Pages --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="sg-page sg-page--dots">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="sg-page sg-page--active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="sg-page">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="sg-page">&raquo;</a>
    @else
        <span class="sg-page sg-page--disabled">&raquo;</span>
    @endif
</nav>
@endif
