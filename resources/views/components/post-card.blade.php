@props(['post'])
<article class="post-card">
    <a href="{{ route('publication.show', $post['slug']) }}" class="post-card-image">
        @if(!empty($post['image']))
            <img src="{{ $post['image'] }}" alt="Dokumentasi: {{ $post['title'] }}" loading="lazy" data-image-fallback data-fallback-class="image-fallback" data-fallback-text="Gambar publikasi tidak tersedia">
        @else
            <span class="image-fallback">Dokumentasi komunitas</span>
        @endif
    </a>
    <div class="post-card-body">
        <p class="card-date">{{ $post['date'] }} &middot; {{ $post['type'] }}</p>
        <h3><a href="{{ route('publication.show', $post['slug']) }}">{{ $post['title'] }}</a></h3>
        <p>{{ $post['excerpt'] }}</p>
        <a class="read-more icon-link" href="{{ route('publication.show', $post['slug']) }}">
            Baca selengkapnya
            <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
    </div>
</article>
