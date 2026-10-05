@extends('layouts.public')
@php($item = collect($posts)->firstWhere('slug', $slug))
@section('title', $item['title'] ?? 'Publikasi tidak ditemukan')
@section('content')
@if($item)
    <section class="section detail-section">
        <div class="container">
            <a href="{{ route('publication.index') }}" class="back-link icon-link icon-link-back">
                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
                Kembali ke Publikasi
            </a>
            <div class="detail-grid">
                <article class="article-detail">
                    @if(!empty($item['image']))
                        <div class="article-image-frame">
                            <img class="article-hero" src="{{ $item['image'] }}" alt="{{ $item['title'] }}" data-image-fallback data-fallback-class="article-hero article-image-fallback" data-fallback-text="Gambar publikasi tidak tersedia">
                        </div>
                    @else
                        <div class="article-image-frame article-image-fallback" role="img" aria-label="Gambar publikasi tidak tersedia">Gambar publikasi tidak tersedia</div>
                    @endif
                    <div class="share-row">
                        <b>Bagikan:</b>
                        <div class="share-actions">
                            <a class="share-whatsapp" href="https://wa.me/?text={{ urlencode($item['title'].' '.url()->current()) }}" target="_blank" rel="noopener noreferrer">WhatsApp</a>
                            <a class="share-facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer">Facebook</a>
                            <button class="share-instagram" type="button" data-native-share data-share-title="{{ $item['title'] }}">Instagram</button>
                            <button class="share-copy" type="button" data-copy-url>Copy Link</button>
                        </div>
                    </div>
                    <h1>{{ $item['title'] }}</h1>
                    <p class="article-lead">{{ $item['excerpt'] }}</p>
                    <div class="article-content">{!! nl2br(e($item['content'])) !!}</div>
                </article>
                <aside class="article-sidebar">
                    <section class="sidebar-search">
                        <h2>Pencarian</h2>
                        <label class="sr-only" for="relatedSearch">Cari berita</label>
                        <input id="relatedSearch" type="search" placeholder="Kata kunci ..." autocomplete="off">
                    </section>
                    <section class="related-posts">
                        <h2>Berita Terbaru</h2>
                        <div id="relatedPostList">
                            @foreach(array_slice(array_values(array_filter($posts, fn ($post) => $post['slug'] !== $slug)), 0, 4) as $post)
                                <a data-related-post href="{{ route('publication.show', $post['slug']) }}">
                                    @if(!empty($post['image']))
                                        <img class="latest-news-thumbnail" src="{{ $post['image'] }}" alt="" loading="lazy" data-image-fallback data-fallback-class="latest-news-thumbnail image-fallback" data-fallback-text="Gambar publikasi">
                                    @else
                                        <span class="latest-news-thumbnail image-fallback" role="img" aria-label="Gambar publikasi tidak tersedia">Gambar tidak tersedia</span>
                                    @endif
                                    <span><b>{{ $post['title'] }}</b><small>{{ $post['date'] }}</small></span>
                                </a>
                            @endforeach
                        </div>
                        <p id="relatedEmpty" hidden>Tidak ada berita yang cocok.</p>
                    </section>
                </aside>
            </div>
        </div>
    </section>
@else
    <section class="section container">
        <h1>Publikasi tidak ditemukan</h1>
        <a class="text-link icon-link" href="{{ route('publication.index') }}">Kembali ke daftar <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
    </section>
@endif
@endsection
