@props(['book'])
<article class="book-card">
    <a class="book-cover {{ empty($book['cover']) ? 'book-cover-placeholder' : '' }}" href="{{ route('library.show', $book['slug']) }}" aria-label="Detail buku {{ $book['title'] }}">
        @if(!empty($book['cover']))
            <img src="{{ $book['cover'] }}" alt="Sampul {{ $book['title'] }}" loading="lazy" data-image-fallback data-fallback-class="book-image-fallback" data-fallback-text="Sampul {{ $book['title'] }}">
        @else
            <span class="book-placeholder-overline">Koleksi Literasi Tamsel</span>
            <strong>{{ $book['title'] }}</strong>
            <small>{{ $book['author'] }}</small>
        @endif
    </a>
    <div class="book-meta">
        <span>{{ $book['genre'] }}</span>
        <h3><a href="{{ route('library.show', $book['slug']) }}">{{ $book['title'] }}</a></h3>
        <p>{{ $book['author'] }}</p>
        <a class="icon-link" href="{{ route('library.show', $book['slug']) }}">
            Lihat detail
            <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
    </div>
</article>
